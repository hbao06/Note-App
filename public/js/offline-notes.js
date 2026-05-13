const DB_NAME = "my-notes-offline-db";
const DB_VERSION = 1;

const NOTES_STORE = "notes";
const OUTBOX_STORE = "outbox";

function openOfflineDb() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onerror = () => reject(request.error);

        request.onsuccess = () => resolve(request.result);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;

            if (!db.objectStoreNames.contains(NOTES_STORE)) {
                const notesStore = db.createObjectStore(NOTES_STORE, {
                    keyPath: "local_key",
                });

                notesStore.createIndex("server_id", "id", { unique: false });
                notesStore.createIndex("client_id", "client_id", {
                    unique: false,
                });
                notesStore.createIndex("updated_at", "updated_at", {
                    unique: false,
                });
            }

            if (!db.objectStoreNames.contains(OUTBOX_STORE)) {
                const outboxStore = db.createObjectStore(OUTBOX_STORE, {
                    keyPath: "outbox_id",
                    autoIncrement: true,
                });

                outboxStore.createIndex("created_at", "created_at", {
                    unique: false,
                });
            }
        };
    });
}

async function getStore(storeName, mode = "readonly") {
    const db = await openOfflineDb();
    const transaction = db.transaction(storeName, mode);

    return transaction.objectStore(storeName);
}

function requestToPromise(request) {
    return new Promise((resolve, reject) => {
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

function makeClientId() {
    if (window.crypto && crypto.randomUUID) {
        return crypto.randomUUID();
    }

    return `client-${Date.now()}-${Math.random().toString(16).slice(2)}`;
}

function makeLocalKey(note) {
    if (note.local_key) return note.local_key;
    if (note.id) return `server-${note.id}`;
    return `client-${note.client_id}`;
}

function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');

    return token ? token.getAttribute("content") : "";
}

function isNotesPage() {
    return window.location.pathname.startsWith("/notes");
}

function getTitleInput() {
    return document.querySelector("[data-offline-note-title]");
}

function getContentInput() {
    return document.querySelector("[data-offline-note-content]");
}

function getCurrentNoteIdInput() {
    return document.querySelector("[data-offline-note-id]");
}

function showOfflineBanner(message) {
    let banner = document.querySelector("[data-offline-banner]");

    if (!banner) {
        banner = document.createElement("div");
        banner.setAttribute("data-offline-banner", "true");
        banner.style.position = "fixed";
        banner.style.left = "16px";
        banner.style.right = "16px";
        banner.style.bottom = "16px";
        banner.style.zIndex = "9999";
        banner.style.padding = "12px 16px";
        banner.style.borderRadius = "12px";
        banner.style.background = "#111827";
        banner.style.color = "#ffffff";
        banner.style.fontSize = "14px";
        banner.style.boxShadow = "0 10px 25px rgba(0, 0, 0, 0.2)";
        document.body.appendChild(banner);
    }

    banner.textContent = message;
}

function hideOfflineBanner() {
    const banner = document.querySelector("[data-offline-banner]");

    if (banner) {
        banner.remove();
    }
}

async function saveNoteToIndexedDb(note) {
    const store = await getStore(NOTES_STORE, "readwrite");

    const normalizedNote = {
        id: note.id || null,
        client_id: note.client_id || null,
        local_key: makeLocalKey(note),
        title: note.title || "Untitled",
        content: note.content || "",
        is_locked: Boolean(note.is_locked),
        is_pinned: Boolean(note.is_pinned),
        created_at: note.created_at || new Date().toISOString(),
        updated_at: note.updated_at || new Date().toISOString(),
    };

    await requestToPromise(store.put(normalizedNote));

    return normalizedNote;
}

async function getOutboxChanges() {
    const store = await getStore(OUTBOX_STORE, "readonly");

    return requestToPromise(store.getAll());
}

async function clearOutbox() {
    const store = await getStore(OUTBOX_STORE, "readwrite");

    return requestToPromise(store.clear());
}

async function queueOfflineChange(change) {
    const store = await getStore(OUTBOX_STORE, "readwrite");
    const existingChanges = await requestToPromise(store.getAll());

    const existingChange = existingChanges.find((item) => {
        if (change.local_key && item.local_key === change.local_key)
            return true;
        if (change.id && item.id === change.id) return true;
        if (change.client_id && item.client_id === change.client_id)
            return true;

        return false;
    });

    const queuedChange = {
        ...existingChange,
        ...change,
        created_at: existingChange
            ? existingChange.created_at
            : new Date().toISOString(),
        updated_at: new Date().toISOString(),
    };

    if (existingChange && existingChange.outbox_id) {
        queuedChange.outbox_id = existingChange.outbox_id;
        await requestToPromise(store.put(queuedChange));
    } else {
        await requestToPromise(store.add(queuedChange));
    }

    return queuedChange;
}

async function cacheNotesFromServer() {
    if (!navigator.onLine) return;

    try {
        const response = await fetch("/offline/notes", {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) return;

        const data = await response.json();

        if (!Array.isArray(data.notes)) return;

        for (const note of data.notes) {
            await saveNoteToIndexedDb({
                ...note,
                local_key: makeLocalKey(note),
            });
        }

        // Quan trọng:
        // Online chỉ cache dữ liệu, tuyệt đối không render/đụng UI.
    } catch (error) {
        console.error("Could not cache notes for offline:", error);
    }
}

async function syncOutbox() {
    if (!navigator.onLine) return;

    const changes = await getOutboxChanges();

    if (!changes.length) return;

    showOfflineBanner("Đã có mạng. Đang đồng bộ ghi chú offline...");

    try {
        const response = await fetch("/offline/notes/sync", {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ changes }),
        });

        if (!response.ok) {
            throw new Error(`Sync failed with status ${response.status}`);
        }

        const data = await response.json();

        if (Array.isArray(data.synced)) {
            for (const note of data.synced) {
                await saveNoteToIndexedDb({
                    ...note,
                    local_key: makeLocalKey(note),
                });
            }
        }

        await clearOutbox();
        await cacheNotesFromServer();

        showOfflineBanner("Đồng bộ ghi chú offline thành công.");

        setTimeout(() => {
            hideOfflineBanner();
        }, 2500);
    } catch (error) {
        console.error("Could not sync offline notes:", error);
        showOfflineBanner(
            "Chưa thể đồng bộ ghi chú offline. Sẽ thử lại khi có mạng.",
        );
    }
}

window.saveCurrentNoteOffline = async function (payload = {}) {
    const idInput = getCurrentNoteIdInput();

    const noteId = payload.id || (idInput ? idInput.value : null);
    let clientId = idInput ? idInput.dataset.clientId : null;
    let localKey = idInput ? idInput.dataset.localKey : null;

    const title = payload.title || "Untitled";
    const content = payload.content || "";

    let note;
    let change;

    if (noteId) {
        localKey = localKey || `server-${noteId}`;

        note = {
            id: noteId,
            client_id: null,
            local_key: localKey,
            title,
            content,
            is_locked: false,
            is_pinned: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        change = {
            type: "update",
            id: noteId,
            local_key: localKey,
            title,
            content,
            updated_at: note.updated_at,
        };
    } else {
        if (!clientId) {
            clientId = makeClientId();
        }

        localKey = localKey || `client-${clientId}`;

        note = {
            id: null,
            client_id: clientId,
            local_key: localKey,
            title,
            content,
            is_locked: false,
            is_pinned: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        change = {
            type: "create",
            client_id: clientId,
            local_key: localKey,
            title,
            content,
            updated_at: note.updated_at,
        };
    }

    const savedNote = await saveNoteToIndexedDb(note);
    await queueOfflineChange(change);

    if (idInput) {
        idInput.value = savedNote.id || "";
        idInput.dataset.clientId = savedNote.client_id || "";
        idInput.dataset.localKey = savedNote.local_key || "";
    }

    showOfflineBanner(
        "Đã lưu ghi chú offline. Ghi chú sẽ được đồng bộ khi có mạng.",
    );

    return savedNote;
};

function bindCreateButtonOnlyWhenOffline() {
    const button = document.querySelector("[data-create-offline-note]");

    if (!button) return;

    button.addEventListener(
        "click",
        async (event) => {
            // Online: không làm gì hết.
            // Để onclick="openEditorModal(...)" của bạn chạy 100% như cũ.
            if (navigator.onLine) {
                return;
            }

            // Offline: mới chặn request online.
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            showOfflineBanner(
                "Bạn đang tạo ghi chú offline. Ghi chú sẽ được đồng bộ khi có mạng.",
            );

            const clientId = makeClientId();
            const localKey = `client-${clientId}`;

            const note = await saveNoteToIndexedDb({
                id: null,
                client_id: clientId,
                local_key: localKey,
                title: "",
                content: "",
                is_locked: false,
                is_pinned: false,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
            });

            await queueOfflineChange({
                type: "create",
                client_id: clientId,
                local_key: localKey,
                title: "",
                content: "",
                updated_at: note.updated_at,
            });

            if (typeof window.openEditorModal === "function") {
                window.openEditorModal("/notes/editor");

                setTimeout(() => {
                    const idInput = getCurrentNoteIdInput();
                    const titleInput = getTitleInput();
                    const contentInput = getContentInput();

                    if (idInput) {
                        idInput.value = "";
                        idInput.dataset.clientId = clientId;
                        idInput.dataset.localKey = localKey;
                    }

                    if (titleInput) titleInput.value = "";
                    if (contentInput) contentInput.value = "";
                }, 400);
            }
        },
        true,
    );
}

function bootOfflineNotesSafeMode() {
    if (!isNotesPage()) return;

    bindCreateButtonOnlyWhenOffline();

    // Online lúc load trang:
    // chỉ cache + sync, không đụng UI.
    if (navigator.onLine) {
        cacheNotesFromServer();
        syncOutbox();
    }

    window.addEventListener("online", async () => {
        await syncOutbox();
    });

    window.addEventListener("offline", () => {
        showOfflineBanner(
            "Bạn đang ngoại tuyến. Các chỉnh sửa sẽ được lưu offline.",
        );
    });
}

document.addEventListener("DOMContentLoaded", () => {
    bootOfflineNotesSafeMode();
});

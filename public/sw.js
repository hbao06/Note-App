const CACHE_NAME = "notes-pwa-safe-v3";

const STATIC_ASSETS = [
    "/offline.html",
    "/manifest.webmanifest",
    "/favicon.ico",
    "/js/offline-notes.js",
    "/notes/editor",
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => cacheName !== CACHE_NAME)
                        .map((cacheName) => caches.delete(cacheName)),
                );
            })
            .then(() => self.clients.claim()),
    );
});

self.addEventListener("fetch", (event) => {
    const request = event.request;

    if (request.method !== "GET") {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    const isNotesPage = url.pathname === "/notes";
    const isNotesEditor = url.pathname.startsWith("/notes/editor");
    const isOfflineApi = url.pathname.startsWith("/offline/notes");
    const isAjax = request.headers.get("X-Requested-With") === "XMLHttpRequest";

    /*
     * Editor:
     * Online: lấy editor mới từ Laravel và cache lại.
     * Offline: lấy editor đã cache.
     *
     * Đặt block này TRƯỚC isAjax,
     * vì openEditorModal() gọi /notes/editor bằng AJAX.
     */
    if (isNotesEditor) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response && response.status === 200) {
                        const responseClone = response.clone();

                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }

                    return response;
                })
                .catch(() => {
                    return caches.match(request).then((cachedResponse) => {
                        return (
                            cachedResponse ||
                            caches.match("/notes/editor") ||
                            caches.match("/offline.html")
                        );
                    });
                }),
        );

        return;
    }

    /*
     * AJAX refresh /notes:
     * Không dùng cache để tránh lỗi tạo note xong index không cập nhật.
     */
    if (isAjax || isOfflineApi) {
        event.respondWith(
            fetch(request).catch(() => {
                return new Response("", {
                    status: 503,
                    statusText: "Offline",
                });
            }),
        );

        return;
    }

    /*
     * /notes khi reload trang:
     * Online: lấy mới từ server và cache lại.
     * Offline: trả bản /notes đã cache gần nhất.
     */
    if (isNotesPage) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response && response.status === 200) {
                        const responseClone = response.clone();

                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }

                    return response;
                })
                .catch(() => {
                    return caches.match(request).then((cachedResponse) => {
                        return cachedResponse || caches.match("/offline.html");
                    });
                }),
        );

        return;
    }

    /*
     * Static assets.
     */
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(request)
                .then((response) => {
                    if (!response || response.status !== 200) {
                        return response;
                    }

                    const responseClone = response.clone();

                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });

                    return response;
                })
                .catch(() => {
                    return caches.match("/offline.html");
                });
        }),
    );
});

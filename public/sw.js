const CACHE_NAME = "notes-pwa-safe-v4";

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
    const isNavigation = request.mode === "navigate";

    /*
     * Không cache file upload/user content.
     * Avatar và note images thường nằm trong /storage/.
     */
    if (url.pathname.startsWith("/storage/")) {
        event.respondWith(fetch(request, { cache: "no-store" }));
        return;
    }

    /*
     * Không cache các trang auth/profile/share.
     * Những trang này có CSRF/session/avatar nên cache sẽ gây lỗi 419 hoặc avatar cũ.
     */
    if (
        url.pathname === "/login" ||
        url.pathname === "/register" ||
        url.pathname === "/profile" ||
        url.pathname.startsWith("/profile/") ||
        url.pathname === "/notes/shared"
    ) {
        event.respondWith(fetch(request, { cache: "no-store" }));
        return;
    }

    /*
     * Editor:
     * Online: lấy editor mới từ Laravel và cache lại.
     * Offline: lấy editor đã cache.
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
     * Không cache để tránh tạo note xong index lấy HTML cũ.
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
     * /notes khi reload:
     * Online: lấy mới và cache.
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
     * Các navigation page khác không được cache.
     * Tránh cache HTML có CSRF/session cũ.
     */
    if (isNavigation) {
        event.respondWith(
            fetch(request, { cache: "no-store" }).catch(() => {
                return caches.match("/offline.html");
            }),
        );

        return;
    }

    /*
     * Chỉ cache static assets thật sự.
     */
    const isStaticAsset =
        url.pathname.endsWith(".css") ||
        url.pathname.endsWith(".js") ||
        url.pathname.endsWith(".ico") ||
        url.pathname.endsWith(".png") ||
        url.pathname.endsWith(".jpg") ||
        url.pathname.endsWith(".jpeg") ||
        url.pathname.endsWith(".webp") ||
        url.pathname.endsWith(".svg") ||
        url.pathname.endsWith(".woff") ||
        url.pathname.endsWith(".woff2") ||
        url.pathname === "/manifest.webmanifest" ||
        url.pathname === "/offline.html";

    if (!isStaticAsset) {
        event.respondWith(fetch(request, { cache: "no-store" }));
        return;
    }

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

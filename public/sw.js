const CACHE = 'gymmate-v3';

const PRECACHE = [
    '/offline',
    '/manifest.json',
];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(c => c.addAll(PRECACHE))
    );
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;

    const url = new URL(e.request.url);

    // Externe Ressourcen (Fonts, CDN) → direkt, kein SW-Overhead
    if (url.origin !== self.location.origin) return;

    // Statische Assets → Cache First (kein Netzwerk nötig wenn cached)
    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/')) {
        e.respondWith(
            caches.match(e.request).then(cached =>
                cached ?? fetch(e.request).then(res => {
                    const clone = res.clone();
                    caches.open(CACHE).then(c => c.put(e.request, clone));
                    return res;
                })
            )
        );
        return;
    }

    // Livewire / API-Requests → kein Caching
    if (url.pathname.startsWith('/livewire') || url.pathname === '/sync') return;

    // Seiten → Stale-While-Revalidate: sofort aus Cache, im Hintergrund aktualisieren
    e.respondWith(
        caches.open(CACHE).then(async cache => {
            const cached = await cache.match(e.request);

            const networkFetch = fetch(e.request).then(res => {
                if (res.ok) cache.put(e.request, res.clone());
                return res;
            }).catch(() => cached ?? caches.match('/offline'));

            // Wenn gecacht → sofort zurückgeben, Netzwerk-Update im Hintergrund
            return cached ?? networkFetch;
        })
    );
});

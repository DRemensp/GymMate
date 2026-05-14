const CACHE = 'gymmate-v4';

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

    // Externe Ressourcen (Fonts, CDN) → kein SW-Overhead
    if (url.origin !== self.location.origin) return;

    // Livewire / Sync / Ping → nie cachen
    if (
        url.pathname.startsWith('/livewire') ||
        url.pathname === '/sync' ||
        url.pathname === '/ping'
    ) return;

    // Statische Assets (JS, CSS, Icons) → Cache First (instant)
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

    // Seiten → Network First, Cache nur als Offline-Fallback
    e.respondWith(
        fetch(e.request)
            .then(res => {
                if (res.ok) {
                    const clone = res.clone();
                    caches.open(CACHE).then(c => c.put(e.request, clone));
                }
                return res;
            })
            .catch(() =>
                caches.match(e.request).then(cached =>
                    cached ?? caches.match('/offline')
                )
            )
    );
});

const DB_NAME = 'gymmate-offline';
const STORE = 'queue';

function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = e => {
            e.target.result.createObjectStore(STORE, { keyPath: 'id' });
        };
        req.onsuccess = e => resolve(e.target.result);
        req.onerror = () => reject(req.error);
    });
}

async function dbAll() {
    const db = await openDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readonly');
        const req = tx.objectStore(STORE).getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

async function dbPut(item) {
    const db = await openDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readwrite');
        tx.objectStore(STORE).put(item);
        tx.oncomplete = resolve;
        tx.onerror = () => reject(tx.error);
    });
}

async function dbDelete(id) {
    const db = await openDB();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readwrite');
        tx.objectStore(STORE).delete(id);
        tx.oncomplete = resolve;
        tx.onerror = () => reject(tx.error);
    });
}

function uuid() {
    return crypto.randomUUID ? crypto.randomUUID() : Math.random().toString(36).slice(2) + Date.now().toString(36);
}

function toast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('offline-toast', { detail: { message, type } }));
}

function updateBadge() {
    getPendingCount().then(count => {
        window.dispatchEvent(new CustomEvent('offline-queue-count', { detail: { count } }));
    }).catch(() => {});
}

// Echte Verbindungs-Prüfung: navigator.onLine ist manchmal falsch (WiFi ohne Internet)
async function isOffline() {
    if (!navigator.onLine) return true;
    try {
        const ctrl = new AbortController();
        const timeout = setTimeout(() => ctrl.abort(), 2000);
        const res = await fetch('/ping', { method: 'HEAD', signal: ctrl.signal, cache: 'no-store' });
        clearTimeout(timeout);
        return !res.ok;
    } catch {
        return true;
    }
}

async function enqueue(type, payload) {
    const item = { id: uuid(), type, payload, queued_at: new Date().toISOString(), retries: 0 };
    try {
        await dbPut(item);
        updateBadge();
        toast('Offline gespeichert – wird synchronisiert sobald du wieder online bist', 'offline');
    } catch (err) {
        console.error('[OfflineQueue] enqueue failed:', err);
        toast('Fehler beim lokalen Speichern. Versuche es erneut.', 'conflict');
    }
}

async function getPendingCount() {
    const items = await dbAll();
    return items.length;
}

let syncing = false;

async function sync() {
    if (syncing) return;
    if (await isOffline()) return;
    syncing = true;

    let items;
    try {
        items = await dbAll();
    } catch {
        syncing = false;
        return;
    }

    if (!items.length) {
        syncing = false;
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let anySuccess = false;
    let anyConflict = false;

    for (const item of items) {
        try {
            const res = await fetch('/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ type: item.type, payload: item.payload, queued_at: item.queued_at }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                await dbDelete(item.id);
                anySuccess = true;
                if (data.conflict) anyConflict = true;
            } else if (res.status === 409 || data.conflict) {
                await dbDelete(item.id);
                anyConflict = true;
            } else {
                item.retries = (item.retries || 0) + 1;
                if (item.retries >= 5) {
                    await dbDelete(item.id);
                } else {
                    await dbPut(item);
                }
            }
        } catch {
            // Netzwerkfehler während Sync — bleibt in Queue
        }
    }

    syncing = false;
    updateBadge();

    if (anyConflict) {
        toast('Konflikt erkannt: Eine neuere Version wurde von einem anderen Gerät gespeichert', 'conflict');
    } else if (anySuccess) {
        toast('Synchronisiert ✓', 'success');
        setTimeout(() => window.location.reload(), 1200);
    }
}

window.addEventListener('online', sync);

// Sync beim Start
if (navigator.onLine) setTimeout(sync, 1000);

// Badge beim Start
updateBadge();

window.OfflineQueue = { enqueue, sync, getPendingCount, isOffline };

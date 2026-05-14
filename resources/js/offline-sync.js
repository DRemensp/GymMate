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
    return crypto.randomUUID ? crypto.randomUUID() : Math.random().toString(36).slice(2) + Date.now();
}

function toast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('offline-toast', { detail: { message, type } }));
}

function updateBadge() {
    getPendingCount().then(count => {
        window.dispatchEvent(new CustomEvent('offline-queue-count', { detail: { count } }));
    });
}

async function enqueue(type, payload) {
    const item = { id: uuid(), type, payload, queued_at: new Date().toISOString(), retries: 0 };
    await dbPut(item);
    updateBadge();
    toast('Offline gespeichert – wird synchronisiert sobald du wieder online bist', 'offline');
}

async function getPendingCount() {
    const items = await dbAll();
    return items.length;
}

let syncing = false;

async function sync() {
    if (syncing || !navigator.onLine) return;
    syncing = true;

    const items = await dbAll();
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
            // network failure during sync attempt — leave in queue
        }
    }

    syncing = false;
    updateBadge();

    if (anyConflict) {
        toast('Konflikt erkannt: Eine neuere Version wurde von einem anderen Gerät gespeichert', 'conflict');
    } else if (anySuccess) {
        toast('Synchronisiert', 'success');
        // Soft page reload to reflect synced data
        setTimeout(() => window.location.reload(), 1200);
    }
}

window.addEventListener('online', sync);

// Sync pending items on startup
if (navigator.onLine) {
    setTimeout(sync, 1000);
}

// Initial badge update
updateBadge();

window.OfflineQueue = { enqueue, sync, getPendingCount };

// Gemeinsame Live-Aktualisierung über ?api=live. Seiten registrieren Handler pro Datentyp.
import { getJson, formatBytes } from './lib.js';

const handlers = new Map();
let timer = null;
let failures = 0;
const BASE_INTERVAL = (Number(document.body.dataset.refresh) || 5) * 1000;

export function onData(type, fn) {
    handlers.set(type, fn);
}

function types() {
    const page = (document.body.dataset.poll || '').split(',').filter(Boolean);
    return ['status', ...page.filter((t) => t !== 'status')];
}

async function tick() {
    if (document.hidden || !navigator.onLine) return schedule();
    try {
        const data = await getJson('index.php?api=live&type=' + types().join(','));
        failures = 0;
        for (const [type, fn] of handlers) {
            if (data[type]) fn(data[type]);
        }
    } catch (err) {
        failures = Math.min(failures + 1, 5);
    }
    schedule();
}

function schedule() {
    clearTimeout(timer);
    timer = setTimeout(tick, BASE_INTERVAL * (failures + 1));
}

export function startPolling() {
    clearTimeout(timer);
    schedule();
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            clearTimeout(timer);
            tick();
        }
    });
    window.addEventListener('online', () => {
        failures = 0;
        clearTimeout(timer);
        tick();
    });
}

// Kopfzeile: Credits
onData('status', (d) => {
    for (const [id, value] of [['aj-status-download', d.dl_speed_raw], ['aj-status-upload', d.ul_speed_raw]]) {
        const speed = document.getElementById(id);
        if (speed) speed.textContent = formatBytes(value) + '/s';
    }
    for (const [site, count] of [['downloads', d.downloads_active], ['uploads', d.uploads_active]]) {
        document.querySelectorAll(`[data-badge="${site}"]`).forEach((badge) => {
            badge.textContent = count;
            badge.hidden = !(count > 0);
        });
    }
    const el = document.getElementById('aj-header-credits');
    if (!el) return;
    el.textContent = d.credits;
    el.closest('.app-credits')?.classList.toggle('is-negative', !!d.credits_negative);
});

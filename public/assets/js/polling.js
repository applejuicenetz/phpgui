// Gemeinsame Live-Aktualisierung über ?site=api. Seiten registrieren Handler pro Datentyp.
import { getJson } from './lib.js';

const handlers = new Map();
let timer = null;
let failures = 0;
const BASE_INTERVAL = 5000;

export function onData(type, fn) {
    handlers.set(type, fn);
}

function types() {
    const page = (document.body.dataset.poll || '').split(',').filter(Boolean);
    return ['header', ...page.filter((t) => t !== 'header')];
}

async function tick() {
    if (document.hidden || !navigator.onLine) return schedule();
    try {
        const data = await getJson('index.php?site=api&type=' + types().join(','));
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
onData('header', (d) => {
    const el = document.getElementById('aj-header-credits');
    if (!el) return;
    el.textContent = d.credits;
    el.closest('.app-credits')?.classList.toggle('is-negative', !!d.credits_negative);
});

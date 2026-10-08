// Kleine Hilfsfunktionen, die mehrere Seiten teilen.

export const $ = (sel, root = document) => root.querySelector(sel);
export const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

export function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

/** GET als JSON; wirft bei Netzwerk- oder Statusfehlern. */
export async function getJson(url) {
    const res = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
    if (res.status === 401) {
        window.location.reload();
        throw new Error('unauthorized');
    }
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
}

/** POST (Formularfelder) mit CSRF-Token als JSON-Antwort. */
export async function postJson(url, fields = {}) {
    const body = new URLSearchParams({ _csrf: csrfToken(), ...fields });
    const res = await fetch(url, {
        method: 'POST',
        body,
        headers: { Accept: 'application/json', 'X-CSRF-Token': csrfToken() },
        credentials: 'same-origin',
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
}

export function formatBytes(bytes) {
    const n = Number(bytes) || 0;
    if (n >= 1073741824) return (n / 1073741824).toFixed(1) + ' GB';
    if (n >= 1048576) return (n / 1048576).toFixed(1) + ' MB';
    if (n >= 1024) return (n / 1024).toFixed(1) + ' KB';
    return n + ' B';
}

/** Kurze Einblendung unten bzw. oben auf der Seite. */
export function toast(message, level = 'info') {
    let host = document.getElementById('toast-host');
    if (!host) {
        host = document.createElement('div');
        host.id = 'toast-host';
        host.className = 'toast-host';
        host.setAttribute('aria-live', 'polite');
        document.body.appendChild(host);
    }
    const el = document.createElement('div');
    el.className = 'notification is-light is-' + level + ' app-toast';
    el.textContent = message;
    host.appendChild(el);
    setTimeout(() => el.remove(), 2500);
}

export function setProgress(el, percent) {
    if (!el) return;
    el.value = percent;
    el.textContent = percent + '%';
}

export function setText(el, text) {
    if (el && el.textContent !== String(text)) el.textContent = text;
}

export function debounce(fn, ms = 200) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), ms);
    };
}

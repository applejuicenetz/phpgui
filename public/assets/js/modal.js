// Modal dialogs with focus trap. Single module instance: page scripts and app.js import this exact URL.
import { $$ } from './lib.js';

let lastFocus = null;

export function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    lastFocus = document.activeElement;
    m.hidden = false;
    m.classList.add('is-active');
    document.documentElement.classList.add('is-clipped');
    const target = m.querySelector('textarea, input:not([type=hidden]), select, button:not(.delete)');
    (target || m).focus?.();
    m.dispatchEvent(new CustomEvent('modal:open', { bubbles: true }));
}

export function closeModal(m) {
    if (!m) return;
    m.classList.remove('is-active');
    m.hidden = true;
    if (!document.querySelector('.modal.is-active')) document.documentElement.classList.remove('is-clipped');
    lastFocus?.focus?.();
}

document.addEventListener('click', (e) => {
    const open = e.target.closest('[data-modal-open]');
    if (open) { e.preventDefault(); openModal(open.dataset.modalOpen); return; }
    const close = e.target.closest('[data-modal-close]');
    if (close) closeModal(close.closest('.modal'));
});

document.addEventListener('keydown', (e) => {
    const m = document.querySelector('.modal.is-active');
    if (!m) return;
    if (e.key === 'Escape') {
        closeModal(m);
        e.stopImmediatePropagation();
    }
    if (e.key === 'Tab') {
        const f = $$('a[href], button:not([disabled]), input:not([type=hidden]):not([disabled]), textarea, select', m).filter((x) => x.offsetParent !== null);
        if (!f.length) return;
        const first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
});

// Gemeinsame Oberfläche: Navigation, Dropdowns, Modals, Meldungen, Theme, Bestätigungen.
import { startPolling } from './polling.js';

const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

/* ---- Sidebar (Mobil: Overlay, Tablet: ausklappen) ---- */
const sidebar = document.getElementById('sidebar');
const scrim = document.querySelector('.app-scrim');
const desktop = window.matchMedia('(min-width: 1024px)');

function setSidebar(open) {
    if (!sidebar) return;
    sidebar.classList.toggle('is-open', open);
    if (scrim) scrim.hidden = !open || desktop.matches;
    $$('[data-sidebar-toggle]').forEach((b) => b.setAttribute('aria-expanded', String(open)));
    document.documentElement.classList.toggle('is-clipped', open && !desktop.matches && window.innerWidth < 769);
}
$$('[data-sidebar-toggle]').forEach((b) => b.addEventListener('click', () => setSidebar(!sidebar.classList.contains('is-open'))));
$$('[data-sidebar-close]').forEach((b) => b.addEventListener('click', () => setSidebar(false)));
sidebar?.addEventListener('click', (e) => { if (e.target.closest('a[href]')) setSidebar(false); });
desktop.addEventListener('change', () => setSidebar(false));

/* ---- Dropdowns ---- */
function closeDropdowns(except) {
    $$('[data-dropdown].is-active').forEach((d) => {
        if (d !== except) {
            d.classList.remove('is-active');
            d.querySelector('[data-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
        }
    });
}
document.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-dropdown-toggle]');
    if (toggle) {
        const dd = toggle.closest('[data-dropdown]');
        const open = !dd.classList.contains('is-active');
        closeDropdowns(open ? dd : null);
        dd.classList.toggle('is-active', open);
        toggle.setAttribute('aria-expanded', String(open));
        return;
    }
    if (!e.target.closest('[data-dropdown] .dropdown-menu') || e.target.closest('a.dropdown-item, button.dropdown-item:not([data-keep-open])')) {
        closeDropdowns(null);
    }
});

/* ---- Modals ---- */
import './modal.js';
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.querySelector('.modal.is-active')) { closeDropdowns(null); setSidebar(false); }
});

/* ---- Meldungen ---- */
$$('.app-alert').forEach((a) => {
    a.querySelector('[data-dismiss]')?.addEventListener('click', () => a.remove());
    const ms = Number(a.dataset.autodismiss || 0);
    if (ms > 0) setTimeout(() => a.remove(), ms);
});

/* ---- Theme ---- */
$$('[data-theme-value]').forEach((b) => b.addEventListener('click', () => window.ajTheme?.set(b.dataset.themeValue)));

/* ---- Formulare mit Bestätigung / automatisch absenden ---- */
document.addEventListener('submit', (e) => {
    const form = e.target.closest('form[data-confirm]');
    if (form && !window.confirm(form.dataset.confirm)) e.preventDefault();
});
document.addEventListener('change', (e) => {
    if (e.target.matches('[data-autosubmit]')) e.target.closest('form')?.requestSubmit();
});

/* ---- Protokoll-Handler web+ajfsp (nur HTTPS) ---- */
if (window.location.protocol === 'https:' && navigator.registerProtocolHandler) {
    try {
        const url = new URL('index.php?ajfsp_link=%s', document.baseURI);
        navigator.registerProtocolHandler('web+ajfsp', url.href);
    } catch (err) { /* nicht unterstützt */ }
}

/* ---- Live-Aktualisierung ---- */
if (document.body.dataset.site) startPolling();

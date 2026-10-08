import { $, $$, postJson, toast, formatBytes } from './lib.js';

// Exakte KB-Werte bleiben beim Anzeigen gerundeter MB-Werte erhalten.
export function unitControl(input, buttons, key) {
    let unit = 'kb', exact = null;
    const parse = () => Math.max(0, Number(String(input.value).replace(',', '.')) || 0);
    const kbValue = () => unit === 'kb' ? parse() : exact && input.value === exact.shown ? exact.kb : parse() * 1024;
    function change(next) {
        if (unit === next) return;
        const kb = kbValue();
        if (next === 'mb') {
            exact = { kb, shown: (kb / 1024).toFixed(2) };
            input.value = exact.shown;
        } else input.value = String(kb);
        unit = next;
        buttons.forEach(b => {
            const active = b.dataset.unit === unit;
            b.classList.toggle('is-selected', active);
            b.classList.toggle('is-link', active);
            b.classList.toggle('is-light', active);
            b.setAttribute('aria-pressed', String(active));
        });
        try { localStorage.setItem(key, unit); } catch (_) {}
    }
    buttons.forEach(b => b.addEventListener('click', () => change(b.dataset.unit)));
    try { if (localStorage.getItem(key) === 'mb') change('mb'); } catch (_) {}
    return { kbValue };
}

export function initLimit(kind) {
    const root = $(`[data-limit="${kind}"]`);
    if (!root) return;
    const input = $('input', root);
    const control = unitControl(input, $$('[data-unit]', root), `${kind}_speed_unit`);
    async function apply() {
        const button = $('[data-limit-apply]', root);
        button.disabled = true;
        try {
            const result = await postJson(`index.php?api=limits&action=set_max${kind}`, { value: Math.round(control.kbValue() * 1024) });
            if (!result.ok) throw new Error('save failed');
            toast(document.documentElement.lang === 'de' ? 'Gespeichert!' : 'Saved!');
        } catch (err) { toast(String(err), 'danger'); }
        finally { button.disabled = false; }
    }
    $('[data-limit-apply]', root).addEventListener('click', apply);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); apply(); } });
}

export function updateSpeed(kind, data) {
    const bar = $(`#aj-${kind}-speed-bar`);
    if (!bar) return;
    const max = data.max_raw || 0, speed = data.speed_raw || 0;
    bar.value = max > 0 ? Math.min(100, speed / max * 100) : 0;
    const text = `${formatBytes(speed)}/s / ${max > 0 ? data.max_text + '/s' : '∞'}` + (data.slot_text ? ` · ${data.slot_text}` : '');
    $('[data-speed-label]', bar.parentElement).textContent = text;
    bar.parentElement.title = text;
}

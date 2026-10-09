import { $, $$ } from './lib.js';
import { rangeSelection } from './selection.js';

// File list selection (only present while files are listed).
const master = $('#sharefiles-select-all');
const form = $('#sharefiles-form');
const checks = () => $$('input[name="sharefile[]"]');

function syncSelection() {
    const items = checks();
    const selected = items.filter(check => check.checked).length;
    if (master) {
        master.checked = items.length > 0 && selected === items.length;
        master.indeterminate = selected > 0 && selected < items.length;
    }
    items.forEach(check => check.closest('tr').classList.toggle('is-selected', check.checked));
}

if (form) {
    const range = rangeSelection(form, 'input[name="sharefile[]"]', syncSelection);
    master?.addEventListener('change', () => {
        range.visible().forEach(check => check.checked = master.checked);
        range.reset();
        syncSelection();
    });
    form.addEventListener('change', syncSelection);
    syncSelection();
}

// Search field: typing submits after a short pause, Esc clears.
const filter = $('#share-filter');
if (filter) {
    let timer;
    filter.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => filter.form.requestSubmit(), 400);
    });
    filter.addEventListener('keydown', event => {
        if (event.key === 'Escape' && filter.value !== '') {
            filter.value = '';
            filter.form.requestSubmit();
        }
    });
    if (filter.value !== '') {
        filter.focus();
        filter.setSelectionRange(filter.value.length, filter.value.length);
    }
}

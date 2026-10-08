import { $, $$ } from './lib.js';
import { rangeSelection } from './selection.js';

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

const range = rangeSelection(form, 'input[name="sharefile[]"]', syncSelection);
master?.addEventListener('change', () => {
    range.visible().forEach(check => check.checked = master.checked);
    range.reset();
    syncSelection();
});
form.addEventListener('change', syncSelection);
syncSelection();

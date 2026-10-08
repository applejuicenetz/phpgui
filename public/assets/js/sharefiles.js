import { $, $$ } from './lib.js';

const master = $('#sharefiles-select-all');
const checks = $$('input[name="sharefile[]"]');
let anchor = null;

function syncSelection() {
    const selected = checks.filter(check => check.checked).length;
    if (master) {
        master.checked = checks.length > 0 && selected === checks.length;
        master.indeterminate = selected > 0 && selected < checks.length;
    }
    checks.forEach(check => check.closest('tr').classList.toggle('is-selected', check.checked));
}

master?.addEventListener('change', () => {
    checks.forEach(check => check.checked = master.checked);
    anchor = null;
    syncSelection();
});
checks.forEach((check, index) => {
    check.addEventListener('click', event => {
        if (event.shiftKey && anchor !== null) {
            const start = Math.min(anchor, index);
            const end = Math.max(anchor, index);
            for (let i = start; i <= end; i++) checks[i].checked = check.checked;
        }
        anchor = index;
        syncSelection();
    });
    check.addEventListener('change', syncSelection);
});
syncSelection();

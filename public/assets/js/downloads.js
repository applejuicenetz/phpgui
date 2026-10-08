import { $, $$, setText, setProgress } from './lib.js';
import { openModal } from './modal.js';
import { onData } from './polling.js';
import { initLimit, updateSpeed } from './limits.js';

import { rangeSelection } from './selection.js';

const form = $('#dl-form');
const checks = () => $$('.dl-check');
const selected = () => checks().filter(c => c.checked);
const filter = $('#dl-filter');
function filterRows() {
    const text = filter.value.toLocaleLowerCase();
    $$('#dl-table tbody tr').forEach(r => r.hidden = !r.dataset.name.toLocaleLowerCase().includes(text));
    try { localStorage.setItem('aj_dl_name_filter', filter.value); } catch (_) {}
}
try { filter.value = localStorage.getItem('aj_dl_name_filter') || ''; } catch (_) {}
filter.addEventListener('input', filterRows);
filterRows();
function selection() {
    const count = selected().length;
    checks().forEach(c => c.closest('tr').classList.toggle('is-selected', c.checked));
    $$('[data-dl-action]').forEach(b => {
        b.disabled = count === 0 && b.dataset.dlAction !== 'cleandownloadlist';
        if (b.closest('.action-buttons') && /\bis-(warning|success|danger)\b/.test(b.className)) b.classList.toggle('is-light', b.disabled);
    });
    const master = $('#dl-select-all');
    if (master) {
        master.checked = count > 0 && count === checks().length;
        master.indeterminate = count > 0 && count < checks().length;
    }
}
form.addEventListener('change', e => {
    if (e.target.matches('.dl-check')) {
        if (e.target.checked) $('#pdl-input').value = e.target.closest('tr').dataset.pdl;
        selection();
    }
});
const range = rangeSelection(form, '.dl-check', selection);
$('#dl-select-all')?.addEventListener('change', e => {
    range.visible().forEach(c => c.checked = e.target.checked);
    range.reset();
    selection();
});
selection();

function submit(action, value = '', ids = selected().map(c => c.value)) {
    const fields = { action, action_value: value };
    $$('input[data-action-field]', form).forEach(el => el.remove());
    for (const [name, val] of Object.entries(fields)) {
        const el = document.createElement('input'); el.type = 'hidden'; el.name = name; el.value = val; el.dataset.actionField = ''; form.appendChild(el);
    }
    checks().forEach(c => c.checked = false);
    for (const id of ids) {
        const el = document.createElement('input'); el.type = 'hidden'; el.name = 'dl_id[]'; el.value = id; el.dataset.actionField = ''; form.appendChild(el);
    }
    form.submit();
}
$$('[data-pdl]').forEach(b => b.addEventListener('click', () => {
    let v = Number($('#pdl-input').value.replace(',', '.')) || 1;
    if (b.dataset.pdl === 'inc') v = v === 1 ? 2.2 : v > 1 && v <= 49.9 ? v + .1 : 1;
    else v = v < 2.3 ? 1 : v > 50 ? 50 : v - .1;
    $('#pdl-input').value = v.toFixed(1);
}));
$$('[data-dl-action]').forEach(b => b.addEventListener('click', () => {
    const action = b.dataset.dlAction;
    if (action === 'settargetdir') { targetIds = null; $('#target-input').value = ''; openModal('modal-target'); }
    else if (action === 'canceldownload') {
        $('#cancel-list').replaceChildren(...selected().map(c => { const li = document.createElement('li'); li.textContent = c.closest('tr').dataset.name; return li; }));
        openModal('modal-cancel');
    } else submit(action, action === 'setpowerdownload' ? $('#pdl-input').value : '', action === 'cleandownloadlist' ? ['0'] : undefined);
}));
let renameId;
let targetIds = null; // null: use selection; otherwise only this row
form.addEventListener('click', e => {
    const b = e.target.closest('[data-row-action]');
    if (!b) return;
    const row = b.closest('tr');
    if (b.dataset.rowAction === 'target') {
        targetIds = [row.dataset.id];
        $('#target-input').value = row.dataset.target || '';
        openModal('modal-target');
        return;
    }
    renameId = row.dataset.id;
    $('#rename-input').value = row.dataset.name;
    openModal('modal-rename');
});
$('#rename-ok').addEventListener('click', () => submit('renamedownload', $('#rename-input').value, [renameId]));
$('#rename-input').addEventListener('keydown', e => { if (e.key === 'Enter') $('#rename-ok').click(); });
$('#target-ok').addEventListener('click', () => submit('settargetdir', $('#target-input').value, targetIds ?? undefined));
$('#cancel-ok').addEventListener('click', () => submit('canceldownload'));
initLimit('dl');

onData('downloads', data => {
    const items = data.items || {};
    const existing = $$('#dl-table tbody tr');
    // Neue/entfernte Downloads brauchen vollständiges Server-Rendering. Kein Reload während Auswahl/Dialog.
    const changed = existing.length !== Object.keys(items).length || existing.some(r => !items[r.dataset.id]);
    if (changed && !selected().length && !$('.modal.is-active') && document.activeElement !== filter) { location.reload(); return; }
    for (const [id, d] of Object.entries(items)) {
        const row = $(`#dl-${id}`); if (!row) continue;
        row.dataset.name = d.name; row.dataset.pdl = d.pdl; row.dataset.target = d.target;
        setText($('[data-aj="target"]', row), d.target);
        $('[data-aj="target-line"]', row).hidden = !d.target;
        const status = $('[data-aj="status"]', row);
        status.className = 'tag status-' + d.status; setText(status, d.status_text);
        setText($('.dl-name', row), d.name);
        setText($('[data-aj="percent"]', row), d.percent + '%');
        row.dataset.status = d.status;
        setText($('[data-aj="loaded"]', row), `${d.loaded} / ${d.size}`);
        setText($('[data-aj="eta"]', row), d.eta);
        setProgress($('[data-aj="bar"]', row), d.percent);
        setText($('[data-aj="speed"]', row), d.speed);
        setText($('[data-aj="pdl"]', row), d.pdl);
        setText($('[data-aj="sources"]', row), `${d.sources_queue + d.sources_active}/${d.sources_total}`);
    }
    updateSpeed('dl', data);
    filterRows();
});

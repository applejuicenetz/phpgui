import { $, $$, setText } from './lib.js';
import { onData } from './polling.js';
let active = 'all';
let formats = [];
const input = $('#search-filter');
try { input.value = localStorage.getItem('aj_search_name_filter') || ''; formats = JSON.parse(localStorage.getItem('aj_search_format_filter') || '[]'); } catch (_) {}
function filter() {
    $$('#search-tbody tr').forEach(r => r.hidden = (active !== 'all' && r.dataset.search !== active) || !r.dataset.name.toLowerCase().includes(input.value.toLowerCase()) || (formats.length > 0 && !formats.includes(r.dataset.format)));
}
function buildFormats() {
    const names = [...new Set($$('#search-tbody tr').map(r => r.dataset.format))].sort();
    $('#search-format-list').replaceChildren(...names.map(name => {
        const label = document.createElement('label'); label.className = 'dropdown-item';
        const check = document.createElement('input'); check.type = 'checkbox'; check.checked = formats.includes(name);
        check.addEventListener('change', () => {
            formats = check.checked ? [...formats, name] : formats.filter(f => f !== name);
            localStorage.setItem('aj_search_format_filter', JSON.stringify(formats)); filter();
        });
        label.append(check, document.createTextNode(name)); return label;
    }));
}
$('#search-list').addEventListener('click', e => {
    const tab = e.target.closest('[data-search-tab]'); if (!tab) return;
    e.preventDefault(); active = tab.dataset.searchTab;
    $$('[data-search-tab]').forEach(t => { t.classList.toggle('is-active', t === tab); t.setAttribute('aria-selected', String(t === tab)); });
    $$('[data-search-actions]').forEach(t => t.hidden = t.dataset.searchActions !== active);
    filter();
});
input.addEventListener('input', () => { localStorage.setItem('aj_search_name_filter', input.value); filter(); });
$('#search-select-all').addEventListener('change', e => $$('#search-tbody tr:not([hidden]) input[type=checkbox]').forEach(c => c.checked = e.target.checked));
buildFormats(); filter();
onData('search', d => {
    setText($('#aj-search-badge-all'), d.total);
    for (const [id, s] of Object.entries(d.searches)) setText($(`[data-search-badge="${id}"]`), s.found);
    const rows = $$('#search-tbody tr');
    const incoming = d.entries || [];
    if (rows.length !== incoming.length && !$$('#search-tbody input:checked').length && document.activeElement !== input) { location.reload(); return; }
    incoming.forEach(entry => setText($(`[data-entry="${entry.id}"] [data-aj="sources"]`), entry.sources));
});

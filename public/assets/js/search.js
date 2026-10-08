import { $, $$, setText } from './lib.js';
import { onData } from './polling.js';
import { rangeSelection } from './selection.js';

const root = $('#search-root');
const input = $('#search-filter');
const tbody = $('#search-tbody');
const TAB_KEY = 'aj_search_tab';
let active = 'all';
try {
    input.value = localStorage.getItem('aj_search_name_filter') || '';
    active = sessionStorage.getItem(TAB_KEY) || $('#search-list').dataset.active || 'all';
} catch (_) { /* storage unavailable */ }

const tabLink = (id) => $(`[data-search-tab="${CSS.escape(id)}"]`);
const rowOf = (id) => $(`[data-entry="${CSS.escape(String(id))}"]`, tbody);

function filter() {
    const needle = input.value.toLowerCase();
    $$('#search-tbody tr').forEach((r) => {
        r.hidden = (active !== 'all' && r.dataset.search !== active)
            || !r.dataset.name.toLowerCase().includes(needle);
    });
    $('#search-empty').hidden = $$('#search-tbody tr').length > 0;
    $('#search-empty').textContent = $$('#search-list [data-search-tab]').length > 1 ? root.dataset.textEmpty : root.dataset.textNone;
}

function selectTab(id) {
    if (!tabLink(id)) id = 'all';
    active = id;
    try { sessionStorage.setItem(TAB_KEY, id); } catch (_) { /* ignore */ }
    $$('[data-search-tab]').forEach((t) => {
        t.closest('li').classList.toggle('is-active', t.dataset.searchTab === id);
        t.setAttribute('aria-selected', String(t.dataset.searchTab === id));
    });
    $$('[data-search-actions]').forEach((a) => { a.hidden = a.dataset.searchActions !== id; });
    filter();
}

function addTab(id, search) {
    const li = $('#search-tab-template').content.firstElementChild.cloneNode(true);
    const a = $('a', li); a.dataset.searchTab = id;
    $('.search-term', li).textContent = search.text;
    $('[data-search-badge]', li).dataset.searchBadge = id;
    $('#search-list ul').append(li);
    const holder = document.createElement('div');
    holder.innerHTML = $('#search-actions-template').innerHTML.replaceAll('__SID__', id);
    const actions = holder.firstElementChild; actions.hidden = true;
    $('#search-actions-host').before(actions);
}

function removeTab(id) {
    tabLink(id)?.closest('li').remove();
    $(`[data-search-actions="${CSS.escape(id)}"]`)?.remove();
    $$('#search-tbody tr').filter((r) => r.dataset.search === id).forEach((r) => r.remove());
}

function fillRow(row, entry) {
    row.dataset.entry = entry.id; row.dataset.search = entry.search;
    row.dataset.name = entry.name;
    const check = $('input[type=checkbox]', row); check.value = entry.link; check.setAttribute('aria-label', entry.name);
    $('.dl-name', row).textContent = entry.name;
    $('[data-aj="size"]', row).textContent = entry.size;
    $('[data-aj="sources"]', row).textContent = entry.sources;
    const info = $('[data-aj="info"]', row);
    info.hidden = !entry.info; if (entry.info) info.href = entry.info;
    $('[data-aj="download"]', row).href = 'index.php?site=search&link=' + encodeURIComponent(entry.link);
}

/** Bring the action area of one search (cancel or delete, progress) up to date. */
function syncActions(id, search) {
    const box = $(`[data-search-actions="${CSS.escape(id)}"]`); if (!box) return;
    $('[data-aj="cancel"]', box).hidden = !search.running;
    $('[data-aj="delete"]', box).hidden = search.running;
    const bar = $('[data-search-progress]', box);
    bar.hidden = !(search.running && search.progress < 100);
    bar.value = search.progress; bar.textContent = search.progress + '%';
}

/** Merge new data without reloading: tabs, rows, counts and actions. Returns false when nothing could be applied. */
function merge(d) {
    const searches = d.searches || {};
    const incoming = d.entries || [];
    $$('#search-list [data-search-tab]').map((a) => a.dataset.searchTab).filter((id) => id !== 'all' && !(id in searches)).forEach(removeTab);
    for (const [id, s] of Object.entries(searches)) {
        if (!tabLink(id)) addTab(id, s);
        setText($(`[data-search-badge="${CSS.escape(id)}"]`), s.found);
        syncActions(id, s);
    }
    setText($('#aj-search-badge-all'), d.total);
    $('#search-delete-all').hidden = Object.keys(searches).length === 0;

    const ids = new Set(incoming.map((e) => String(e.id)));
    $$('#search-tbody tr').filter((r) => !ids.has(r.dataset.entry)).forEach((r) => r.remove());
    for (const entry of incoming) {
        let row = rowOf(entry.id);
        if (!row) {
            row = $('#search-row-template').content.firstElementChild.cloneNode(true);
            fillRow(row, entry); tbody.append(row);
        } else {
            setText($('[data-aj="sources"]', row), entry.sources);
        }
    }
    selectTab(active);
}

$('#search-list').addEventListener('click', (e) => {
    const tab = e.target.closest('[data-search-tab]'); if (!tab) return;
    e.preventDefault(); selectTab(tab.dataset.searchTab);
});
input.addEventListener('input', () => { localStorage.setItem('aj_search_name_filter', input.value); filter(); });
function syncSelection() {
    const checks = range.visible();
    const count = checks.filter(c => c.checked).length;
    const master = $('#search-select-all');
    master.checked = checks.length > 0 && count === checks.length;
    master.indeterminate = count > 0 && count < checks.length;
    $$('#search-tbody tr').forEach(row => row.classList.toggle('is-selected', $('input[type=checkbox]', row).checked));
}
const range = rangeSelection(tbody, 'input[type=checkbox]', syncSelection);
tbody.addEventListener('change', syncSelection);
$('#search-select-all').addEventListener('change', (e) => {
    range.visible().forEach(c => { c.checked = e.target.checked; });
    range.reset();
    syncSelection();
});

selectTab(active);
onData('search', merge);

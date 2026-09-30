var SEARCH_FILTER_KEY        = 'aj_search_name_filter';
var SEARCH_FILTER_FORMAT_KEY = 'aj_search_format_filter';

// Live update: search results
AjPolling.register('search', function(data) {
    var entries = data.entries || {};
    var searches = data.searches || {};

    var badgeAll = document.getElementById('aj-search-badge-all');
    if (badgeAll) badgeAll.textContent = data.total_count;

    for (var sid in searches) {
        var s = searches[sid];
        var badge = document.getElementById('aj-search-badge-' + sid);
        if (badge) badge.textContent = s.found_files;

        var progressWrap = document.getElementById('aj-search-progress-' + sid);
        if (s.running && s.progress < 100) {
            if (!progressWrap) {
                var tabPane = document.getElementById('search-' + sid);
                if (tabPane) {
                    var firstChild = tabPane.querySelector('.mb-4');
                    if (firstChild) {
                        progressWrap = document.createElement('div');
                        progressWrap.id = 'aj-search-progress-' + sid;
                        progressWrap.className = 'progress mb-3';
                        progressWrap.innerHTML = '<div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width:0%" aria-valuemin="0" aria-valuemax="100"></div>';
                        firstChild.after(progressWrap);
                    }
                }
            }
            if (progressWrap) {
                var bar = progressWrap.querySelector('.progress-bar');
                if (bar) {
                    bar.style.width = s.progress + '%';
                    bar.textContent = s.progress + ' %';
                    bar.setAttribute('aria-valuenow', s.progress);
                }
            }
        } else if (progressWrap) {
            progressWrap.remove();
        }
    }

    var tbodyAll = document.getElementById('aj-search-tbody-all');
    for (var eid in entries) {
        var e = entries[eid];
        if (tbodyAll) updateOrCreateRow(tbodyAll, eid, e);
        var tbodySearch = document.getElementById('aj-search-tbody-' + e.search_id);
        if (tbodySearch) updateOrCreateRow(tbodySearch, eid, e);
    }
});

function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function updateOrCreateRow(tbody, entryId, entry) {
    var row = tbody.querySelector('tr[data-entry-id="' + entryId + '"]');
    if (row) {
        var srcEl = row.querySelector('[data-aj="sources"]');
        if (srcEl) srcEl.textContent = entry.sources;
        var infoEl = row.querySelector('[data-aj="info"]');
        if (infoEl) infoEl.innerHTML = entry.rel_info;
    } else {
        var tr = document.createElement('tr');
        tr.className = 'align-middle';
        tr.setAttribute('data-entry-id', entryId);
        tr.setAttribute('data-search-name', entry.filename);
        tr.setAttribute('data-search-format', entry.format);

        var safeLink = escapeHtml(entry.ajfsp_link);

        tr.innerHTML =
            '<td><input class="form-check-input" type="checkbox" name="selected_links[]" value="' + safeLink + '"></td>' +
            '<td><b>' + escapeHtml(entry.filename) + '</b></td>' +
            '<td class="text-nowrap">' + escapeHtml(entry.size) + '</td>' +
            '<td class="text-nowrap">' + escapeHtml(entry.format) + '</td>' +
            '<td class="text-nowrap" data-aj="sources">' + entry.sources + '</td>' +
            '<td data-aj="info">' + entry.rel_info + '</td>' +
            '<td><a href="?site=search&link=' + safeLink + '" class="btn btn-success btn-sm"><i class="fa fa-download"></i></a></td>';

        tbody.appendChild(tr);

        // Rebuild format dropdown if it is currently open
        var table = tbody.closest('table');
        var fmtBox = table ? table.querySelector('.search-format-buttons') : null;
        if (fmtBox && fmtBox.style.display !== 'none') {
            buildFormatDropdown(table, null);
        }
    }
    applySearchFilter(tbody.closest('table'));
}

// ---- Filter icon visual state ----

function updateFilterIcons(table) {
    if (!table) return;

    // Format filter icon
    var fmtBox = table.querySelector('.search-format-buttons');
    if (fmtBox) {
        var fmtLink = fmtBox.previousElementSibling;
        var fmtActive = getActiveFormats(table).length > 0;
        if (fmtLink) fmtLink.style.color = fmtActive ? 'var(--cui-warning, #f9b115)' : '';
    }

    // Name filter icon
    var nameInput = table.querySelector('.search-filter-name');
    if (nameInput) {
        var nameBox = nameInput.closest('div');
        var nameLink = nameBox ? nameBox.previousElementSibling : null;
        var nameActive = nameInput.value.trim() !== '';
        if (nameLink) nameLink.style.color = nameActive ? 'var(--cui-warning, #f9b115)' : '';
    }
}

// ---- Filter: toggle open/close ----

function toggleSearchFilter(link) {
    var box = link.nextElementSibling;
    var isFormat = box.classList.contains('search-format-buttons');
    var table = link.closest('table');

    if (box.style.display === 'none') {
        // Open
        box.style.display = '';
        if (isFormat) {
            buildFormatDropdown(table, link);
        } else {
            var input = box.querySelector('input');
            if (input) input.focus();
        }
    } else {
        // Close — keep filter applied, just hide the UI
        box.style.display = 'none';
        if (isFormat) {
            if (box._panel) { box._panel.remove(); box._panel = null; }
        } else {
            var input = box.querySelector('input');
            if (input) input.value = '';
            localStorage.removeItem(SEARCH_FILTER_KEY);
            applySearchFilter(table);
        }
    }
    return false;
}

// Close format dropdown when clicking outside — keep filter applied
document.addEventListener('click', function(e) {
    document.querySelectorAll('.search-format-buttons').forEach(function(box) {
        if (box.style.display === 'none') return;
        var th = box.closest('th');
        var panel = box._panel;
        var clickedInside = (th && th.contains(e.target)) || (panel && panel.contains(e.target));
        if (!clickedInside) {
            box.style.display = 'none';
            if (panel) { panel.remove(); box._panel = null; }
            // Filter stays applied — do NOT clear _savedFormats
        }
    });
});

// Close format dropdown on scroll (panel would drift from anchor)
document.addEventListener('scroll', function() {
    document.querySelectorAll('.search-format-buttons').forEach(function(box) {
        if (box.style.display !== 'none') {
            box.style.display = 'none';
            if (box._panel) { box._panel.remove(); box._panel = null; }
        }
    });
}, true);

// ---- Filter: name input ----

function filterSearchResults(input) {
    localStorage.setItem(SEARCH_FILTER_KEY, input.value);
    applySearchFilter(input.closest('table'));
}

// ---- Filter: format dropdown with checkboxes ----

function buildFormatDropdown(table, anchorEl) {
    var container = table.querySelector('.search-format-buttons');
    if (!container) return;

    // Collect unique formats from all rows
    var seen = {};
    table.querySelectorAll('tbody tr[data-search-format]').forEach(function(row) {
        var f = row.dataset.searchFormat;
        if (f) seen[f] = true;
    });

    // Preserve active formats across open/close cycles via _savedFormats
    var active = getActiveFormats(table);

    // Remove old panel
    if (container._panel) { container._panel.remove(); container._panel = null; }

    // Cache anchor element for polling rebuilds
    if (anchorEl) container._anchorEl = anchorEl;
    var anchor = container._anchorEl || container;

    var rect = anchor.getBoundingClientRect();
    var topPx  = rect.bottom + window.scrollY + 4;
    var leftPx = rect.left   + window.scrollX;

    var panel = document.createElement('div');
    panel.className = 'border rounded bg-body shadow-sm';
    panel.style.cssText =
        'position:absolute;z-index:9999;min-width:150px;max-height:240px;overflow-y:auto;' +
        'top:'  + topPx  + 'px;' +
        'left:' + leftPx + 'px;';

    Object.keys(seen).sort().forEach(function(fmt) {
        var row = document.createElement('label');
        row.style.cssText =
            'display:flex;align-items:center;gap:8px;padding:5px 12px;cursor:pointer;' +
            'white-space:nowrap;margin:0;width:100%;box-sizing:border-box;';

        var cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.dataset.format = fmt;
        cb.checked = active.indexOf(fmt) !== -1;
        cb.style.cssText = 'flex-shrink:0;cursor:pointer;';
        cb.addEventListener('change', function() {
            // Save current selection immediately (persists across sort navigation)
            container._savedFormats = getActiveFormats(table);
            var formats = container._savedFormats;
            if (formats.length > 0) {
                localStorage.setItem(SEARCH_FILTER_FORMAT_KEY, JSON.stringify(formats));
            } else {
                localStorage.removeItem(SEARCH_FILTER_FORMAT_KEY);
            }
            applySearchFilter(table);
        });

        var span = document.createElement('span');
        span.textContent = fmt;
        span.style.cssText = 'overflow:hidden;text-overflow:ellipsis;';

        row.appendChild(cb);
        row.appendChild(span);
        panel.appendChild(row);
    });

    document.body.appendChild(panel);
    container._panel = panel;
}

function getActiveFormats(table) {
    var container = table.querySelector('.search-format-buttons');
    if (!container) return [];
    // Read from open panel
    if (container._panel) {
        var active = [];
        container._panel.querySelectorAll('input[type="checkbox"]:checked').forEach(function(cb) {
            active.push(cb.dataset.format);
        });
        container._savedFormats = active; // keep in sync
        return active;
    }
    // Fall back to last known selection (panel closed but filter still active)
    return container._savedFormats || [];
}

// ---- Filter: apply (name + format) ----

function applySearchFilter(table) {
    if (!table) return;
    var nameInput = table.querySelector('.search-filter-name');
    var nameVal = nameInput ? nameInput.value.toLowerCase() : '';
    var activeFormats = getActiveFormats(table);

    table.querySelectorAll('tbody tr').forEach(function(row) {
        if (!row.dataset.searchName) return;
        var nameMatch = nameVal === '' || row.dataset.searchName.toLowerCase().indexOf(nameVal) !== -1;
        var fmtMatch = activeFormats.length === 0 || activeFormats.indexOf(row.dataset.searchFormat) !== -1;
        row.style.display = (nameMatch && fmtMatch) ? '' : 'none';
    });

    updateFilterIcons(table);
}

// ---- Restore filters after sort navigation ----

function restoreNameFilter() {
    var saved = localStorage.getItem(SEARCH_FILTER_KEY);
    if (!saved) return;
    document.querySelectorAll('.search-filter-name').forEach(function(input) {
        input.value = saved;
        var box = input.closest('div');
        if (box) box.style.display = '';
        applySearchFilter(input.closest('table'));
    });
}

function restoreFormatFilter() {
    var saved = localStorage.getItem(SEARCH_FILTER_FORMAT_KEY);
    if (!saved) return;
    var formats;
    try { formats = JSON.parse(saved); } catch(e) { return; }
    if (!Array.isArray(formats) || formats.length === 0) return;

    document.querySelectorAll('.search-format-buttons').forEach(function(container) {
        container._savedFormats = formats;
        // Apply filter immediately (rows exist from PHP render)
        applySearchFilter(container.closest('table'));
    });
}

// ---- Other search functions ----

function searchSelectAll(cb) {
    var form = cb.closest('form');
    if (!form) return;
    form.querySelectorAll("input[name='selected_links[]']").forEach(function(box) {
        box.checked = cb.checked;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    restoreNameFilter();
    restoreFormatFilter();
    AjPolling.start(['header', 'search']);
});

function showAjToast(msg) {
    var t = document.getElementById('aj-js-toast');
    if (!t) {
        var wrap = document.createElement('div');
        wrap.id = 'aj-js-toast-wrap';
        wrap.style.cssText = 'position:fixed;top:120px;right:5px;z-index:300;opacity:0.9;';
        wrap.innerHTML = '<div id="aj-js-toast" class="toast align-items-center text-white bg-info border-0 fade" role="alert" aria-atomic="true">'
            + '<div class="d-flex"><div id="aj-js-toast-body" class="toast-body"></div>'
            + '<button class="btn-close btn-close-white me-2 m-auto" type="button" onclick="this.closest(\'#aj-js-toast-wrap\').style.display=\'none\'"></button>'
            + '</div></div>';
        document.body.appendChild(wrap);
        t = document.getElementById('aj-js-toast');
    }
    clearTimeout(t._hideTimer);
    document.getElementById('aj-js-toast-body').textContent = msg;
    t.parentElement.style.display = '';
    t.classList.add('show');
    t._hideTimer = setTimeout(function() {
        t.classList.remove('show');
        setTimeout(function() { t.parentElement.style.display = 'none'; }, 300);
    }, 2000);
}

function formatUlBytes(bytes) {
    if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(1) + ' GB';
    if (bytes >= 1048576)    return (bytes / 1048576).toFixed(1) + ' MB';
    if (bytes >= 1024)       return (bytes / 1024).toFixed(1) + ' KB';
    return bytes + ' B';
}

var ul_unit = 'kb';

function parseUlVal(str) {
    return parseFloat(String(str).replace(',', '.')) || 0;
}

function setUlUnit(unit) {
    var input = document.getElementById('maxul');
    var val = parseUlVal(input.value);
    if (ul_unit === unit) return;
    if (unit === 'mb') {
        input.value = (val / 1024).toFixed(1);
    } else {
        input.value = Math.round(val * 1024);
    }
    ul_unit = unit;
    document.getElementById('maxul_kb').classList.toggle('active', unit === 'kb');
    document.getElementById('maxul_mb').classList.toggle('active', unit === 'mb');
    localStorage.setItem('ul_speed_unit', unit);
}

function applyMaxUl() {
    var input = document.getElementById('maxul');
    var val = parseUlVal(input.value);
    var kb = (ul_unit === 'mb') ? Math.round(val * 1024) : Math.round(val);
    var bytes = kb * 1024;
    input.value = (ul_unit === 'mb') ? (kb / 1024).toFixed(1) : kb.toFixed(1);
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'index.php?site=api&action=set_maxul&value=' + bytes);
    input.blur();
    xhr.onload = function() {
        if (xhr.status === 200) {
            aj_max_ul_bytes = bytes;
            var bar = document.getElementById('aj-ul-speed-bar');
            if (bar) { bar._maxRaw = bytes; bar._maxFmt = bytes > 0 ? formatUlBytes(bytes) : ''; }
            updateUlSpeedBar(null);
            showAjToast('Gespeichert!');
        }
    };
    xhr.send();
}

function updateUlSpeedBar(speedInfo) {
    var bar = document.getElementById('aj-ul-speed-bar');
    if (!bar) return;
    if (speedInfo !== null) {
        bar._speedRaw = speedInfo.current_speed_raw;
        bar._speedFmt = speedInfo.current_speed_formatted;
        bar._maxRaw   = speedInfo.max_speed_raw;
        bar._maxFmt   = speedInfo.max_speed_formatted;
        if (typeof aj_max_ul_bytes !== 'undefined') aj_max_ul_bytes = speedInfo.max_speed_raw;
    }
    var speedRaw = bar._speedRaw || 0;
    var speedFmt = bar._speedFmt || '0 B';
    var maxRaw   = bar._maxRaw !== undefined ? bar._maxRaw : (typeof aj_max_ul_bytes !== 'undefined' ? aj_max_ul_bytes : 0);
    var maxFmt   = bar._maxFmt || '';
    var pct = 0;
    var label;
    if (maxRaw > 0) {
        pct = Math.min(100, Math.round((speedRaw / maxRaw) * 100 * 10) / 10);
        label = speedFmt + '/s / ' + maxFmt + '/s';
    } else {
        label = speedFmt + '/s / \u221E';
    }
    bar.style.width = pct + '%';
    bar.textContent = label;
    bar.parentElement.title = label;
    bar.className = 'progress-bar bg-info';
}

// Live update: uploads
AjPolling.register('uploads', function(data) {
    var items = data.items;
    var totalSpeedRaw = 0;
    for (var id in items) {
        var row = document.getElementById('aj-ul-' + id);
        if (!row) { window.location.reload(); return; }
        var ul = items[id];
        totalSpeedRaw += ul.speed_raw || 0;
        var el;
        el = row.querySelector('[data-aj="status"]');
        if (el) el.innerHTML = ul.status_html;
        el = row.querySelector('[data-aj="progress-label"]');
        if (el) el.textContent = ul.progress_label;
        el = row.querySelector('[data-aj="progress-sub"]');
        if (el) el.textContent = ul.progress_sub;
        el = row.querySelector('[data-aj="progress-bar"]');
        if (el) el.style.width = ul.progress_width + '%';
        el = row.querySelector('[data-aj="speed"]');
        if (el) el.textContent = ul.speed;
        el = row.querySelector('[data-aj="icon"]');
        if (el) el.innerHTML = ul.icon;
    }
    if (data.max_speed_raw !== undefined) {
        var bar = document.getElementById('aj-ul-speed-bar');
        if (bar) {
            bar._maxRaw = data.max_speed_raw;
            bar._maxFmt = data.max_speed_formatted;
            aj_max_ul_bytes = data.max_speed_raw;
        }
    }
    var bar2 = document.getElementById('aj-ul-speed-bar');
    if (bar2) {
        bar2._speedRaw = totalSpeedRaw;
        bar2._speedFmt = formatUlBytes(totalSpeedRaw);
    }
    updateUlSpeedBar(null);
});
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('ul_speed_unit');
    if (saved === 'mb') setUlUnit('mb');
});

document.addEventListener('DOMContentLoaded', function() {
    AjPolling.start(['header', 'uploads']);
});

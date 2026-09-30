// Live update: dashboard
AjPolling.register('dashboard', function(data) {
    var map = {
        'aj-dash-downloads': data.downloads,
        'aj-dash-uploads': data.uploads,
        'aj-dash-dl-speed': data.dl_speed,
        'aj-dash-ul-speed': data.ul_speed,
        'aj-dash-session-dl': data.session_dl,
        'aj-dash-session-ul': data.session_ul,
        'aj-dash-open-conn': data.open_conn,
        'aj-dash-connected': data.connected
    };
    for (var id in map) {
        var el = document.getElementById(id);
        if (el) el.textContent = map[id];
    }
    // Credits with color
    var credits = document.getElementById('aj-dash-credits');
    if (credits) {
        var span = credits.querySelector('span') || credits;
        span.textContent = data.credits;
        span.className = data.credits_negative ? 'text-danger' : '';
    }
    // Shares (two-line HTML)
    var shares = document.getElementById('aj-dash-shares');
    if (shares && data.share_size) {
        shares.innerHTML = '<div class="fs-4 fw-semibold">' + data.share_size + '</div>'
            + '<div class="text-body-secondary text-uppercase small">' + data.share_count.toLocaleString('de-DE') + ' Dateien</div>';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    AjPolling.start(['header', 'dashboard']);
});

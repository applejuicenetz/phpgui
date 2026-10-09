// Farbschema vor dem ersten Rendern setzen (verhindert Aufblitzen). Wert: light | dark | auto.
(function () {
    var KEY = 'aj-theme';
    var root = document.documentElement;
    var COLORS = { light: '#ffffff', dark: '#14161a' };
    // An explicit choice overrides the media-query theme-color tags; auto restores them.
    function syncThemeColor(value) {
        var tags = document.querySelectorAll('meta[name="theme-color"]');
        for (var i = 0; i < tags.length; i++) {
            var media = tags[i].getAttribute('media') || '';
            var own = media.indexOf('dark') > -1 ? 'dark' : 'light';
            tags[i].setAttribute('content', COLORS[value === 'light' || value === 'dark' ? value : own]);
        }
        var scheme = document.querySelector('meta[name="color-scheme"]');
        if (scheme) scheme.setAttribute('content', value === 'light' || value === 'dark' ? value : 'light dark');
    }
    function apply(value) {
        if (value === 'light' || value === 'dark') {
            root.setAttribute('data-theme', value);
        } else {
            root.setAttribute('data-theme', 'auto');
        }
        syncThemeColor(value);
    }
    var saved = null;
    try { saved = localStorage.getItem(KEY); } catch (e) { /* Speicher gesperrt */ }
    apply(saved);
    window.ajTheme = {
        get: function () { try { return localStorage.getItem(KEY) || 'auto'; } catch (e) { return 'auto'; } },
        set: function (value) {
            try { localStorage.setItem(KEY, value); } catch (e) { /* ignorieren */ }
            apply(value);
        }
    };
})();

// Farbschema vor dem ersten Rendern setzen (verhindert Aufblitzen). Wert: light | dark | auto.
(function () {
    var KEY = 'aj-theme';
    var root = document.documentElement;
    function apply(value) {
        if (value === 'light' || value === 'dark') {
            root.setAttribute('data-theme', value);
        } else {
            root.setAttribute('data-theme', 'auto');
        }
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

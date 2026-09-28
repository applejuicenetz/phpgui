var AjPolling = {
    timer: null,
    updaters: {},
    types: [],
    interval: 5000,

    register: function(type, fn) {
        this.updaters[type] = fn;
    },

    start: function(types, interval) {
        this.types = types;
        if (interval) this.interval = interval;
        this.stop();
        var self = this;
        var poll = function() {
            if (document.hidden) return;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'index.php?site=api&type=' + self.types.join(','));
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        for (var i = 0; i < self.types.length; i++) {
                            var t = self.types[i];
                            if (data[t] && self.updaters[t]) {
                                self.updaters[t](data[t]);
                            }
                        }
                    } catch(e) {
                        console.error('Poll parse error:', e);
                    }
                } else if (xhr.status === 401) {
                    self.stop();
                    window.location.reload();
                }
            };
            xhr.onerror = function() {
                console.error('Poll network error');
            };
            xhr.send();
        };
        this.timer = setInterval(poll, this.interval);
    },

    stop: function() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }
};

// Header updater - always registered
AjPolling.register('header', function(data) {
    var el = document.getElementById('aj-header-credits');
    if (!el) return;
    el.textContent = data.credits;
    if (data.credits_negative) {
        el.classList.add('text-danger');
    } else {
        el.classList.remove('text-danger');
    }
});

// Fallback: if no page-specific JS starts polling, start header-only polling
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if (!AjPolling.timer) {
            AjPolling.start(['header']);
        }
    }, 500);
});

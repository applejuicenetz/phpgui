const form = document.getElementById('login_form');
const remember = document.getElementById('remember-login');
const key = 'aj_remember_login';
const params = new URLSearchParams(location.search);
form.addEventListener('submit', () => {
    document.getElementById('remember-login-value').value = remember.checked ? '1' : '0';
    if (!remember.checked) {
        try { localStorage.removeItem(key); } catch (_) { /* storage unavailable */ }
    }
});
// Preserve protocol-handler links before submitting saved credentials.
const field = document.getElementById('ajfsp_link');
if (field && !field.value) {
    field.value = new URLSearchParams(window.location.search).get('ajfsp_link') || '';
}
if (window.location.protocol === 'https:' && navigator.registerProtocolHandler) {
    try {
        navigator.registerProtocolHandler('web+ajfsp', new URL('index.php?ajfsp_link=%s', document.baseURI).href);
    } catch (err) { /* nicht unterstützt */ }
}
try {
    if (params.has('logout') || form.dataset.loginError === '1') {
        localStorage.removeItem(key);
    } else if (!params.has('l')) {
        const saved = JSON.parse(localStorage.getItem(key) || 'null');
        if (saved && /^https?:$/.test(new URL(saved.url).protocol) && /^[a-f0-9]{32}$/i.test(saved.md5)) {
            document.getElementById('chost').value = saved.url;
            document.getElementById('cpass').value = saved.md5;
            remember.checked = true;
            form.requestSubmit();
        }
    }
} catch (_) { /* Invalid or unavailable storage must not prevent manual login. */ }

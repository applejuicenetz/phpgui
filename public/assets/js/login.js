// ajfsp_link aus der URL ins versteckte Feld übernehmen (Protokoll-Handler / Erweiterung).
const field = document.getElementById('ajfsp_link');
if (field && !field.value) {
    field.value = new URLSearchParams(window.location.search).get('ajfsp_link') || '';
}
if (window.location.protocol === 'https:' && navigator.registerProtocolHandler) {
    try {
        navigator.registerProtocolHandler('web+ajfsp', new URL('index.php?ajfsp_link=%s', document.baseURI).href);
    } catch (err) { /* nicht unterstützt */ }
}

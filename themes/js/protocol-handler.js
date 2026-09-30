if (window.location.protocol === 'https:' && navigator.registerProtocolHandler) {
    const handlerUrl = new URL('index.php?ajfsp_link=%s', document.baseURI);
    navigator.registerProtocolHandler('web+ajfsp', handlerUrl.href, 'appleJuice Link');
}

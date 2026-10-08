"""Verify SVG part maps load as responsive images."""
from playwright.sync_api import sync_playwright

with sync_playwright() as pw:
    browser = pw.firefox.launch()
    page = browser.new_page(viewport={'width': 360, 'height': 800})
    page.goto('http://127.0.0.1:8088/')
    page.fill('[name=host]', 'http://127.0.0.1:19851')
    page.locator('form').evaluate('f => f.submit()')
    page.wait_for_selector('body[data-site]')
    for query in ('dl_id=105', 'usr_id=107'):
        page.goto('http://127.0.0.1:8088/index.php?site=dl_parts&' + query)
        page.wait_for_load_state('networkidle')
        assert page.locator('.parts-image').evaluate('x => x.complete && x.naturalWidth > 0'), query
        response = page.request.get('http://127.0.0.1:8088/index.php?site=showparts&' + query)
        assert response.headers['content-type'].startswith('image/svg+xml')
        assert '<svg' in response.text()
    browser.close()
print('Download and user SVG maps loaded successfully')

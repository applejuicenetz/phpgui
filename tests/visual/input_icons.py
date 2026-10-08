"""Verify SVG sizes inside Bulma input icon containers."""
from playwright.sync_api import sync_playwright

with sync_playwright() as pw:
    browser = pw.firefox.launch()
    page = browser.new_page()
    page.goto('http://127.0.0.1:8088/')
    page.fill('[name=host]', 'http://127.0.0.1:19851')
    page.locator('form').evaluate('f => f.submit()')
    page.wait_for_load_state('networkidle')
    assert page.locator('body[data-site]').count(), 'Login failed'
    for width in (360, 1280):
        page.set_viewport_size({'width': width, 'height': 800})
        for site in ('downloads', 'search'):
            page.goto('http://127.0.0.1:8088/index.php?site=' + site)
            sizes = page.locator('.control.has-icons-left > .icon > svg').evaluate_all('xs => xs.map(x => ({width: x.getBoundingClientRect().width, height: x.getBoundingClientRect().height}))')
            assert sizes, (site, 'missing icons')
            assert all(s['width'] <= 24 and s['height'] <= 24 for s in sizes), (site, width, sizes)
            offsets = page.locator('.control.has-icons-left > .icon > svg').evaluate_all('xs => xs.map(x => { const icon = x.getBoundingClientRect(), input = x.closest(".control").querySelector("input").getBoundingClientRect(); return Math.abs(icon.y + icon.height / 2 - input.y - input.height / 2); })')
            assert all(offset < 1 for offset in offsets), (site, width, offsets)
            print(site, width, sizes, 'center offsets:', offsets)
    browser.close()
print('Input icon sizes verified')

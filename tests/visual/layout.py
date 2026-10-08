"""Layout-Regression: gleiche Dashboard-Karten, Balkentext außerhalb Füllung."""
from playwright.sync_api import sync_playwright

BASE = 'http://127.0.0.1:8088'
with sync_playwright() as pw:
    browser = pw.firefox.launch()
    page = browser.new_page(color_scheme='dark')
    page.goto(BASE)
    page.fill('[name=host]', 'http://127.0.0.1:19851')
    page.locator('form').evaluate('f => f.submit()')
    page.wait_for_load_state('networkidle')
    assert page.locator('body[data-site]').count(), 'Login failed'
    for scheme in ('light', 'dark'):
        page.evaluate('s => window.ajTheme.set(s)', scheme)
        for width in (360, 768, 1280):
            page.set_viewport_size({'width': width, 'height': 800})
            page.goto(BASE + '/index.php?site=start')
            heights = page.locator('.stat-card').evaluate_all('xs => xs.map(x => x.getBoundingClientRect().height)')
            assert len(heights) == 4 and max(heights) - min(heights) < 1, (scheme, width, heights)
            for site in ('uploads', 'downloads'):
                page.goto(BASE + '/index.php?site=' + site)
                assert page.locator('.speed-bar').evaluate('x => { const l = x.querySelector("[data-speed-label]").getBoundingClientRect(), p = x.querySelector("progress").getBoundingClientRect(); return l.top >= p.top - 1 && l.bottom <= p.bottom + 1 && l.left >= p.left - 1 && l.right <= p.right + 1; }'), (scheme, width, site, 'label outside bar')
                assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), (scheme, width, site)
    browser.close()
print('Layout-Regression bestanden: light/dark × 360/768/1280 px')

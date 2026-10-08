"""Verify master checkbox, Shift-click ranges and toolbar alignment."""
from playwright.sync_api import sync_playwright

with sync_playwright() as pw:
    browser = pw.firefox.launch()
    page = browser.new_page()
    page.goto('http://127.0.0.1:8088/')
    page.fill('[name=host]', 'http://127.0.0.1:19851')
    page.locator('form').evaluate('f => f.submit()')
    page.wait_for_selector('body[data-site]')
    for width in (360, 1280):
        page.set_viewport_size({'width': width, 'height': 800})
        page.goto('http://127.0.0.1:8088/index.php?site=sharefiles&dir=/mock/incoming')
        page.wait_for_timeout(200)
        master = page.locator('#sharefiles-select-all')
        boxes = page.locator('input[name="sharefile[]"]')
        assert boxes.count() >= 5
        assert master.is_visible()
        master.check()
        assert boxes.evaluate_all('xs => xs.every(x => x.checked)')
        master.uncheck()
        boxes.nth(1).click()
        boxes.nth(4).click(modifiers=['Shift'])
        assert boxes.evaluate_all('xs => xs.slice(1, 5).every(x => x.checked)')
        assert master.evaluate('x => x.indeterminate')
        boxes.nth(4).click()
        boxes.nth(1).click(modifiers=['Shift'])
        assert boxes.evaluate_all('xs => xs.every(x => !x.checked)')
        if width == 1280:
            centers = page.locator('.sharefiles-toolbar button,.sharefiles-toolbar a.button,.sharefiles-toolbar select').evaluate_all('xs => xs.map(x => { const r=x.getBoundingClientRect(); return r.y+r.height/2; })')
            assert max(centers) - min(centers) < 1, centers
    browser.close()
print('Share selection and toolbar alignment verified')

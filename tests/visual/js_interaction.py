"""Every page: JS modules load once, sidebar/dropdown/modal respond to a single click."""
from playwright.sync_api import sync_playwright

BASE = 'http://127.0.0.1:8088'
PAGES = ['start', 'downloads', 'uploads', 'search', 'shares', 'server', 'settings', 'user_settings', 'sharefiles&dir=/mock/incoming',
         'dl_users&dl_id=105', 'dl_parts&dl_id=105', 'extras&show=ajl/ajl.php', 'help']

with sync_playwright() as pw:
    browser = pw.firefox.launch()
    for width in (360, 1280):
        page = browser.new_page(viewport={'width': width, 'height': 800})
        errors = []
        page.on('pageerror', lambda e: errors.append(str(e)))
        page.goto(BASE); page.fill('[name=host]', 'http://127.0.0.1:19851'); page.locator('form').evaluate('f => f.submit()')
        page.wait_for_selector('body[data-site]')
        for site in PAGES:
            page.goto(f'{BASE}/index.php?site={site}'); page.wait_for_selector('body[data-site]'); page.wait_for_timeout(250)
            urls = page.evaluate('performance.getEntriesByType("resource").filter(r => /\\/js\\/[a-z]+\\.js/.test(r.name)).map(r => r.name.replace(location.origin, "").split("?")[0])')
            assert len(urls) == len(set(urls)), (width, site, 'module loaded twice', urls)
            dropdown = page.locator('.app-topbar [data-dropdown-toggle]').last
            dropdown.click(); page.wait_for_timeout(80)
            assert page.locator('.app-topbar [data-dropdown].is-active').count() == 1, (width, site, 'dropdown needs exactly one click')
            page.keyboard.press('Escape'); page.wait_for_timeout(50)
            assert page.locator('.app-topbar [data-dropdown].is-active').count() == 0, (width, site, 'Escape closes dropdown')
            page.locator('[data-modal-open="modal-links"]').first.click()
            assert page.locator('#modal-links.is-active').count() == 1, (width, site, 'modal opens')
            page.keyboard.press('Escape')
            assert page.locator('#modal-links.is-active').count() == 0, (width, site, 'Escape closes modal')
            if width < 769:
                page.locator('.app-tab[data-sidebar-toggle]').click(); page.wait_for_timeout(300)
                assert page.locator('#sidebar.is-open').count() == 1, (width, site, 'sidebar opens with one click')
                page.keyboard.press('Escape'); page.wait_for_timeout(300)
                assert page.locator('#sidebar.is-open').count() == 0, (width, site, 'Escape closes sidebar')
        assert not errors, (width, errors)
        page.close()
    browser.close()
print(f'JS interaction verified on {len(PAGES)} pages x 2 widths')

"""AJL-Datei lokal ins dauerhaft sichtbare Textfeld laden und importieren."""
from playwright.sync_api import sync_playwright

with sync_playwright() as pw:
    browser = pw.firefox.launch()
    page = browser.new_page()
    page.goto('http://127.0.0.1:8088/')
    page.fill('[name=host]', 'http://127.0.0.1:19851')
    page.locator('form').evaluate('f => f.submit()')
    page.wait_for_load_state('networkidle')
    page.goto('http://127.0.0.1:8088/index.php?site=extras&show=ajl/ajl.php')
    assert page.locator('#ajl-links').is_visible(), 'Textfeld muss immer sichtbar sein'
    assert page.locator('input[type=radio]').count() == 0
    text = '100\nlocal-file.bin\n0123456789abcdef0123456789abcdef\n5000\n'
    page.locator('#ajl-file').set_input_files({'name': 'probe.ajl', 'mimeType': 'application/octet-stream', 'buffer': text.encode()})
    page.wait_for_function('document.getElementById("ajl-links").value.includes("local-file.bin")')
    assert page.locator('#ajl-links').input_value() == text
    page.locator('#ajl-form button[type=submit]').click()
    page.wait_for_load_state('networkidle')
    assert 'local-file.bin' in page.locator('.result-list').inner_text()
    assert page.locator('.result-list').inner_text().endswith('ok')
    assert page.locator('#ajl-links').input_value() == text
    browser.close()
print('AJL File API und Textimport bestanden')

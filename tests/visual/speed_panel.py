"""Speed panels on downloads and uploads must keep identical geometry and readable bar text."""
from playwright.sync_api import sync_playwright

BASE = 'http://127.0.0.1:8088'
JS = '''() => {
  const r = e => { const b = document.querySelector(e).getBoundingClientRect(); return [b.x, b.y, b.width, b.height].map(v => Math.round(v * 10) / 10); };
  const label = document.querySelector('[data-speed-label]');
  const progress = document.querySelector('.speed-bar progress');
  return {panel: r('.speed-panel'), bar: r('.speed-bar'), form: r('.limit-form'), note: r('.speed-panel-note'),
          labelColor: getComputedStyle(label).color, trackColor: getComputedStyle(progress).getPropertyValue('--bulma-progress-bar-background-color'),
          fillColor: getComputedStyle(progress).getPropertyValue('--bulma-progress-value-background-color'), trackRendered: getComputedStyle(progress).backgroundColor, fillRendered: getComputedStyle(progress, '::-moz-progress-bar').backgroundColor, overflow: document.documentElement.scrollWidth - innerWidth};
}'''

def luminance(css):
    import re
    values = [int(v) for v in re.findall(r'\d+', css)[:3]]
    c = [(v / 255) / 12.92 if v / 255 <= .03928 else (((v / 255) + .055) / 1.055) ** 2.4 for v in values]
    return .2126 * c[0] + .7152 * c[1] + .0722 * c[2]

with sync_playwright() as pw:
    browser = pw.firefox.launch()
    for scheme in ('light', 'dark'):
        for width in (360, 768, 1280):
            page = browser.new_page(viewport={'width': width, 'height': 800}, color_scheme=scheme)
            page.goto(BASE); page.fill('[name=host]', 'http://127.0.0.1:19851'); page.locator('form').evaluate('f => f.submit()'); page.wait_for_load_state('networkidle')
            assert page.locator('body[data-site]').count(), 'Login failed'
            data = {}
            for site in ('downloads', 'uploads'):
                page.goto(f'{BASE}/index.php?site={site}'); data[site] = page.evaluate(JS)
                assert data[site]['overflow'] <= 0, (scheme, width, site)
            assert data['downloads'] == {**data['uploads'], 'labelColor': data['downloads']['labelColor'], 'trackColor': data['downloads']['trackColor'], 'fillColor': data['downloads']['fillColor']}, (scheme, width, data)
            assert data['downloads']['note'][3] > 0
            assert abs(data['downloads']['bar'][3] - data['downloads']['form'][3]) < 1, ('bar/form height', scheme, width, data['downloads'])
            assert luminance(data['downloads']['labelColor']) > .8 and luminance('rgb(' + ','.join(str(int(x)) for x in (50, 54, 66)) + ')') < .05
            fill = data['downloads']['fillRendered']
            assert luminance(data['downloads']['trackRendered']) < .08, ('track too light for white label', data['downloads']['trackRendered'], scheme, width)
            assert luminance(fill) < .2, ('fill too light for white label', fill, scheme, width)
            page.close()
    browser.close()
print('Speed panels identical across downloads/uploads: light/dark x 360/768/1280')

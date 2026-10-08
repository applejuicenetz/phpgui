#!/usr/bin/env python3
"""HTTP-Regression gegen laufende phpGUI und separaten Mock; verändert nur Mock-Daten."""
import argparse
import http.cookiejar
import json
import re
import urllib.parse
import urllib.request

p = argparse.ArgumentParser()
p.add_argument('--base', default='http://127.0.0.1:8088')
p.add_argument('--core', default='http://127.0.0.1:19851')
a = p.parse_args()
o = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
count = 0

def request(site='', fields=None):
    global count
    url = a.base + '/index.php' + ('?site=' + site if site else '')
    data = urllib.parse.urlencode(fields, doseq=True).encode() if fields is not None else None
    r = o.open(url, data, timeout=30)
    body = r.read()
    count += 1
    assert b'Fatal error' not in body and b'<b>Warning</b>' not in body, body[:300]
    return body

def html(site):
    b = request(site).decode()
    assert 'data-site=' in b and 'login_form' not in b, site
    return b

def post(site, fields):
    token = re.search(r'name="csrf-token" content="([^"]+)"', html(site)).group(1)
    return request(site, {'_csrf': token, **fields})

def api(kind):
    return json.loads(request('api&type=' + kind))[kind]

request(fields={'host': a.core, 'cpass': ''})
for site in ['start','downloads','uploads','search','shares','sharefiles&dir=/mock/incoming','server','settings','user_settings','help','dl_users&dl_id=105','dl_parts&dl_id=105','extras&show=ajl/ajl.php','extras&show=sharestats/sharestats.php','extras&show=phpinfo/phpinfo.php']:
    html(site)
assert request('showparts&dl_id=105').startswith(b'<svg'), 'SVG'
rows = api('downloads')['items']
assert len(rows) >= 7
post('downloads', {'action':'pausedownload','dl_id[]':['105']})
assert api('downloads')['items']['105']['status'] == 'paused'
post('downloads', {'action':'resumedownload','dl_id[]':['105']})
assert api('downloads')['items']['105']['status'] == 'loading'
post('downloads', {'action':'renamedownload','dl_id[]':['105'],'action_value':'Ä & <probe>.iso'})
assert api('downloads')['items']['105']['name'] == 'Ä & <probe>.iso'
post('downloads', {'action':'settargetdir','dl_id[]':['105'],'action_value':'Probe'})
assert api('downloads')['items']['105']['target'] == 'Probe'
post('downloads', {'action':'setpowerdownload','dl_id[]':['105'],'action_value':'3.2'})
assert api('downloads')['items']['105']['pdl'] == '3.2'
post('shares', {'action':'add','name':'/mock/new','subs':'1'})
assert '/mock/new' in html('shares')
post('shares', {'action':'remove','name':'/mock/new'})
assert '/mock/new' not in html('shares')
post('sharefiles&dir=/mock/incoming', {'exportlinks':'1'})
assert 'ajfsp://file|' in html('sharefiles&dir=/mock/incoming')
post('settings', {'change':'connection','maxcon':'250','maxul':'256','maxdl':'1234','uls':'30','conturn':'50','maxdlsrc':'500','autoconnect':'true'})
assert api('downloads')['max_raw'] == 1234 * 1024
post('search', {'searchstring':'probe'})
assert 'probe' in html('search')
assert re.search(r'speed-panel-note">[^<]*\d+\s?%[^<]*<', html('uploads')) and '%percent' not in html('uploads') and '{percent}' not in html('uploads'), 'upload slot note'
marker = request('downloads', {'ajfsp_link':'ajfsp://file|smoke.bin|0123456789abcdef0123456789abcdef|5000/'}).decode()
assert 'newlinkinfo' in marker and 'smoke.bin' in marker
assert json.loads(request('directory&dir=/'))['entries']
print(f'{count} HTTP-Requests: Seiten, PNG, Aktionen, Settings, Shares, Export, Suche, Link-Marker erfolgreich')

#!/usr/bin/env python3
"""Zustandsbehafteter Mock des appleJuice-Core (HTTP/XML-API) für phpGUI-Tests.

Nur Standardbibliothek. Der Mock bildet die Endpunkte nach, die phpGUI nutzt:
/xml/{information,settings,share,directory,getobject,modified,downloadpartlist,
userpartlist}.xml und /function/{...}. Aktionen verändern den Zustand wirklich,
Downloads schreiten zeitbasiert voran.

Beispiele:
    python3 mock_core.py                         # Szenario "busy" auf 127.0.0.1:19851
    python3 mock_core.py --scenario empty --port 9851
    python3 mock_core.py --password secret       # Core erwartet MD5("secret")
"""
from __future__ import annotations

import argparse
import hashlib
import json
import threading
import time
import urllib.parse
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from xml.sax.saxutils import quoteattr

DEFAULT_PORT = 19851
EMPTY_PASSWORD_MD5 = hashlib.md5(b"").hexdigest()

# Download-Status (GUI-Codes): 0 laden/suchen, 14 fertig, 17 abgebrochen, 18 pausiert
# User-Status: 1 ungefragt, 5 Warteschlange, 7 aktive Übertragung
# Upload-Status: 1 aktiv, 2 Warteschlange


def now_ms() -> int:
    return int(time.time() * 1000)


def attrs(**values) -> str:
    return " ".join(f"{k}={quoteattr(str(v))}" for k, v in values.items())


class State:
    """Gesamter Core-Zustand. Alle Zugriffe unter self.lock."""

    def __init__(self, scenario: str, password_md5: str):
        self.lock = threading.RLock()
        self.password_md5 = password_md5
        self.started = now_ms()
        self.next_id = 100
        self.settings = {
            "nick": "mocknick",
            "port": 9850,
            "xmlport": DEFAULT_PORT,
            "autoconnect": "true",
            "maxupload": 262144,
            "maxdownload": 1048576,
            "maxconnections": 250,
            "maxsourcesperfile": 500,
            "speedperslot": 30,
            "maxnewconnectionsperturn": 50,
            "incomingdirectory": "/mock/incoming/",
            "temporarydirectory": "/mock/temp/",
        }
        self.share_dirs = [("/mock/incoming", "subdirectory")]
        self.credits = 0
        self.servers: dict[int, dict] = {}
        self.downloads: dict[int, dict] = {}
        self.users: dict[int, dict] = {}
        self.uploads: dict[int, dict] = {}
        self.shares: dict[int, dict] = {}
        self.searches: dict[int, dict] = {}
        self.entries: dict[int, dict] = {}
        self.connected_server = -1
        self.connected_since = 0
        self.firewalled = "false"
        self.exited = False
        self.last_tick = time.time()
        getattr(self, f"scenario_{scenario}")()

    # ---- Szenarien -------------------------------------------------------
    def new_id(self) -> int:
        self.next_id += 1
        return self.next_id

    def add_server(self, name, host, port, connected=False):
        sid = self.new_id()
        self.servers[sid] = {"id": sid, "name": name, "host": host, "port": port,
                             "lastseen": now_ms() if connected else 0,
                             "connectiontry": 0}
        if connected:
            self.connected_server = sid
            self.connected_since = now_ms() - 3_600_000
        return sid

    def add_download(self, filename, size, ready=0, status=0, pdl=0, target="", sources=(), md5=None):
        did = self.new_id()
        self.downloads[did] = {
            "id": did, "shareid": self.new_id(), "size": size, "ready": ready,
            "hash": md5 or hashlib.md5(filename.encode()).hexdigest(),
            "status": status, "powerdownload": pdl, "filename": filename,
            "temporaryfilenumber": did, "targetdirectory": target, "users": [],
        }
        for status_u, speed, nick in sources:
            uid = self.new_id()
            self.users[uid] = {
                "id": uid, "downloadid": did, "status": status_u, "speed": speed,
                "nickname": nick, "version": "0.35.185.93", "os": 1, "directstate": 1,
                "queueposition": 0 if status_u == 7 else 4, "from": -1, "to": -1,
                "pos": -1, "filename": filename,
            }
            self.downloads[did]["users"].append(uid)
            if status_u == 7:
                self.assign_chunk(self.users[uid], self.downloads[did])
        return did

    CHUNK = 8 * 1024 * 1024

    def assign_chunk(self, user, download):
        """Aktive Quelle bekommt einen Byte-Bereich oberhalb des fertigen Teils."""
        taken = sum(1 for u in download["users"] if self.users[u]["from"] >= 0)
        start = min(download["ready"] + taken * self.CHUNK, max(download["size"] - 1, 0))
        user["from"] = start
        user["to"] = min(start + self.CHUNK, download["size"])
        user["pos"] = start

    def scenario_empty(self):
        self.add_server("appleJuice mainserver", "knastbruder.applejuicenet.cc", 9855, connected=True)
        self.firewalled = "false"

    def scenario_busy(self):
        self.credits = 4_300_000_000
        self.add_server("appleJuice mainserver", "knastbruder.applejuicenet.cc", 9855, connected=True)
        self.add_server("Chimera mit sehr langem Servernamen für Umbruchtests", "chimera.goip.de", 9855)
        self.add_server("", "aj-server.ddnss.de", 7001)
        self.add_server("Apple Deluxe", "apple-deluxe.ddns.net", 9855)
        self.add_download("ubuntu-26.04.1-desktop-arm64-with-a-really-long-file-name-for-wrapping-tests.iso",
                          4_154_798_080, ready=1_200_000_000, pdl=0,
                          sources=[(7, 180_000, "alice"), (7, 95_000, "bob"), (5, 0, "carol"), (1, 0, "dave")])
        self.add_download("debian-13.7.0-amd64-netinst.iso", 735_358_976, ready=100_000_000, pdl=12,
                          sources=[(7, 400_000, "erin")])
        self.add_download("Konzert [2024] ÄÖÜ ß <test> & \"quotes\".mkv", 1_500_000_000, ready=300_000_000,
                          sources=[(5, 0, "frank")], target="Videos")
        self.add_download("pausiert.zip", 52_000_000, ready=10_000_000, status=18)
        self.add_download("abgebrochen.rar", 220_000_000, ready=1_000_000, status=17)
        self.add_download("fertig.tar.gz", 8_000_000, ready=8_000_000, status=14)
        self.add_download("suchend.bin", 99_000_000, ready=0, sources=[])
        for i, (nick, status, speed) in enumerate([("alice", 1, 180_000), ("gerd", 2, 0),
                                                   ("heidi", 2, 0), ("ivan", 2, 0)]):
            uid = self.new_id()
            sid = self.new_id()
            fname = ["debian-13.7.0-amd64-netinst.iso", "ajcore.jar", "linux.iso", "movie.mkv"][i]
            self.shares[sid] = {"id": sid, "filename": f"/mock/incoming/{fname}", "short": fname,
                                "size": 100_000_000 * (i + 1), "checksum": hashlib.md5(fname.encode()).hexdigest(),
                                "priority": 1, "lastasked": now_ms() - 60_000, "askcount": i, "searchcount": i * 2}
            self.uploads[uid] = {"id": uid, "shareid": sid, "version": "0.35.185.93", "os": 1 + i % 3,
                                 "status": status, "directstate": 1 + i % 2, "priority": 100 - i * 10,
                                 "nick": nick, "from": 0, "pos": 5_000_000, "to": 10_000_000,
                                 "lastconnection": now_ms(), "speed": speed, "loaded": 5_000_000 * (i + 1)}
        for n in range(5):
            sid = self.new_id()
            self.shares[sid] = {"id": sid, "filename": f"/mock/incoming/archiv-{n}.zip", "short": f"archiv-{n}.zip",
                                "size": 12_000_000 * (n + 1), "checksum": hashlib.md5(f"a{n}".encode()).hexdigest(),
                                "priority": 1 + n % 3, "lastasked": 0, "askcount": 0, "searchcount": 0}
        self.add_search("debian", running=False, results=[
            ("debian-13.7.0-amd64-netinst.iso", 735_358_976, [("debian-13.7.0-amd64-netinst.iso", 12), ("debian.iso", 3)]),
            ("debian-live-xfce.iso", 3_200_000_000, [("debian-live-xfce.iso", 5)]),
            ("debian-notes.txt", 12_345, [("debian-notes.txt", 1)]),
        ])
        self.firewalled = "true"

    def scenario_firewalled(self):
        self.scenario_busy()
        self.firewalled = "true"

    def scenario_disconnected(self):
        self.scenario_empty()
        self.connected_server = -1
        self.connected_since = 0
        self.credits = -50_000_000

    def add_search(self, text, running=True, results=()):
        sid = self.new_id()
        self.searches[sid] = {"id": sid, "text": text, "open": 1 if running else 0,
                              "sum": 3, "running": "true" if running else "false"}
        for name, size, names in results:
            eid = self.new_id()
            self.entries[eid] = {"id": eid, "searchid": sid, "size": size,
                                 "checksum": hashlib.md5(name.encode()).hexdigest(), "names": names}
        self.searches[sid]["found"] = len(results)
        return sid

    # ---- Simulation ------------------------------------------------------
    def tick(self):
        now = time.time()
        dt = now - self.last_tick
        self.last_tick = now
        for d in self.downloads.values():
            if d["status"] != 0:
                continue
            speed = 0
            for uid in d["users"]:
                u = self.users[uid]
                if u["status"] == 7:
                    speed += u["speed"]
            if speed:
                d["ready"] = min(d["size"], d["ready"] + int(speed * dt))
                for uid in d["users"]:
                    u = self.users[uid]
                    if u["status"] != 7:
                        continue
                    u["pos"] = min(u["to"], u["pos"] + int(u["speed"] * dt))
                    if u["pos"] >= u["to"] and u["to"] < d["size"]:
                        u["from"] = -1
                        self.assign_chunk(u, d)
                if d["ready"] >= d["size"]:
                    d["status"] = 14
                    for uid in d["users"]:
                        self.users.pop(uid, None)
                    d["users"] = []
        dl = sum(u["speed"] for u in self.users.values() if u["status"] == 7)
        self.credits += int(dl * dt * 0.2)

    def totals(self):
        dl = sum(u["speed"] for u in self.users.values() if u["status"] == 7)
        ul = sum(u["speed"] for u in self.uploads.values() if u["status"] == 1)
        return dl, ul

    # ---- XML -------------------------------------------------------------
    def information_xml(self):
        dl, ul = self.totals()
        return (f"<information {attrs(credits=self.credits, sessionupload=123456, sessiondownload=654321, uploadspeed=ul, downloadspeed=dl, openconnections=len(self.users), maxuploadpositions=50)}/>\n"
                f"<networkinfo {attrs(users=450, files=4191120, filesize='1519980543.83', firewalled=self.firewalled, ip='203.0.113.7', tryconnecttoserver=-1, connectedwithserverid=self.connected_server, connectedsince=self.connected_since, paused='false')}>\n"
                f"<welcomemessage>Mock-Core: willkommen im Test-Netzwerk</welcomemessage></networkinfo>")

    def server_xml(self):
        return "\n".join(
            f"<server {attrs(id=s['id'], name=s['name'], host=s['host'], lastseen=s['lastseen'], port=s['port'], connectiontry=s['connectiontry'])}/>"
            for s in self.servers.values())

    def ids_xml(self, down: bool, uploads: bool, server: bool):
        out = ["<ids>"]
        if server:
            out += [f'<serverid id="{i}"/>' for i in self.servers]
        if uploads:
            out += [f'<uploadid id="{i}"/>' for i in self.uploads]
        if down:
            for d in self.downloads.values():
                out.append(f'<downloadid id="{d["id"]}">')
                out += [f'<userid id="{u}"/>' for u in d["users"]]
                out.append("</downloadid>")
        out.append("</ids>")
        return "\n".join(out)

    def download_xml(self, d):
        return f"<download {attrs(id=d['id'], shareid=d['shareid'], hash=d['hash'], size=d['size'], status=d['status'], powerdownload=d['powerdownload'], temporaryfilenumber=d['temporaryfilenumber'], filename=d['filename'], targetdirectory=d['targetdirectory'], ready=d['ready'])}/>"

    def user_xml(self, u):
        return f"<user {attrs(id=u['id'], source=4, downloadid=u['downloadid'], status=u['status'], directstate=u['directstate'], downloadfrom=u['from'], downloadto=u['to'], actualdownloadposition=u['pos'], speed=u['speed'], version=u['version'], operatingsystem=u['os'], queueposition=u['queueposition'], powerdownload=0, filename=u['filename'], nickname=u['nickname'])}/>"

    def upload_xml(self, u):
        return f"<upload {attrs(id=u['id'], shareid=u['shareid'], version=u['version'], operatingsystem=u['os'], status=u['status'], directstate=u['directstate'], priority=u['priority'], nick=u['nick'], uploadfrom=u['from'], actualuploadposition=u['pos'], uploadto=u['to'], lastconnection=u['lastconnection'], speed=u['speed'], loaded=u['loaded'])}/>"

    def search_xml(self):
        out = []
        for s in self.searches.values():
            out.append(f"<search {attrs(id=s['id'], searchtext=s['text'], opensearches=s['open'], foundfiles=s['found'], sumsearches=s['sum'], running=s['running'])}/>")
        for e in self.entries.values():
            out.append(f"<searchentry {attrs(id=e['id'], searchid=e['searchid'], checksum=e['checksum'], size=e['size'])}>")
            out += [f"<filename {attrs(name=n, user=c)}/>" for n, c in e["names"]]
            out.append("</searchentry>")
        return "\n".join(out)

    def modified_xml(self, filters: set[str]):
        parts = [f"<time>{now_ms()}</time>"]
        full = not filters or "all" in filters
        if full or "ids" in filters:
            parts.append(self.ids_xml("down" in filters or full, "uploads" in filters or full, True))
        if full or "informations" in filters:
            parts.append(self.information_xml())
        if full or "server" in filters:
            parts.append(self.server_xml())
        if full or "down" in filters:
            parts += [self.download_xml(d) for d in self.downloads.values()]
        if full or "user" in filters:
            parts += [self.user_xml(u) for u in self.users.values()]
        if full or "uploads" in filters:
            parts += [self.upload_xml(u) for u in self.uploads.values()]
        if full or "search" in filters:
            parts.append(self.search_xml())
        return "<applejuice>\n" + "\n".join(p for p in parts if p) + "\n</applejuice>"

    def settings_xml(self):
        s = self.settings
        body = "".join(f"<{k}>{v}</{k}>" for k, v in s.items())
        shares = "".join(f'<directory name="{n}" sharemode="{m}"/>' for n, m in self.share_dirs)
        return f"<settings>{body}<share>{shares}</share></settings>"

    def share_xml(self):
        rows = "\n".join(
            f"<share {attrs(id=s['id'], filename=s['filename'], shortfilename=s['short'], size=s['size'], checksum=s['checksum'], priority=s['priority'], lastasked=s['lastasked'], askcount=s['askcount'], searchcount=s['searchcount'])}/>"
            for s in self.shares.values())
        return f"<applejuice>\n<shares>\n{rows}\n</shares>\n</applejuice>"

    def partlist_xml(self, size: int, ready: int, active: list[tuple[int, int]]):
        parts = [(0, -1)] if ready else []
        parts.append((ready, 3))
        for frm, _to in active:
            parts.append((frm, -1))
        parts.sort()
        rows = "\n".join(f'<part fromposition="{f}" type="{t}"/>' for f, t in parts)
        return f'<applejuice>\n<fileinformation filesize="{size}"/>\n{rows}\n</applejuice>'

    def directory_xml(self, directory: str):
        tree = {"/": ["mock", "home", "tmp"], "/mock": ["incoming", "temp"], "/home": ["user"]}
        key = directory.rstrip("/") or "/"
        dirs = "".join(f'<dir name="{n}" isfilesystem="false" type="1"/>' for n in tree.get(key, []))
        return f'<applejuice>\n<filesystem seperator="/"/>{dirs}</applejuice>'

    # ---- Aktionen --------------------------------------------------------
    def ids_param(self, q) -> list[int]:
        ids = []
        if "id" in q:
            ids.append(int(q["id"][0]))
        n = 1
        while f"id{n}" in q:
            ids.append(int(q[f"id{n}"][0]))
            n += 1
        return ids

    def action(self, name: str, q: dict) -> str:
        ids = self.ids_param(q)
        if name == "pausedownload":
            for i in ids:
                if i in self.downloads and self.downloads[i]["status"] == 0:
                    self.downloads[i]["status"] = 18
        elif name == "resumedownload":
            for i in ids:
                if i in self.downloads and self.downloads[i]["status"] in (17, 18):
                    self.downloads[i]["status"] = 0
        elif name == "canceldownload":
            for i in ids:
                d = self.downloads.get(i)
                if d and d["status"] != 14:
                    d["status"] = 17
                    for u in d["users"]:
                        self.users.pop(u, None)
                    d["users"] = []
        elif name == "cleandownloadlist":
            for i in [i for i, d in self.downloads.items() if d["status"] in (14, 17)]:
                del self.downloads[i]
        elif name == "renamedownload":
            for i in ids:
                if i in self.downloads:
                    self.downloads[i]["filename"] = q.get("name", [""])[0]
        elif name == "settargetdir":
            for i in ids:
                if i in self.downloads:
                    self.downloads[i]["targetdirectory"] = q.get("dir", [""])[0]
        elif name == "setpowerdownload":
            for i in ids:
                if i in self.downloads:
                    self.downloads[i]["powerdownload"] = int(float(q.get("Powerdownload", ["0"])[0]))
        elif name == "setpriority":
            for i in ids:
                if i in self.shares:
                    self.shares[i]["priority"] = int(q.get("priority", ["1"])[0])
        elif name == "search":
            self.add_search(q.get("search", [""])[0], running=True)
        elif name == "cancelsearch":
            for i in ids:
                if i in self.searches:
                    self.searches[i]["running"] = "false"
                    self.searches[i]["open"] = 0
        elif name == "serverlogin":
            for i in ids:
                if i in self.servers:
                    self.connected_server = i
                    self.connected_since = now_ms()
                    self.servers[i]["lastseen"] = now_ms()
        elif name == "removeserver":
            for i in ids:
                self.servers.pop(i, None)
                if self.connected_server == i:
                    self.connected_server = -1
        elif name == "processlink":
            return self.process_link(q.get("link", [""])[0])
        elif name == "setsettings":
            for key, val in q.items():
                if key == "password":
                    continue
                low = key.lower()
                if low in self.settings:
                    self.settings[low] = val[0]
        elif name == "setpassword":
            pass
        elif name in ("sharecheck", "stopsharecheck"):
            pass
        elif name == "exitcore":
            self.exited = True
        else:
            return f"failure: unknown function {name}"
        return "ok"

    def process_link(self, link: str) -> str:
        parts = link.removeprefix("ajfsp://").rstrip("/").split("|")
        if parts[0] == "file" and len(parts) >= 4:
            self.add_download(parts[1], int(parts[3]), md5=parts[2], sources=[(5, 0, "linked")])
            return "ok"
        if parts[0] == "server" and len(parts) >= 3:
            self.add_server("", parts[1], int(parts[2]))
            return "ok"
        return "failure: invalid link"


class Handler(BaseHTTPRequestHandler):
    server_version = "MockAJCore/1"
    state: State

    def log_message(self, format, *args):
        if self.server.verbose:  # type: ignore[attr-defined]
            super().log_message(format, *args)

    def send(self, code: int, body: str, ctype="text/xml", headers=None):
        data = body.encode("utf-8", "replace")
        self.send_response(code)
        self.send_header("Content-Type", f"{ctype}; charset=UTF-8")
        self.send_header("Content-Length", str(len(data)))
        self.send_header("Access-Control-Allow-Origin", "*")
        for k, v in (headers or {}).items():
            self.send_header(k, v)
        self.end_headers()
        self.wfile.write(data)

    def do_OPTIONS(self):
        self.send(204, "")

    def do_GET(self):
        self.handle_request({})

    def do_POST(self):
        length = int(self.headers.get("Content-Length", 0) or 0)
        form = urllib.parse.parse_qs(self.rfile.read(length).decode("utf-8", "replace"))
        self.handle_request(form)

    def handle_request(self, form):
        url = urllib.parse.urlsplit(self.path)
        q = urllib.parse.parse_qs(url.query, keep_blank_values=True)
        q.update(form)
        st = self.state
        header_pw = self.headers.get("X-AppleJuice-Password")
        password = header_pw if header_pw is not None else q.get("password", [""])[0]
        if url.path in ("/help", "/wrongpassword", "/wrongsession"):
            return self.send(200, "<html><body>mock</body></html>", "text/html")
        if password.lower() != st.password_md5:
            return self.send(302, "", headers={"Location": "/wrongpassword"})
        with st.lock:
            st.tick()
            try:
                body, ctype = self.route(url.path, q)
            except (KeyError, ValueError) as exc:
                return self.send(200, f"failure: {exc}", "text/html")
            self.send(200, body, ctype)
            if st.exited:
                threading.Thread(target=self.server.shutdown, daemon=True).start()

    def route(self, path: str, q: dict):
        st = self.state
        head = '<?xml version="1.0"?>\n'
        if path == "/xml/information.xml":
            return head + '<applejuice><generalinformation><version>0.35.185.93-mock</version><filesystem seperator="/"/><system>Linux</system></generalinformation></applejuice>', "text/xml"
        if path == "/xml/settings.xml":
            return head + st.settings_xml(), "text/xml"
        if path == "/xml/share.xml":
            return head + st.share_xml(), "text/xml"
        if path == "/xml/directory.xml":
            return head + st.directory_xml(q.get("directory", ["/"])[0]), "text/xml"
        if path == "/xml/modified.xml":
            filters = {f.strip().lower() for f in q.get("filter", [""])[0].split(";") if f.strip()}
            return head + st.modified_xml(filters), "text/xml"
        if path == "/xml/downloadpartlist.xml":
            d = st.downloads[int(q["id"][0])]
            active = [(st.users[u]["pos"], st.users[u]["to"]) for u in d["users"] if st.users[u]["status"] == 7 and st.users[u]["pos"] >= 0]
            return head + st.partlist_xml(d["size"], d["ready"], active), "text/xml"
        if path == "/xml/userpartlist.xml":
            u = st.users[int(q["id"][0])]
            d = st.downloads[u["downloadid"]]
            return head + st.partlist_xml(d["size"], d["size"] // 2, []), "text/xml"
        if path == "/xml/getobject.xml":
            oid = int(q["id"][0])
            for coll, fn in ((st.downloads, st.download_xml), (st.users, st.user_xml), (st.uploads, st.upload_xml)):
                if oid in coll:
                    return head + f"<applejuice>{fn(coll[oid])}</applejuice>", "text/xml"
            return "error: invalid id", "text/xml"
        if path.startswith("/function/"):
            return st.action(path.removeprefix("/function/"), q), "text/html"
        raise KeyError(f"unknown path {path}")


def run(host: str, port: int, scenario: str, password: str | None, verbose: bool):
    md5 = EMPTY_PASSWORD_MD5 if password is None else hashlib.md5(password.encode()).hexdigest()
    state = State(scenario, md5)
    Handler.state = state
    httpd = ThreadingHTTPServer((host, port), Handler)
    httpd.verbose = verbose  # type: ignore[attr-defined]
    print(json.dumps({"mock_core": f"http://{host}:{port}", "scenario": scenario, "password_md5": md5}), flush=True)
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        httpd.server_close()


if __name__ == "__main__":
    p = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--host", default="127.0.0.1")
    p.add_argument("--port", type=int, default=DEFAULT_PORT)
    p.add_argument("--scenario", default="busy", choices=["empty", "busy", "firewalled", "disconnected"])
    p.add_argument("--password", default=None, help="Klartext; Standard: leer")
    p.add_argument("-v", "--verbose", action="store_true")
    a = p.parse_args()
    run(a.host, a.port, a.scenario, a.password, a.verbose)

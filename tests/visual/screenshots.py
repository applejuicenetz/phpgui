#!/usr/bin/env python3
"""Screenshots aller phpGUI-Seiten in mehreren Viewports und Farbschemata.

Voraussetzung: phpGUI läuft (--base) und ein Core bzw. der Mock (--core) ist erreichbar.
Playwright wird nicht vom Projekt mitgeliefert:
    python3 -m venv .venv && .venv/bin/pip install playwright && .venv/bin/playwright install firefox

    .venv/bin/python tests/visual/screenshots.py --out /tmp/shots/before
    .venv/bin/python tests/visual/screenshots.py --pages downloads,search --widths 360
"""
from __future__ import annotations

import argparse
import json
from pathlib import Path

from playwright.sync_api import sync_playwright

PAGES = ["start", "downloads", "uploads", "search", "shares", "server", "settings", "user_settings", "help", "404"]
VIEWPORTS = {"360": (360, 740), "768": (768, 1024), "1280": (1280, 800)}


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--base", default="http://127.0.0.1:8088")
    p.add_argument("--core", default="http://127.0.0.1:19851")
    p.add_argument("--password", default="")
    p.add_argument("--out", type=Path, default=Path("tests/visual/out"))
    p.add_argument("--pages", default=",".join(PAGES))
    p.add_argument("--widths", default=",".join(VIEWPORTS))
    p.add_argument("--schemes", default="light,dark")
    p.add_argument("--full-page", action="store_true", default=True)
    a = p.parse_args()

    a.out.mkdir(parents=True, exist_ok=True)
    report: list[dict] = []
    with sync_playwright() as pw:
        browser = pw.firefox.launch()
        for scheme in a.schemes.split(","):
            for width in a.widths.split(","):
                w, h = VIEWPORTS[width]
                ctx = browser.new_context(viewport={"width": w, "height": h}, color_scheme=scheme,
                                          has_touch=w < 768, is_mobile=False)
                page = ctx.new_page()
                errors: list[str] = []
                page.on("pageerror", lambda e: errors.append(str(e)))
                page.on("console", lambda m: errors.append(m.text) if m.type == "error" else None)
                page.goto(f"{a.base}/index.php")
                page.evaluate("localStorage.setItem('aj-theme', %s)" % json.dumps(scheme))
                if page.locator("input[name=host]").count():
                    page.fill("input[name=host]", a.core)
                    if page.locator("input[name=cpass]").count():
                        page.fill("input[name=cpass]", a.password)
                    page.locator("form").first.evaluate("f => f.submit()")
                    page.wait_for_load_state("networkidle")
                assert page.locator('body[data-site]').count(), 'Login fehlgeschlagen: Mock/Core prüfen'
                for name in a.pages.split(","):
                    errors.clear()
                    page.goto(f"{a.base}/index.php?site={name}")
                    page.wait_for_load_state("networkidle")
                    page.wait_for_timeout(300)
                    overflow = page.evaluate("document.documentElement.scrollWidth - document.documentElement.clientWidth")
                    target = a.out / f"{name}_{width}_{scheme}.png"
                    page.screenshot(path=str(target), full_page=a.full_page)
                    report.append({"page": name, "width": width, "scheme": scheme,
                                   "horizontal_overflow_px": overflow, "errors": list(errors)})
                ctx.close()
        browser.close()
    (a.out / "report.json").write_text(json.dumps(report, indent=2))
    bad = [r for r in report if r["horizontal_overflow_px"] > 0 or r["errors"]]
    print(f"{len(report)} Screenshots in {a.out}; {len(bad)} mit Overflow oder JS-Fehlern")
    for r in bad:
        print(f"  {r['page']} {r['width']} {r['scheme']}: overflow={r['horizontal_overflow_px']}px errors={r['errors'][:2]}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

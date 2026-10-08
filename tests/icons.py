"""Verify the local Lucide sprite and all statically referenced icons."""
from pathlib import Path
import re
import xml.etree.ElementTree as ET

root = Path(__file__).resolve().parents[1]
sprite = ET.parse(root / 'public/assets/vendor/icons/icons.svg').getroot()
assert sprite.attrib.get('data-icon-set') == 'lucide', 'Expected Lucide, not Bootstrap Icons'
ids = {node.attrib['id'] for node in sprite}
for folder in ('templates', 'src/GUI'):
    for file in (root / folder).rglob('*.php'):
        if file.name in ('GUI.php', 'template.php', 'subs.php'):
            continue
        for name in re.findall(r"(?:View::icon\(|'icon'\s*=>\s*)'([^']+)'", file.read_text()):
            assert 'i-' + name in ids, (file, name)
assert 'ISC' in (root / 'public/assets/vendor/icons/LICENSE').read_text()
print(f'Lucide sprite verified: {len(ids)} icons')

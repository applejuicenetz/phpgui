import { $, $$, setText, setProgress } from './lib.js';
import { onData } from './polling.js';
import { initLimit, updateSpeed } from './limits.js';
initLimit('ul');
onData('uploads', data => {
    const rows = $$('#ul-table tbody tr');
    if (rows.length !== Object.keys(data.items || {}).length) { location.reload(); return; }
    for (const [id, d] of Object.entries(data.items || {})) {
        const row = $(`#ul-${id}`); if (!row) { location.reload(); return; }
        const status = $('[data-aj="status"]', row);
        status.className = 'tag ul-' + d.status;
        setText(status, d.status_text);
        setText($('[data-aj="label"]', row), d.label);
        setText($('[data-aj="sub"]', row), d.sub);
        setText($('[data-aj="speed"]', row), d.speed);
        setProgress($('[data-aj="bar"]', row), d.percent);
        $('[data-aj="direct"]', row).src = d.direct.src;
    }
    updateSpeed('ul', data);
});

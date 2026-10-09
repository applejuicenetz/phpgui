// "Add links" dialog: an .ajl file is converted to ajfsp:// links in the browser.
import { $ } from './lib.js';
import { ajlToLinks } from './ajl.js';

const input = $('#ajfsp-link-input');
const file = $('#ajfsp-file-input');
const note = $('#ajfsp-file-note');

if (input && file && note) {
    const show = (text) => { note.textContent = text; note.hidden = text === ''; };

    file.addEventListener('change', async () => {
        const selected = file.files[0];
        if (!selected) return;
        const links = ajlToLinks(await selected.text());
        if (links.length === 0) {
            show(note.dataset.textInvalid);
        } else {
            const current = input.value.trim();
            input.value = (current === '' ? '' : current + '\n') + links.join('\n');
            input.dispatchEvent(new Event('input', { bubbles: true }));
            show(note.dataset.textLoaded.replace('{n}', String(links.length)));
        }
        file.value = '';
    });
}

// Installed app: .ajl files opened through the OS file handler arrive via launchQueue.
if (input && file && 'launchQueue' in window) {
    window.launchQueue.setConsumer(async (params) => {
        const [handle] = params.files;
        if (!handle) return;
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(await handle.getFile());
        file.files = dataTransfer.files;
        file.dispatchEvent(new Event('change'));
        document.querySelector('[data-modal-open="modal-links"]')?.click();
    });
}

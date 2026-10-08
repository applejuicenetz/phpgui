import { $, toast } from './lib.js';

const fileInput = $('#ajl-file');
const textInput = $('#ajl-links');
if (fileInput && textInput) {
    fileInput.addEventListener('change', async () => {
        const file = fileInput.files[0];
        if (!file) return;
        const submit = $('#ajl-form button[type="submit"]');
        submit.disabled = true;
        try {
            textInput.value = await file.text();
            textInput.dispatchEvent(new Event('input', { bubbles: true }));
        } catch (err) {
            toast(String(err), 'danger');
        } finally {
            submit.disabled = false;
        }
    });
}

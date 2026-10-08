import { $, getJson, toast } from './lib.js';
import { closeModal } from './modal.js';
let dir = '';
async function browse(path) {
    try {
        const data = await getJson('index.php?site=directory&dir=' + encodeURIComponent(path));
        dir = data.dir;
        $('#dirs-current').textContent = dir || '/';
        $('#dirs-list').replaceChildren(...data.entries.map(d => {
            const li = document.createElement('li'); li.className = 'dir-row';
            const b = document.createElement('button'); b.type = 'button'; b.className = 'dir-name'; b.textContent = d.name;
            b.addEventListener('click', () => browse(d.path)); li.appendChild(b); return li;
        }));
    } catch (err) { toast(String(err), 'danger'); }
}
$('#modal-dirs').addEventListener('modal:open', () => browse($('#share-new-path').value));
$('#dirs-choose').addEventListener('click', () => { $('#share-new-path').value = dir || '/'; closeModal($('#modal-dirs')); });

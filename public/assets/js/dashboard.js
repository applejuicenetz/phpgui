import { $, setText } from './lib.js';
import { onData } from './polling.js';
onData('dashboard', d => {
    const fields = { downloads: 'downloads', uploads: 'uploads', connections: 'connections', connected: 'connected', 'session-dl': 'session_dl', 'session-ul': 'session_ul', 'dl-speed': 'dl_speed', 'ul-speed': 'ul_speed' };
    for (const [id, key] of Object.entries(fields)) setText($(`#aj-dash-${id}`), d[key]);
    const credit = $('#aj-dash-credits span');
    setText(credit, d.credits);
    credit?.classList.toggle('has-text-danger', !!d.credits_negative);
});

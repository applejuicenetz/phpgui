import { t, state } from './store.js';
export function bytes(input, precision = 2) {
    let value = Number(input) || 0, step = 0;
    while (Math.abs(value) >= 1024 && step < 6) { value /= 1024; step++; }
    return (step ? value.toLocaleString('en-US', { minimumFractionDigits: precision, maximumFractionDigits: precision }) : Math.trunc(value)) + ' ' + ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'][step];
}
export const speed = value => bytes(value) + '/s';
export function eta(rest, rate) {
    if (rate <= 0) return '';
    const s = Math.floor(rest / rate);
    return s >= 86400 ? (s / 86400).toFixed(1) + 'd' : [Math.floor(s / 3600), Math.floor(s % 3600 / 60), s % 60].map(n => String(n).padStart(2, '0')).join(':');
}
export const duration = seconds => seconds === null ? '?' : `${Math.floor(seconds / 3600)}h ${Math.floor(seconds % 3600 / 60)}min`;
export function compactCount(input) {
    let value = Number(input), step = 0;
    while (step < 3 && Math.abs(Math.round(value * 100) / 100) >= 1000) { value /= 1000; step++; }
    return value.toLocaleString(state.session?.language || 'de', { maximumFractionDigits: step ? 2 : 0 }) + (step ? ' ' + t('UI.units.' + ['', 'thousand', 'million', 'billion'][step]) : '');
}
export function date(value, timeOnly = false) {
    if (!value) return '—';
    const options = timeOnly ? { hour: '2-digit', minute: '2-digit', second: '2-digit' } : { day: 'numeric', month: 'numeric', year: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return new Date(value).toLocaleString(state.session?.language || 'de', { ...options, timeZone: state.session?.timezone || 'Europe/Berlin' });
}
export const osImage = code => 'assets/img/' + ['os_unknow.svg', 'os_windows.svg', 'os_linux.svg', 'os_mac.svg'][code || 0];
export const directImage = code => 'assets/img/' + (code === 1 ? 'direct.svg' : code === 2 || code === 3 ? 'indirect.svg' : 'unknow.svg');
export const relInfo = link => state.session?.rel_info ? state.session.rel_info.replace('%s', encodeURIComponent(link)) : '';
// Never put an arbitrary Core/config URL into a browser navigation sink.
export function safeUrl(value) { return /^(https?:|ajfsp:)/i.test(value || '') ? value : '#'; }

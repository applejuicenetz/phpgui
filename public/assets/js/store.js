import { reactive } from './vue.js';
import { request, setCsrf } from './api.js';

export const state = reactive({ session: null, status: {}, route: {}, alerts: [], busy: false, pendingFile: '' });
export function t(key) { return key.split('.').reduce((value, part) => value?.[part], state.session?.translations) ?? key; }
export function notify(text, level = 'info') {
    const id = Date.now() + Math.random();
    state.alerts.push({ id, text, level });
    setTimeout(() => { state.alerts = state.alerts.filter(item => item.id !== id); }, 5000);
}
export function errorText(error) { return t('UI.errors.' + (error.code || 'network')); }
export function handleError(error) {
    if (error.name === 'AbortError') return;
    if (error.code === 'unauthorized') { state.session.authenticated = false; state.status = {}; }
    notify(errorText(error), 'danger');
}
export async function session() {
    state.session = await request('session');
    setCsrf(state.session.csrf);
    document.documentElement.lang = state.session.language;
}
export function routeFromLocation() {
    const params = Object.fromEntries(new URLSearchParams(location.search));
    state.route = { site: params.site || 'start', ...params };
    document.title = (t('System.pagetitle.' + state.route.site) || '') + ' – appleJuice phpGUI';
}
export function url(site, params = {}) { return 'index.html?' + new URLSearchParams({ site, ...params }); }
export function navigate(site, params = {}, replace = false) {
    history[replace ? 'replaceState' : 'pushState'](null, '', url(site, params));
    routeFromLocation();
}
export async function mutate(endpoint, fields, params = {}) {
    try { return await request(endpoint, fields, params); }
    catch (error) { handleError(error); return null; }
}
export function rememberGet(key, fallback = '') { try { return localStorage.getItem(key) ?? fallback; } catch { return fallback; } }
export function rememberSet(key, value) { try { localStorage.setItem(key, value); } catch { /* Storage may be disabled. */ } }
export function forgetLogin() { try { localStorage.removeItem('aj_remember_login'); } catch { /* Storage may be disabled. */ } }

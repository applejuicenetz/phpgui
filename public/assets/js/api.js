// All transport stays here. A reverse proxy can route api.php to a separate backend.
import { reactive } from './vue.js';

let csrf = '';
// Drives the topbar heartbeat: pending = requests in flight, beat = increments per request start.
export const activity = reactive({ pending: 0, beat: 0 });
export function setCsrf(value) { csrf = value; }
export class ApiError extends Error {
    constructor(status, code) { super(code); this.status = status; this.code = code; }
}
export async function request(endpoint, fields = null, params = {}, signal) {
    const url = new URL('api.php', document.baseURI);
    url.search = new URLSearchParams({ endpoint, ...params });
    const options = { credentials: 'same-origin', headers: { Accept: 'application/json' }, signal };
    if (fields !== null) {
        options.method = 'POST';
        options.headers['X-CSRF-Token'] = csrf;
        const body = new URLSearchParams();
        for (const [key, value] of Object.entries(fields)) {
            if (Array.isArray(value)) value.forEach(item => body.append(key + '[]', item));
            else body.set(key, String(value));
        }
        options.body = body;
    }
    activity.pending++;
    activity.beat++;
    try {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok) throw new ApiError(response.status, data.error || 'internal_error');
        return data;
    } finally {
        activity.pending--;
    }
}

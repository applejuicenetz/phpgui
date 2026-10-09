import { ref, onMounted, onUnmounted } from './vue.js';
import { request } from './api.js';
import { state, handleError } from './store.js';

// One in-flight request per component. Visibility/online events cannot start duplicate loops.
export function useData(endpoint, params = () => ({}), poll = true) {
    const data = ref(null), loading = ref(true);
    let timer, controller, disposed = false, failures = 0, pending = false;
    async function refresh() {
        if (disposed || pending || !state.session?.authenticated) return;
        clearTimeout(timer);
        pending = true;
        controller = new AbortController();
        try {
            data.value = await request(endpoint, null, params(), controller.signal);
            failures = 0;
        } catch (error) {
            failures = Math.min(5, failures + 1);
            if (!data.value || error.status === 401) handleError(error);
        } finally {
            pending = false; loading.value = false;
            if (poll && !disposed) timer = setTimeout(tick, (state.session?.refresh || 5) * 1000 * (failures + 1));
        }
    }
    function tick() {
        if (document.hidden || !navigator.onLine) timer = setTimeout(tick, 5000);
        else refresh();
    }
    function resume() { if (!document.hidden && navigator.onLine && poll) refresh(); }
    onMounted(() => { refresh(); document.addEventListener('visibilitychange', resume); window.addEventListener('online', resume); });
    onUnmounted(() => { disposed = true; clearTimeout(timer); controller?.abort(); document.removeEventListener('visibilitychange', resume); window.removeEventListener('online', resume); });
    return { data, loading, refresh };
}

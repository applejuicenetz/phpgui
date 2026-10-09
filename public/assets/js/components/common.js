import { ref, nextTick, watch, onUnmounted } from '../vue.js';
import { t, state, url, navigate, mutate, notify, rememberGet, rememberSet } from '../store.js';
import { bytes, speed } from '../format.js';

export const Icon = { props: ['name'], template: `<svg class="icon" aria-hidden="true" focusable="false"><use :href="'assets/vendor/icons/icons.svg#i-' + name"></use></svg>` };
export const Modal = {
    props: ['title', 'danger', 'fit'], emits: ['close'],
    setup(props, { emit }) {
        const root = ref(null), previous = document.activeElement;
        nextTick(() => { root.value?.querySelector('input, textarea, button:not(.delete)')?.focus(); document.documentElement.classList.add('is-clipped'); });
        function key(event) {
            if (event.key === 'Escape') emit('close');
            if (event.key !== 'Tab') return;
            const nodes = [...root.value.querySelectorAll('button:not([disabled]), input, textarea, select, a[href]')].filter(el => el.offsetParent !== null);
            const first = nodes[0], last = nodes.at(-1);
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
        }
        document.addEventListener('keydown', key);
        onUnmounted(() => { document.removeEventListener('keydown', key); document.documentElement.classList.remove('is-clipped'); previous?.focus(); });
        return { root, t };
    },
    template: `<div ref="root" class="modal is-active" :role="danger ? 'alertdialog' : 'dialog'" aria-modal="true" aria-labelledby="modal-title"><div class="modal-background" @click="$emit('close')"></div><div class="modal-card" :class="{'is-fit': fit}"><header class="modal-card-head" :class="{'has-background-danger': danger}"><h2 class="modal-card-title" id="modal-title">{{title}}</h2><button type="button" class="delete" :aria-label="t('UI.close')" @click="$emit('close')"></button></header><section class="modal-card-body"><slot></slot></section><footer class="modal-card-foot"><slot name="footer"></slot><button type="button" class="button" @click="$emit('close')">{{t('UI.cancel')}}</button></footer></div></div>`
};
export const SortLink = {
    props: ['field', 'label', 'sort', 'direction'], emits: ['sort'],
    template: `<button type="button" class="sort-link" :class="{'is-sorted': sort === field}" @click="$emit('sort', field)">{{label}} <span v-if="sort === field" aria-hidden="true">{{direction === 'asc' ? '↑' : '↓'}}</span></button>`
};
export const UnitInput = {
    props: ['modelValue', 'storageKey', 'label', 'id'], emits: ['update:modelValue'],
    setup(props, { emit }) {
        const unit = ref(rememberGet(props.storageKey, 'kb'));
        const shown = ref('');
        function show() { shown.value = unit.value === 'mb' ? (props.modelValue / 1048576).toFixed(2) : String(props.modelValue / 1024); }
        watch(() => props.modelValue, show, { immediate: true });
        function change(next) { unit.value = next; rememberSet(props.storageKey, next); show(); }
        function input(value) {
            // A rounded MB display must not overwrite the exact byte value unless edited.
            if (value === shown.value) return;
            shown.value = value;
            emit('update:modelValue', Math.max(0, Math.round((Number(value.replace(',', '.')) || 0) * (unit.value === 'mb' ? 1048576 : 1024))));
        }
        return { unit, shown, change, input, t };
    },
    template: `<div class="limit-row"><input class="input" type="text" inputmode="decimal" :id="id" :aria-label="label" :value="shown" @input="input($event.target.value)"><div class="buttons has-addons unit-toggle"><button v-for="u in ['kb','mb']" :key="u" type="button" class="button" :class="{'is-selected is-link is-light': unit === u}" :aria-pressed="unit === u" @click="change(u)">{{t('Settings.unit_' + u)}}</button></div></div>`
};
export const SpeedPanel = {
    props: ['kind', 'data'], components: { UnitInput, Icon },
    setup(props) {
        const limit = ref(props.data.max), busy = ref(false);
        async function apply() { busy.value = true; const result = await mutate('limits', {action: 'set_max' + props.kind, value: limit.value}); busy.value = false; if(result) notify(t('UI.saved'), 'success'); }
        return { limit, busy, apply, speed, bytes, t };
    },
    template: `<section class="box toolbar-box speed-panel"><div class="speed-panel-grid"><div class="speed-bar"><progress class="progress" :value="data.max > 0 ? Math.min(100, data.speed / data.max * 100) : 0" max="100"></progress><span class="speed-bar-label">{{speed(data.speed)}} / {{data.max > 0 ? speed(data.max) : '∞'}}<template v-if="kind === 'ul'"> · {{t('Uploads.limit').replace('{percent}', data.slots_max > 0 ? Math.round(data.slots_used / data.slots_max * 100) : '?')}}</template></span></div><div class="field has-addons limit-form"><div class="control is-expanded"><UnitInput v-model="limit" :storage-key="kind + '_speed_unit'" :label="t('Downloads.limit')" /></div><div class="control"><button type="button" class="button is-primary" :disabled="busy" @click="apply" :aria-label="t('Settings.save')"><Icon name="check-lg" /></button></div></div></div></section>`
};
export const ShareTabs = { setup: () => ({t,state,url}), template: `<div class="share-tabs-row"><div class="tabs is-boxed share-tabs" role="tablist"><ul><li :class="{'is-active': state.route.site !== 'sharestats'}"><a :href="url('shares')" role="tab">{{t('Navigation.shares')}}</a></li><li :class="{'is-active': state.route.site === 'sharestats'}"><a :href="url('sharestats')" role="tab">{{t('Share.statistics')}}</a></li></ul></div><slot></slot></div>` };

// Vue owns selection state, including live-added/deleted rows and Shift-click ranges.
export function selection() {
    const selected = ref([]); let anchor = null;
    function toggle(id, event, visible) {
        const from = visible.indexOf(anchor), to = visible.indexOf(id), checked = event.target.checked;
        const ids = event.shiftKey && from >= 0 ? visible.slice(Math.min(from,to), Math.max(from,to) + 1) : [id];
        const set = new Set(selected.value);
        ids.forEach(value => checked ? set.add(value) : set.delete(value));
        selected.value = [...set]; anchor = id;
    }
    function all(event, ids) { const set = new Set(selected.value); ids.forEach(id => event.target.checked ? set.add(id) : set.delete(id)); selected.value = [...set]; anchor = null; }
    function prune(ids) { selected.value = selected.value.filter(id => ids.includes(id)); }
    return { selected, toggle, all, prune };
}

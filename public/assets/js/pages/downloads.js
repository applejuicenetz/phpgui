import { ref, computed, watch, onMounted, onUnmounted } from '../vue.js';
import { useData } from '../polling.js';
import { t, state, url, mutate, notify, rememberGet, rememberSet } from '../store.js';
import { bytes, speed, eta } from '../format.js';
import { Icon, Modal, SortLink, SpeedPanel, selection } from '../components/common.js';

export default {
    components: { Icon, Modal, SortLink, SpeedPanel },
    setup() {
        const { data, loading, refresh } = useData('downloads');
        const filter = ref(rememberGet('aj_dl_name_filter')), sort = ref(state.route.sort || 'status'), direction = ref(state.route.sort_dir || 'asc');
        const select = selection(), dialog = ref(''), value = ref(''), actionIds = ref([]), pdl = ref('1.0'), busy = ref(false), menu = ref(null), menuStyle = ref({});
        const rows = computed(() => {
            const items = (data.value?.items || []).filter(row => row.name.toLocaleLowerCase().includes(filter.value.toLocaleLowerCase()));
            const order = ['loading','searching','paused','canceled','done','unknown'];
            return items.slice().sort((a,b) => {
                let result = sort.value === 'name' ? a.name.localeCompare(b.name) : sort.value === 'done' ? a.percent-b.percent : sort.value === 'pdl' ? a.pdl-b.pdl : order.indexOf(a.status)-order.indexOf(b.status);
                return (direction.value === 'desc' ? -result : result) || a.id-b.id;
            });
        });
        function closeMenu() { menu.value = null; }
        function outsideMenu(event) { if (!event.target.closest('.col-actions')) closeMenu(); }
        function escapeMenu(event) { if (event.key === 'Escape') closeMenu(); }
        onMounted(() => { document.addEventListener('aj:close-menus', closeMenu); document.addEventListener('click', outsideMenu); document.addEventListener('keydown', escapeMenu); });
        onUnmounted(() => { document.removeEventListener('aj:close-menus', closeMenu); document.removeEventListener('click', outsideMenu); document.removeEventListener('keydown', escapeMenu); });
        const ids = computed(() => rows.value.map(row => row.id));
        watch(filter, value => rememberSet('aj_dl_name_filter', value));
        watch(() => data.value?.items, items => select.prune((items || []).map(row => row.id)));
        function changeSort(field) { direction.value = sort.value === field ? (direction.value === 'asc' ? 'desc' : 'asc') : ['done','pdl'].includes(field) ? 'desc' : 'asc'; sort.value = field; }
        function choose(row,event) { select.toggle(row.id,event,ids.value); if (event.target.checked) pdl.value = String(row.pdl); }
        async function act(action, actionValue = '', chosen = select.selected.value) {
            busy.value = true;
            const result = await mutate('downloads', {action, action_value:actionValue, dl_id:chosen});
            busy.value = false;
            if (result) { dialog.value = ''; select.selected.value = []; notify(t('UI.saved'), 'success'); await refresh(); }
        }
        function open(type,row=null) { menu.value = null; actionIds.value = row ? [row.id] : [...select.selected.value]; value.value = row ? (type === 'rename' ? row.name : row.target) : ''; dialog.value = type; }
        function bump(increment) { let n = Number(pdl.value.replace(',','.')) || 1; n = increment ? (n === 1 ? 2.2 : n < 50 ? n+.1 : 1) : n < 2.3 ? 1 : n > 50 ? 50 : n-.1; pdl.value = n.toFixed(1); }
        function showMenu(row,event) { menu.value = menu.value === row.id ? null : row.id; const rect = event.currentTarget.getBoundingClientRect(); menuStyle.value = {position:'fixed', left:Math.max(8,Math.min(rect.right-220,innerWidth-228))+'px',top:(rect.bottom+250<innerHeight?rect.bottom:Math.max(8,rect.top-250))+'px'}; }
        return { data,loading,rows,ids,filter,sort,direction,selected:select.selected,toggleAll:select.all,choose,dialog,value,actionIds,pdl,busy,menu,menuStyle,showMenu,changeSort,act,open,bump,t,url,bytes,speed,eta };
    },
    template: `
    <div id="dl-form" v-if="data">
        <SpeedPanel kind="dl" :data="data" />
        <section class="box">
            <div class="selection-bar"><div class="field has-addons pdl-form">
                <p class="control"><button type="button" class="button is-small" @click="bump(false)" aria-label="−"><Icon name="dash-lg" /></button></p>
                <p class="control"><input class="input is-small" id="pdl-input" v-model="pdl" inputmode="decimal" :aria-label="t('Downloads.pdl_value')"></p>
                <p class="control"><button type="button" class="button is-small" @click="bump(true)" aria-label="+"><Icon name="plus-lg" /></button></p>
                <p class="control"><button class="button is-small" :disabled="!selected.length || busy" @click="act('setpowerdownload',pdl)">{{t('Downloads.set_pdl')}}</button></p>
            </div><div class="buttons are-small action-buttons">
                <button class="button is-warning" :class="{'is-light':!selected.length}" :disabled="!selected.length || busy" @click="act('pausedownload')"><Icon name="pause-fill" /><span class="action-text">{{t('Downloads.pause')}}</span></button>
                <button class="button is-success" :class="{'is-light':!selected.length}" :disabled="!selected.length || busy" @click="act('resumedownload')"><Icon name="play-fill" /><span class="action-text">{{t('Downloads.resume')}}</span></button>
                <button class="button is-danger" :class="{'is-light':!selected.length}" :disabled="!selected.length || busy" @click="open('cancel')"><Icon name="x-lg" /><span class="action-text">{{t('Downloads.cancel')}}</span></button>
                <button class="button" :disabled="!selected.length || busy" @click="open('target')"><Icon name="folder" /><span class="action-text">{{t('Downloads.target')}}</span></button>
                <button class="button is-link action-clean" :disabled="busy" @click="act('cleandownloadlist','',['0'])"><Icon name="magic" /><span class="action-text">{{t('Downloads.clean')}}</span></button>
            </div></div>
            <div class="field filter-field"><p class="control has-icons-left"><input class="input" type="search" id="dl-filter" v-model="filter" :placeholder="t('UI.filter_placeholder')" :aria-label="t('UI.filter')"><span class="icon is-left"><Icon name="funnel" /></span></p></div>
            <div class="sort-bar"><SortLink v-for="[field,key] in [['name','filename'],['status','statuss'],['done','progress'],['pdl','pdl']]" :key="field" :field="field" :label="t('Downloads.'+key)" :sort="sort" :direction="direction" @sort="changeSort" /></div>
            <p v-if="!rows.length" class="empty-state">{{t('Downloads.none')}}</p>
            <div v-else class="table-wrap"><table class="table is-fullwidth is-hoverable responsive-table" id="dl-table">
                <thead><tr><th class="col-check"><input type="checkbox" id="dl-select-all" :aria-label="t('UI.select_all')" :checked="ids.length > 0 && ids.every(id=>selected.includes(id))" :indeterminate="ids.some(id=>selected.includes(id)) && !ids.every(id=>selected.includes(id))" @change="toggleAll($event,ids)"></th>
                <th><SortLink field="name" :label="t('Downloads.filename')" :sort="sort" :direction="direction" @sort="changeSort" /></th><th class="has-text-centered">{{t('Downloads.sources')}}</th>
                <th class="col-status"><SortLink field="status" :label="t('Downloads.statuss')" :sort="sort" :direction="direction" @sort="changeSort" /></th>
                <th><SortLink field="done" :label="t('Downloads.progress')" :sort="sort" :direction="direction" @sort="changeSort" /></th>
                <th><SortLink field="pdl" :label="t('Downloads.pdl')" :sort="sort" :direction="direction" @sort="changeSort" /></th><th class="col-speed">{{t('Downloads.speed')}}</th><th></th></tr></thead>
                <tbody><tr v-for="row in rows" :key="row.id" :id="'dl-'+row.id" :data-status="row.status" :class="{'is-selected':selected.includes(row.id)}">
                    <td class="col-check"><input class="dl-check" type="checkbox" :checked="selected.includes(row.id)" :aria-label="row.name" @click="choose(row,$event)"></td>
                    <td class="col-name" :data-label="t('Downloads.filename')"><div class="dl-name" :title="row.name">{{row.name}}</div><div v-if="row.target" class="dl-meta dl-target"><Icon name="folder" /><span>{{row.target}}</span></div><div v-if="row.part !== null" class="dl-meta">{{t('Downloads.part')}}: {{row.part}}</div></td>
                    <td class="has-text-centered" :data-label="t('Downloads.sources')"><a :href="url('dl_users',{dl_id:row.id})">{{row.sources_queue+row.sources_active}}/{{row.sources_total}}</a></td>
                    <td class="col-status" :data-label="t('Downloads.statuss')"><span :class="'tag status-'+row.status">{{t('Downloads.state.'+row.status)}}</span></td>
                    <td class="col-progress" :data-label="t('Downloads.progress')"><div class="progress-line"><strong>{{row.percent}}%</strong><span class="dl-meta">{{bytes(row.loaded)}} / {{bytes(row.size)}}</span></div><progress class="progress is-small" :value="row.percent" max="100"></progress></td>
                    <td class="has-text-centered" :data-label="t('Downloads.pdl')">{{row.pdl}}</td><td class="col-speed" :data-label="t('Downloads.speed')"><span>{{speed(row.speed)}}</span><div class="dl-meta dl-eta">{{eta(row.rest,row.speed)}}</div></td>
                    <td class="col-actions"><button class="app-icon-button is-small" :aria-label="t('UI.actions')" :aria-expanded="menu === row.id" @click="showMenu(row,$event)"><Icon name="three-dots-vertical" /></button>
                    <div v-if="menu === row.id" class="dropdown-menu table-dropdown-menu" style="display:block" :style="menuStyle"><div class="dropdown-content"><button class="dropdown-item" @click="open('rename',row)"><Icon name="pencil" />{{t('Downloads.rename')}}</button><button class="dropdown-item" @click="open('target',row)"><Icon name="folder" />{{t('Downloads.target')}}</button><a class="dropdown-item" :href="url('dl_users',{dl_id:row.id})"><Icon name="people" />{{t('Downloads.sources_show')}}</a><a class="dropdown-item" :href="url('dl_parts',{dl_id:row.id})"><Icon name="bar-chart" />{{t('Downloads.parts_show')}}</a><button class="dropdown-item" @click="menu=null">{{t('UI.close')}}</button></div></div></td>
                </tr></tbody>
            </table></div>
        </section>
        <Modal v-if="dialog" :title="t('Downloads.'+dialog+'_title')" :danger="dialog === 'cancel'" :fit="dialog === 'cancel'" @close="dialog=''">
            <ul v-if="dialog === 'cancel'" class="cancel-list"><li v-for="row in data.items.filter(row=>actionIds.includes(row.id))" :key="row.id">{{row.name}}</li></ul>
            <template v-else><label class="label" for="action-value">{{t('Downloads.'+dialog+'_label')}}</label><input class="input" id="action-value" v-model="value" @keydown.enter="act(dialog === 'rename' ? 'renamedownload' : 'settargetdir',value,actionIds)"></template>
            <template #footer><button class="button" :class="dialog === 'cancel' ? 'is-danger' : 'is-primary'" :disabled="busy" @click="act(dialog === 'cancel' ? 'canceldownload' : dialog === 'rename' ? 'renamedownload' : 'settargetdir',value,actionIds)">{{dialog === 'cancel' ? t('Downloads.cancel_confirm') : t('UI.ok')}}</button></template>
        </Modal>
    </div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

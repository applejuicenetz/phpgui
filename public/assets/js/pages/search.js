import { ref, computed, watch, onUnmounted } from '../vue.js';
import { useData } from '../polling.js';
import { t, state, mutate, notify, rememberGet, rememberSet } from '../store.js';
import { bytes, relInfo, safeUrl } from '../format.js';
import { Icon, SortLink, selection } from '../components/common.js';
export default {
    components: { Icon, SortLink },
    setup() {
        const source = useData('search'), term = ref(''), filter = ref(rememberGet('aj_search_name_filter')), active = ref(state.route.searchid || 'all'), sort = ref('name'), direction = ref('asc'), busy = ref(false);
        const select = selection();
        const rows = computed(() => (source.data.value?.entries || []).filter(r => (active.value === 'all' || r.search === active.value) && r.name.toLowerCase().includes(filter.value.toLowerCase())).sort((a,b) => {
            const result = sort.value === 'name' ? a.name.localeCompare(b.name) : sort.value === 'size' ? a.size-b.size : a.sources-b.sources;
            return direction.value === 'asc' ? result : -result;
        }));
        const ids = computed(() => rows.value.map(r=>r.id));
        const current = computed(() => source.data.value?.searches.find(s=>s.id === active.value));
        watch(filter,value=>rememberSet('aj_search_name_filter',value));
        watch(source.data, data => { select.prune(data.entries.map(r=>r.id)); if(active.value !== 'all' && !data.searches.some(s=>s.id === active.value)) active.value='all'; });
        const linkRow = ref(null), linkStyle = ref({});
        // Same shape as the Java GUI: base link, then |ip:port[:serverHost:serverPort]/.
        function sourceLink(link) {
            const own = source.data.value?.source;
            if (!own?.ip || !own.port) return '';
            return link.replace(/\/$/, '') + '|' + own.ip + ':' + own.port + (own.server_host ? ':' + own.server_host + ':' + own.server_port : '') + '/';
        }
        function showLink(row, event) {
            if (linkRow.value?.id === row.id) { linkRow.value = null; return; }
            const rect = event.currentTarget.getBoundingClientRect(), width = Math.min(520, window.innerWidth - 16);
            const below = window.innerHeight - rect.bottom > 190;
            linkStyle.value = { position: 'fixed', width: width + 'px', left: Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8)) + 'px', ...(below ? { top: rect.bottom + 4 + 'px' } : { bottom: window.innerHeight - rect.top + 4 + 'px' }) };
            linkRow.value = row;
        }
        const closeLink = () => { linkRow.value = null; };
        const outside = event => { if (linkRow.value && !event.target.closest('.link-popover, .link-popover-trigger')) closeLink(); };
        const escape = event => { if (event.key === 'Escape') closeLink(); };
        document.addEventListener('click', outside); document.addEventListener('keydown', escape); window.addEventListener('resize', closeLink); document.addEventListener('scroll', closeLink, true);
        onUnmounted(() => { document.removeEventListener('click', outside); document.removeEventListener('keydown', escape); window.removeEventListener('resize', closeLink); document.removeEventListener('scroll', closeLink, true); });
        function changeSort(field) { direction.value=sort.value===field ? direction.value==='asc'?'desc':'asc' : field==='name'?'asc':'desc'; sort.value=field; }
        async function action(action,id='') { busy.value=true; const result=await mutate('search',{action,id,searchstring:term.value}); busy.value=false; if(result){term.value='';await source.refresh();} }
        async function download(rows) { busy.value=true; const result=await mutate('links',{ajfsp_link:rows.map(r=>r.link).join('\n')}); busy.value=false; if(result){ select.selected.value=[]; notify(t('Downloads.get_start')+' '+result.results.filter(r=>r.ok).length,'success'); } }
        return {...source,term,filter,active,sort,direction,busy,rows,ids,current,selected:select.selected,toggle:select.toggle,all:select.all,changeSort,action,download,linkRow,linkStyle,sourceLink,showLink,t,bytes,relInfo,safeUrl};
    },
    template:`<div id="search-root" v-if="data"><section class="box"><form class="field has-addons search-form" @submit.prevent="action('start')"><div class="control is-expanded has-icons-left"><input class="input" type="search" v-model="term" :placeholder="t('Search.placeholder')" :aria-label="t('Search.placeholder')" required><span class="icon is-left"><Icon name="search" /></span></div><div class="control"><button class="button is-primary" :disabled="busy">{{t('Search.button')}}</button></div></form></section>
    <div class="search-tabs"><div class="tabs is-boxed search-tabs-scroll"><ul role="tablist"><li :class="{'is-active':active==='all'}"><a href="#" role="tab" :aria-selected="active==='all'" @click.prevent="active='all'">{{t('Search.all')}} <span class="tag is-rounded is-link">{{data.total}}</span></a></li><li v-for="s in data.searches" :key="s.id" :class="{'is-active':active===s.id}"><a href="#" role="tab" :aria-selected="active===s.id" @click.prevent="active=s.id"><span class="search-term">{{s.text}}</span><span class="tag is-rounded is-link">{{s.found}}</span></a></li></ul></div><button v-if="data.searches.length" class="button is-danger is-light is-small" :disabled="busy" @click="action('deleteall')"><Icon name="trash" />{{t('Search.delet')}}</button></div>
    <section class="box"><div class="search-toolbar"><button class="button is-primary" :disabled="!selected.length || busy" @click="download(data.entries.filter(r=>selected.includes(r.id)))"><Icon name="download" />{{t('Search.download_selected')}}</button><div class="field filter-field"><input class="input" type="search" v-model="filter" :placeholder="t('UI.filter_placeholder')" :aria-label="t('UI.filter')"></div><template v-if="current"><button class="button" :class="current.running?'is-warning':'is-danger'" :disabled="busy" @click="action(current.running?'cancel':'delete',current.id)">{{t(current.running?'Search.cancle_search':'Search.delet_search')}}</button><progress v-if="current.running && current.progress<100" class="progress is-success search-progress" :value="current.progress" max="100"></progress></template></div>
    <div class="sort-bar"><SortLink v-for="[field,key] in [['name','name'],['sources','sources'],['size','size']]" :key="field" :field="field" :label="t('Search.'+key)" :sort="sort" :direction="direction" @sort="changeSort" /></div>
    <div class="table-wrap"><table class="table is-fullwidth is-hoverable responsive-table" id="search-table"><thead><tr><th class="col-check"><input type="checkbox" :aria-label="t('UI.select_all')" :checked="ids.length>0 && ids.every(id=>selected.includes(id))" :indeterminate="ids.some(id=>selected.includes(id)) && !ids.every(id=>selected.includes(id))" @change="all($event,ids)"></th><th><SortLink field="name" :label="t('Search.name')" :sort="sort" :direction="direction" @sort="changeSort" /></th><th class="col-narrow"><SortLink field="sources" :label="t('Search.sources')" :sort="sort" :direction="direction" @sort="changeSort" /></th><th class="col-narrow"><SortLink field="size" :label="t('Search.size')" :sort="sort" :direction="direction" @sort="changeSort" /></th><th></th></tr></thead><tbody><tr v-for="row in rows" :key="row.id" :data-entry="row.id" :class="{'is-selected':selected.includes(row.id),'is-shared':row.shared,'is-downloading':row.downloading}"><td class="col-check"><input type="checkbox" :checked="selected.includes(row.id)" :aria-label="row.name" @click="toggle(row.id,$event,ids)"></td><td class="col-name" :data-label="t('Search.name')"><strong class="dl-name">{{row.name}}</strong><span v-if="row.shared" class="tag is-success is-light ml-2">{{t('Search.in_share')}}</span><span v-if="row.downloading" class="tag is-info is-light ml-2">{{t('Search.in_download')}}</span></td><td class="col-narrow has-text-centered" :data-label="t('Search.sources')">{{row.sources}}</td><td class="col-narrow" :data-label="t('Search.size')">{{bytes(row.size)}}</td><td class="col-actions"><a v-if="relInfo(row.link)" class="app-icon-button is-small" :href="safeUrl(relInfo(row.link))" target="_blank" rel="noopener noreferrer" :title="t('Search.info')"><Icon name="info-circle" /></a><button type="button" class="app-icon-button is-small link-popover-trigger" :title="t('Share.show_link')" :aria-label="t('Share.show_link')" :aria-expanded="linkRow?.id === row.id" @click="showLink(row,$event)"><Icon name="link-45deg" /></button><button class="button is-success is-small" :disabled="busy" :title="t('UI.download')" @click="download([row])"><Icon name="download" /></button></td></tr></tbody></table></div><div v-if="linkRow" class="box link-popover" role="dialog" :aria-label="t('Share.show_link')" :style="linkStyle"><label class="label is-small" for="link-plain">{{t('Share.link_plain')}}</label><input id="link-plain" class="input is-small" type="text" readonly :value="linkRow.link" @focus="$event.target.select()" @click="$event.target.select()"><template v-if="sourceLink(linkRow.link)"><label class="label is-small mt-3" for="link-source">{{t('Share.source_link')}}</label><input id="link-source" class="input is-small" type="text" readonly :value="sourceLink(linkRow.link)" @focus="$event.target.select()" @click="$event.target.select()"></template></div><p v-if="!rows.length" class="empty-state">{{t(data.searches.length?'Search.empty':'Search.none')}}</p></section></div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

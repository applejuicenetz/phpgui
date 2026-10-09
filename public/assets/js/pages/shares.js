import { ref, watch, computed, onUnmounted } from '../vue.js';
import { useData } from '../polling.js';
import { request } from '../api.js';
import { t, state, url, navigate, mutate, notify, handleError } from '../store.js';
import { bytes, date, relInfo, safeUrl } from '../format.js';
import { Icon, Modal, ShareTabs, selection } from '../components/common.js';

export default {
    components: { Icon, Modal, ShareTabs },
    setup() {
        const scoped = state.route.site === 'sharefiles', filter = ref(state.route.q || ''), directory = state.route.dir || '';
        const params = () => ({ ...(scoped ? {dir:directory} : {}), q:filter.value, page:state.route.page || 1 });
        const endpoint = scoped || filter.value ? 'files' : 'shares';
        const source = useData(endpoint, params, false), select = selection(), exported = ref(''), priority = ref(1), newPath = ref(''), recursive = ref(true), busy = ref(false), browsing = ref(false), browseData = ref({dir:'',entries:[]});
        const ids = computed(() => (source.data.value?.files || []).map(row=>row.id));
        let timer;
        function search() { navigate(scoped?'sharefiles':'shares', {...(scoped?{dir:directory}:{}),...(filter.value?{q:filter.value}:{})}, true); }
        watch(filter,()=>{clearTimeout(timer);timer=setTimeout(search,400);});
        onUnmounted(()=>clearTimeout(timer));
        watch(() => source.data.value?.files, files => select.prune((files || []).map(row => row.id)));
        watch(source.loading, loading => { if (!loading && filter.value) requestAnimationFrame(() => { const input = document.getElementById('share-filter'); input?.focus(); input?.setSelectionRange(filter.value.length, filter.value.length); }); });
        try { exported.value = sessionStorage.getItem('aj_share_export') || ''; } catch { /* Storage unavailable. */ }
        watch(exported, value => { try { sessionStorage.setItem('aj_share_export', value); } catch { /* Storage unavailable. */ } });
        async function action(action, fields={}) {
            busy.value=true;
            const result = await mutate('shares',{action,...fields});
            busy.value=false;
            if(result){notify(t('UI.saved'),'success');await source.refresh();}
        }
        async function fileAction(action) {
            busy.value=true;
            const result = await mutate('files',{action,sharefile:select.selected.value,prio:priority.value},params());
            busy.value=false;
            if(result){ if(action==='export') exported.value=result.links.join('\n'); else {notify(t('UI.saved'),'success');await source.refresh();} }
        }
        async function browse(path) {
            try {browseData.value=await request('directories',null,{dir:path});browsing.value=true;} catch(error){handleError(error);}
        }
        function remove(name) { if(window.confirm(t('Share.remove')+': '+name)) action('remove',{name}); }
        return {...source,scoped,directory,filter,search,ids,selected:select.selected,toggle:select.toggle,all:select.all,exported,priority,newPath,recursive,busy,browsing,browseData,action,fileAction,browse,remove,t,url,bytes,date,relInfo,safeUrl};
    },
    template:`<div v-if="data" :id="scoped ? 'sharefiles-root' : 'shares-root'"><ShareTabs />
    <p v-if="scoped" class="mb-3"><a class="button is-small" :href="url('shares')"><Icon name="arrow-left-short" />{{t('Navigation.shares')}}</a> <code class="dir-path">{{directory}}</code></p>
    <form class="field has-addons" id="share-search" role="search" @submit.prevent="search"><div class="control is-expanded"><input class="input" type="search" id="share-filter" v-model="filter" :placeholder="t('Share.filter_hint')" :aria-label="t('Share.filter_hint')" autocomplete="off" @keydown.esc="filter=''"></div><div class="control"><button class="button" :aria-label="t('UI.filter')"><Icon name="search" /></button></div><div v-if="filter" class="control"><button class="button" type="button" @click="filter=''" :aria-label="t('UI.cancel')"><Icon name="x-lg" /></button></div></form>
    <template v-if="data.files">
        <section v-if="exported" class="box"><h2 class="box-title">{{t('Share.link_export_title')}}</h2><textarea class="textarea" rows="8" readonly id="export-text" :value="exported"></textarea><button class="button mt-3" @click="exported=''">{{t('Share.delet_export')}}</button></section>
        <section class="box" id="sharefiles-form"><div class="toolbar sharefiles-toolbar"><div class="buttons mb-0"><button class="button" :disabled="busy" @click="fileAction('export')">{{t('Share.export')}}</button><button class="button" :aria-label="t('Share.refresh')" @click="refresh"><Icon name="arrow-clockwise" /></button></div><div class="field has-addons mb-0"><div class="control"><div class="select"><select v-model="priority" :aria-label="t('Share.prio')"><option v-for="n in 250" :key="n" :value="n">{{n}}</option></select></div></div><div class="control"><button class="button" :disabled="busy || !selected.length" @click="fileAction('priority')">{{t('Share.set_prio')}}</button></div></div></div>
        <ul class="dir-list"><li v-for="folder in data.folders" :key="folder.path" class="dir-row"><Icon name="folder-fill" class="dir-icon" /><a class="dir-name" :href="url('sharefiles',{dir:folder.path})">{{folder.name}}</a></li></ul>
        <p v-if="!data.files.length" class="empty-state">{{t('Share.no_files')}}</p><div v-else class="table-wrap"><table class="table is-fullwidth is-hoverable responsive-table"><thead><tr><th class="col-check"><input type="checkbox" :aria-label="t('UI.select_all')" :checked="ids.length>0 && ids.every(id=>selected.includes(id))" :indeterminate="ids.some(id=>selected.includes(id)) && !ids.every(id=>selected.includes(id))" @change="all($event,ids)"></th><th>{{t('Share.name')}}</th><th>{{t('Share.size')}}</th><th>{{t('Share.prio')}}</th><th></th></tr></thead><tbody><tr v-for="row in data.files" :key="row.id" :class="{'is-selected':selected.includes(row.id)}"><td class="col-check"><input type="checkbox" :aria-label="row.name" :checked="selected.includes(row.id)" @click="toggle(row.id,$event,ids)"></td><td class="col-name" :data-label="t('Share.name')"><div class="dl-name" :title="row.path">{{row.name}}</div><div v-if="filter" class="dl-meta">{{row.path}}</div><div class="dl-meta">ID {{row.id}} · {{t('Share.last_asked')}}: {{date(row.last)}} · {{t('Share.ask_count')}}: {{row.asked}} · {{t('Share.search_count')}}: {{row.searched}}</div></td><td class="is-nowrap" :data-label="t('Share.size')">{{bytes(row.size)}}</td><td :data-label="t('Share.prio')">{{row.priority}}</td><td class="col-actions"><a v-if="relInfo(row.link)" class="app-icon-button is-small" :href="safeUrl(relInfo(row.link))" target="_blank" rel="noopener noreferrer" :aria-label="t('Search.info')"><Icon name="info-circle" /></a><a class="app-icon-button is-small" :href="safeUrl(row.link)" :title="t('Share.source_link')"><Icon name="link-45deg" /></a></td></tr></tbody></table></div>
        <nav v-if="data.pages>1" class="pagination is-centered mt-4" :aria-label="t('UI.pagination')"><a v-if="data.page>1" class="pagination-previous" :href="url(scoped?'sharefiles':'shares',{...(scoped?{dir:directory}:{}),q:filter,page:data.page-1})">‹</a><a v-if="data.page<data.pages" class="pagination-next" :href="url(scoped?'sharefiles':'shares',{...(scoped?{dir:directory}:{}),q:filter,page:data.page+1})">›</a><span class="pagination-list dl-meta">{{data.page}} / {{data.pages}} · {{data.total}}</span></nav><p class="dl-meta mt-3">{{t('Share.prio_spent').replace('%spent',data.spent)}}</p></section>
    </template><template v-else>
        <section class="box"><button class="button is-primary mb-4" :disabled="busy" @click="action('check')"><Icon name="arrow-repeat" />{{t('Share.check')}}</button><ul class="dir-list"><li class="dir-row"><Icon name="folder-fill" class="dir-icon" /><a class="dir-name" :href="url('sharefiles',{dir:data.temp})">{{data.temp}}</a><span class="tag">{{t('Share.temp')}}</span></li><li v-for="dir in data.dirs" :key="dir.name" class="dir-row"><Icon name="folder-fill" class="dir-icon" /><a class="dir-name" :href="url('sharefiles',{dir:dir.name})">{{dir.name}}</a><div class="dir-controls"><label class="checkbox"><input type="checkbox" :checked="dir.subs" :disabled="busy" @change="action('toggle',{name:dir.name,subs:$event.target.checked?'1':'0'})"> {{t('Share.subs_toggle')}}</label><button class="button is-danger is-light is-small" :disabled="busy" :aria-label="t('Share.remove')" @click="remove(dir.name)"><Icon name="trash" /></button></div></li></ul></section>
        <section class="box"><h2 class="box-title">{{t('Share.shared_directories_new')}}</h2><form @submit.prevent="action('add',{name:newPath,subs:recursive?'1':'0'})"><div class="field has-addons"><div class="control is-expanded"><input class="input" id="share-new-path" v-model="newPath" :placeholder="t('Share.way')" :aria-label="t('Share.way')" required></div><div class="control"><button type="button" class="button" @click="browse(newPath)"><Icon name="folder" /><span class="action-text">{{t('Share.browse')}}</span></button></div></div><div class="field"><label class="checkbox"><input type="checkbox" v-model="recursive"> {{t('Share.with_subs')}}</label></div><button class="button is-primary" :disabled="busy"><Icon name="plus-lg" />{{t('Share.add')}}</button></form></section>
    </template>
    <Modal v-if="browsing" :title="t('Share.choose_dir')" @close="browsing=false"><p class="dir-current">{{browseData.dir || '/'}}</p><ul class="dir-list"><li v-for="dir in browseData.entries" :key="dir.path" class="dir-row"><button type="button" class="dir-name" @click="browse(dir.path)">{{dir.name}}</button></li></ul><template #footer><button class="button is-primary" @click="newPath=browseData.dir || '/';browsing=false">{{t('Share.choose')}}</button></template></Modal>
    </div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

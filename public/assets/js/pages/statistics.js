import { computed } from '../vue.js';
import { useData } from '../polling.js';
import { t, state, url } from '../store.js';
import { date, safeUrl } from '../format.js';
import { ShareTabs } from '../components/common.js';
export default {
    components: { ShareTabs },
    setup() {
        const source = useData('statistics',()=>({stats:state.route.stats || 'most'}),false);
        const modes = {'last':'last','-last':'nolast','most':'most','-most':'least','search':'search','-search':'nosearch'};
        const column = computed(()=>t('Share.stats.'+(source.data.value?.mode.includes('last')?'date':source.data.value?.mode.includes('search')?'searches':'requests')));
        return {...source,modes,column,t,url,date,safeUrl};
    },
    template:`<div v-if="data" id="sharestats-root"><ShareTabs /><div class="buttons has-addons share-stats-modes" role="group" :aria-label="t('Share.statistics')"><a v-for="(key,mode) in modes" :key="mode" class="button is-small" :class="{'is-link is-selected':data.mode===mode}" :href="url('sharestats',{stats:mode})">{{t('Share.stats.'+key)}}</a></div><p v-if="!data.rows.length" class="empty-state">{{t('UI.no_results')}}</p><div v-else class="table-wrap"><table class="table is-fullwidth is-hoverable responsive-table"><thead><tr><th>#</th><th>{{column}}</th><th>{{t('Share.stats.file')}}</th></tr></thead><tbody><tr v-for="(row,i) in data.rows" :key="row.link"><td data-label="#"><strong>{{i+1}}.</strong></td><td :data-label="column">{{data.mode.includes('last') ? date(row.value) : row.value}}</td><td class="col-name" :data-label="t('Share.stats.file')"><a :href="safeUrl(row.link)">{{row.name}}</a></td></tr></tbody></table></div></div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

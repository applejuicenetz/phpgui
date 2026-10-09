import { ref, computed } from '../vue.js';
import { useData } from '../polling.js';
import { t } from '../store.js';
import { bytes, speed, directImage } from '../format.js';
import { SortLink, SpeedPanel } from '../components/common.js';
export default {
    components: { SortLink, SpeedPanel },
    setup() {
        const source = useData('uploads'), sort = ref('status'), direction = ref('asc');
        const rows = computed(() => (source.data.value?.items || []).slice().sort((a,b) => {
            const states = ['active','queue','connecting','failed','unknown'];
            const result = sort.value === 'name' ? a.name.localeCompare(b.name) : states.indexOf(a.status)-states.indexOf(b.status);
            // Tie-breakers keep row order stable between polls.
            const stable = result || a.name.localeCompare(b.name) || String(a.id).localeCompare(String(b.id));
            return direction.value === 'asc' || !result ? stable : -result;
        }));
        function changeSort(field) { direction.value = sort.value === field && direction.value === 'asc' ? 'desc' : 'asc'; sort.value = field; }
        return {...source,rows,sort,direction,changeSort,t,bytes,speed,directImage};
    },
    template: `<div v-if="data" id="ul-root"><SpeedPanel kind="ul" :data="data" /><section class="box"><p v-if="!rows.length" class="empty-state">{{t('Uploads.none')}}</p><template v-else><div class="sort-bar"><SortLink v-for="field in ['name','status']" :key="field" :field="field" :label="t(field === 'name' ? 'Uploads.files' : 'Uploads.statuss')" :sort="sort" :direction="direction" @sort="changeSort" /></div><div class="table-wrap"><table class="table is-fullwidth is-hoverable responsive-table" id="ul-table"><thead><tr><th></th><th><SortLink field="name" :label="t('Uploads.files')" :sort="sort" :direction="direction" @sort="changeSort" /></th><th><SortLink field="status" :label="t('Uploads.statuss')" :sort="sort" :direction="direction" @sort="changeSort" /></th><th>{{t('Uploads.progress')}}</th><th>{{t('Uploads.speed')}}</th></tr></thead><tbody><tr v-for="row in rows" :key="row.id" :id="'ul-'+row.id"><td class="col-check"><img class="inline-icon" :src="directImage(row.direct)" alt="" width="16" height="16"></td><td class="col-name" :data-label="t('Uploads.files')"><div class="dl-name" :title="row.name">{{row.name}}</div><div class="dl-meta">{{t('Uploads.username')}}: {{row.nick}} · {{t('Uploads.pdl')}}: {{row.pdl === null ? '' : '('+row.pdl+') '}}{{row.priority}}</div></td><td :data-label="t('Uploads.statuss')"><span :class="'tag ul-'+row.status">{{t('Uploads.ul_status.status_'+row.status_code)}}</span></td><td class="col-progress" :data-label="t('Uploads.progress')"><div class="progress-line"><strong>{{row.active ? row.percent+'%' : Math.floor(row.age/60)+'min '+String(row.age%60).padStart(2,'0')+'s'}}</strong><span class="dl-meta">{{row.done === null ? '' : bytes(row.done)+' / '}}{{bytes(row.span)}}</span></div><progress class="progress is-success is-small" :value="row.percent" max="100"></progress></td><td :data-label="t('Uploads.speed')">{{speed(row.speed)}}</td></tr></tbody></table></div></template></section></div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

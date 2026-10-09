import { ref } from '../vue.js';
import { useData } from '../polling.js';
import { t, mutate, notify } from '../store.js';
import { date } from '../format.js';
import { Icon } from '../components/common.js';
export default {
    components: { Icon },
    setup() {
        const source=useData('servers'),busy=ref(false);
        const labels={connected:'connectet',trying:'try_connect',been:'been_connected',none:'no_con'};
        const tones={connected:'has-text-success',trying:'has-text-danger',been:'has-text-warning',none:'has-text-grey'};
        async function action(action,id=0,name='') { if(action==='removeserver' && !window.confirm(t('Server.delet')+': '+name)) return; busy.value=true;const result=await mutate('servers',{action,serv_id:id});busy.value=false;if(result){notify(t('UI.saved'),'success');await source.refresh();} }
        return {...source,busy,labels,tones,action,t,date};
    },
    template:`<div v-if="data"><div class="toolbar mb-4"><button class="button is-primary" :disabled="busy" @click="action('getservers')"><Icon name="download" />{{t('Server.add_more')}}</button></div><p v-if="!data.items.length" class="empty-state box">{{t('Server.none')}}</p><div v-else class="card-grid"><article v-for="row in data.items" :key="row.id" class="box server-card" :data-state="row.state"><header class="server-head"><h2 class="title is-6">{{row.name}}</h2><span class="server-state" :class="tones[row.state]"><Icon :name="row.state==='none'?'wifi-off':'wifi'" />{{t('Server.'+labels[row.state])}}</span></header><dl class="kv"><div><dt>{{t('Server.host')}}</dt><dd>{{row.host}}</dd></div><div><dt>{{t('Server.port')}}</dt><dd>{{row.port}}</dd></div><div><dt>{{t('Server.last_connection')}}</dt><dd>{{row.lastseen>0?date(row.lastseen):t('Server.not_yet')}}</dd></div></dl><footer class="buttons"><button class="button is-danger is-light is-small" :disabled="busy" @click="action('removeserver',row.id,row.name)"><Icon name="trash" />{{t('Server.delet')}}</button><button v-if="row.can_login" class="button is-link is-light is-small" :disabled="busy" @click="action('serverlogin',row.id)"><Icon name="box-arrow-in-right" />{{t('Server.login')}} ({{row.tries}})</button><span v-else class="tag">{{t('Server.login')}} ({{row.tries}})</span></footer></article></div></div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

import { ref } from '../vue.js';
import { t, mutate, notify, navigate } from '../store.js';
import { ajlToLinks } from '../ajl.js';
import { Modal, Icon } from './common.js';
export default {
    components:{Modal,Icon},props:['initial'],emits:['close'],
    setup(props,{emit}){
        const text=ref(props.initial || ''),target=ref(''),note=ref(''),busy=ref(false);
        async function file(event){const file=event.target.files[0];if(!file)return;const links=ajlToLinks(await file.text());note.value=links.length?t('UI.add_links_loaded').replace('{n}',links.length):t('UI.add_links_invalid');if(links.length)text.value=(text.value.trim()?text.value.trim()+'\n':'')+links.join('\n');event.target.value='';}
        async function submit(){busy.value=true;const result=await mutate('links',{ajfsp_link:text.value,ajfsp_target:target.value});busy.value=false;if(result){for(const r of result.results)notify(r.ok?t('Downloads.get_start')+' '+(r.link.name || r.link.host):t('UI.core_said').replace('{text}',r.reply),r.ok?'success':'warning');emit('close');navigate(result.results.some(r=>r.link.type==='file')?'downloads':'server');}}
        return {text,target,note,busy,file,submit,t};
    },
    template:`<Modal :title="t('UI.add_links')" @close="$emit('close')"><form id="links-form" @submit.prevent="submit"><div class="field"><label class="label" for="ajfsp-link-input">{{t('UI.add_links_label')}}</label><textarea class="textarea" id="ajfsp-link-input" v-model="text" rows="4" placeholder="ajfsp://file|…" required></textarea></div><div class="field"><label class="label" for="ajfsp-target-input">{{t('UI.add_links_target')}}</label><input class="input" id="ajfsp-target-input" v-model="target" maxlength="255" :placeholder="t('UI.add_links_target_hint')"></div><div class="field"><label class="label" for="ajfsp-file-input">{{t('UI.add_links_file')}}</label><input class="input" type="file" id="ajfsp-file-input" accept=".ajl" @change="file"><p v-if="note" class="help">{{note}}</p></div></form><template #footer><button class="button is-primary" form="links-form" :disabled="busy"><Icon name="download" />{{t('UI.add_links_submit')}}</button></template></Modal>`
};

import { computed } from '../vue.js';
import { useData } from '../polling.js';
import { t,state,url } from '../store.js';
import { Icon } from '../components/common.js';

// Pure SVG geometry from JSON byte ranges. No backend-rendered image/HTML.
export function rectangles(map) {
    const size=map.size, ranges=[], parts=map.parts, block=1048576;
    if(!size || !Number.isFinite(size)) return [];
    const checked=(start,end)=>{const left=start===0 && end>=size?0:Math.ceil(start/block)*block,right=end>=size?size:Math.floor(end/block)*block;if(right>left)ranges.push([left,right,'#00ff00']);};
    let received=null;
    parts.forEach((part,i)=>{
        const end=parts[i+1]?.start ?? size, type=part.type, shade=250-25*Math.min(10,Math.max(1,type));
        ranges.push([part.start,end,type===-1?'#000000':type===0?'#ff0000':`rgb(${shade},${shade},255)`]);
        if(map.download){if(type===-1)received ??= part.start;else if(received!==null){checked(received,part.start);received=null;}}
    });
    if(received!==null)checked(received,size);
    for(const transfer of map.transfers){if(transfer.start<0 || transfer.end<=transfer.start)continue;const percent=Math.max(0,Math.min(10,Math.floor((transfer.position-transfer.start)/(transfer.end-transfer.start)*10))),shade=255-12*percent;ranges.push([transfer.start,transfer.end,`rgb(${shade},${shade},0)`]);}
    const result=[];
    for(let [start,end,color] of ranges){start=Math.max(0,Math.min(size,start));end=Math.max(start,Math.min(size,end));for(let row=0;row<14;row++){const left=Math.max(start,size*row/14),right=Math.min(end,size*(row+1)/14);if(right>left)result.push({x:(left-size*row/14)/(size/14)*500,y:row*15,width:(right-left)/(size/14)*500,color});}}
    return result;
}
export default {
    components:{Icon},
    setup(){const source=useData('parts',()=>state.route.dl_id?{dl_id:state.route.dl_id}:{usr_id:state.route.usr_id});return {...source,rects:computed(()=>source.data.value?rectangles(source.data.value):[]),t,url};},
    template:`<div v-if="data"><p class="mb-3"><a class="button is-small" :href="url(data.download?'downloads':'dl_users',data.download?{}:{dl_id:data.download_id})"><Icon name="arrow-left-short" />{{t('Downloads.back')}}</a></p><section class="box"><h2 class="box-title">{{data.heading}}</h2><ul class="legend"><li v-for="[key,color] in [['available','#0000ff'],['NA','#ff0000'],['received','#000000'],['checked','#00ff00'],['active_transfer','#ffff00']]" :key="key"><span class="swatch" :style="{'--sw':color}"></span>{{t('Downloads.parts.'+key)}}</li></ul><div class="parts-image-wrap"><svg class="parts-image" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 210" role="img" :aria-label="data.heading"><rect width="500" height="210" fill="#c8c8c8"/><rect v-for="(r,i) in rects" :key="i" :x="r.x" :y="r.y" :width="r.width" height="14" :fill="r.color"/></svg></div></section></div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

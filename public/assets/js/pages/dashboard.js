import { useData } from '../polling.js';
import { t, url, state } from '../store.js';
import { bytes, speed, compactCount, duration, date, osImage } from '../format.js';
import { Icon } from '../components/common.js';
export default {
    components: { Icon },
    setup() { return { ...useData('dashboard'), t, url, state, bytes, speed, compactCount, duration, date, osImage }; },
    template: `<div v-if="data" class="columns is-multiline dashboard-columns">
        <div class="column is-12-tablet is-8-desktop"><section class="box"><h2 class="box-title"><Icon name="hdd-network" />{{t('Start.current_server')}}</h2><p class="title is-5 mb-2">{{data.server_name || t('Server.no_server')}}</p><div v-if="data.welcome" class="content" v-html="data.welcome"></div><p class="dl-meta has-text-right">{{t('Start.connected_since')}} {{duration(data.connected_since)}}</p></section>
        <div class="stat-grid"><a class="box stat-card" :href="url('downloads')"><span class="stat-icon has-background-warning has-text-warning-invert"><Icon class="is-large" name="cloud-download" /></span><span class="stat-body"><span class="stat-value">{{data.downloads_active}}/{{data.downloads_total}}</span><span class="stat-label">{{t('Start.active_downloads')}}</span></span></a>
        <a class="box stat-card" :href="url('uploads')"><span class="stat-icon has-background-link has-text-link-invert"><Icon class="is-large" name="cloud-upload" /></span><span class="stat-body"><span class="stat-value">{{data.uploads_active}}</span><span class="stat-label">{{t('Start.active_uploads')}}</span></span></a>
        <a class="box stat-card" :href="url('sharestats')"><span class="stat-icon has-background-warning has-text-warning-invert"><Icon class="is-large" name="diamond" /></span><span class="stat-body"><span class="stat-value" :class="{'has-text-danger':data.credits < 0}">{{bytes(data.credits)}}</span><span class="stat-label">{{t('Start.credits')}}</span></span></a>
        <a v-if="data.share" class="box stat-card" :href="url('shares')"><span class="stat-icon has-background-link has-text-link-invert"><Icon class="is-large" name="folder2-open" /></span><span class="stat-body"><span class="stat-value">{{bytes(data.share.size)}}</span><span class="stat-label">{{data.share.count.toLocaleString(state.session.language)}} {{t('Start.share_dat')}}</span></span></a></div>
        <section v-if="state.news" class="box news"><h2 class="box-title"><Icon name="newspaper" />{{t('UI.news')}}</h2><div class="content" v-html="state.news"></div></section></div>
        <div class="column is-12-tablet is-4-desktop"><section class="box"><h2 class="box-title"><Icon name="globe2" />{{t('Start.core_info')}}</h2><dl class="kv">
        <div><dt>{{t('Start.server_time')}}</dt><dd>{{date(data.time,true)}}</dd></div><div><dt>{{t('UI.core_version')}}</dt><dd>{{data.core_version}}</dd></div><div><dt>{{t('Start.op_system')}}</dt><dd><img class="inline-icon" :src="osImage(data.core_os_code)" alt="" width="16" height="16"> {{data.core_os}}</dd></div>
        </dl></section>
        <section class="box"><h2 class="box-title"><Icon name="diagram-3" />{{t('Start.network_info')}}</h2><dl class="kv"><div><dt>{{t('Start.open_connections')}}</dt><dd>{{data.connections}}<template v-if="data.max_connections > 0"> / {{data.max_connections}}</template></dd></div><div><dt>{{t('Start.download_speed')}}</dt><dd>{{speed(data.dl_speed)}}</dd></div><div><dt>{{t('Start.upload_speed')}}</dt><dd>{{speed(data.ul_speed)}}</dd></div><div><dt>{{t('Start.bytes_in')}}</dt><dd>{{bytes(data.session_dl)}}</dd></div><div><dt>{{t('Start.bytes_out')}}</dt><dd>{{bytes(data.session_ul)}}</dd></div><div><dt>{{t('Start.public_ip')}}</dt><dd>{{data.public_ip}}</dd></div></dl></section>
        <section class="box"><h2 class="box-title"><Icon name="people" />{{t('Start.community')}}</h2><dl class="kv"><div><dt>{{t('Start.shared_users')}}</dt><dd>{{data.users}}</dd></div><div><dt>{{t('Start.all_data')}}</dt><dd class="kv-nowrap">{{compactCount(data.filecount)}} ({{bytes(data.filesize)}})</dd></div></dl></section></div>
    </div><p v-else-if="loading" class="empty-state">{{t('UI.loading')}}</p>`
};

import { createApp, ref, computed, watch, onMounted, onUnmounted } from './vue.js';
import { request, setCsrf } from './api.js';
import { state, t, session, routeFromLocation, navigate, url, mutate, notify, handleError, forgetLogin } from './store.js';
import { bytes, speed, safeUrl } from './format.js';
import { useData } from './polling.js';
import { Icon, Modal } from './components/common.js';
import Login from './components/login.js';
import Links from './components/links.js';
import Dashboard from './pages/dashboard.js';
import Downloads from './pages/downloads.js';
import Uploads from './pages/uploads.js';
import Search from './pages/search.js';
import Shares from './pages/shares.js';
import Statistics from './pages/statistics.js';
import Servers from './pages/servers.js';
import Settings from './pages/settings.js';
import Sources from './pages/sources.js';
import Parts from './pages/parts.js';
import { ajlToLinks } from './ajl.js';

const pages = {start:Dashboard,downloads:Downloads,uploads:Uploads,search:Search,shares:Shares,sharefiles:Shares,sharestats:Statistics,server:Servers,settings:Settings,dl_users:Sources,dl_parts:Parts};
const Shell = {
    components: {Icon,Modal,Links},
    setup() {
        const status=useData('status'), sidebar=ref(false), dropdown=ref(''), dialog=ref(''), initialLinks=ref(''), shuttingDown=ref(false);
        watch(status.data,value=>{if(value)state.status=value;});
        const nav=[['start','speedometer2','dashboard'],['downloads','cloud-download','downloads'],['uploads','cloud-upload','uploads'],['search','search','search'],['shares','share','shares'],['server','hdd-network','server_list'],['settings','gear','settings']];
        const navSite=computed(()=>['dl_users','dl_parts'].includes(state.route.site)?'downloads':['sharefiles','sharestats'].includes(state.route.site)?'shares':state.route.site);
        const page=computed(()=>pages[state.route.site]);
        const pageKey=computed(()=>JSON.stringify(state.route));
        const title=computed(()=>t('System.pagetitle.'+state.route.site));
        async function logout(){const result=await mutate('session',{action:'logout'});if(result){forgetLogin();state.session=result;setCsrf(result.csrf);state.status={};navigate('start',{logout:1},true);}}
        async function shutdown(){shuttingDown.value=true;const result=await mutate('session',{action:'shutdown'});shuttingDown.value=false;if(result){dialog.value='';forgetLogin();state.session=result;setCsrf(result.csrf);navigate('start',{},true);}}
        function setTheme(theme){window.ajTheme.set(theme);dropdown.value='';}
        function closeMenus(event){if(!event.target.closest('.dropdown'))dropdown.value='';}
        function closeOnMove(){dropdown.value='';document.dispatchEvent(new Event('aj:close-menus'));}
        function key(event){if(event.key==='Escape'){dropdown.value='';sidebar.value=false;}}
        async function deliveredLinks(){
            const input=state.route.ajfsp_link || state.route.link;
            if(!input)return;
            const result=await mutate('links',{ajfsp_link:input});
            if(result){for(const r of result.results)notify(r.ok?t('Downloads.get_start')+' '+(r.link.name || r.link.host):t('UI.core_said').replace('{text}',r.reply),r.ok?'success':'warning');navigate(result.results.some(r=>r.link.type==='file')?'downloads':'server',{},true);}
        }
        onMounted(()=>{
            document.addEventListener('click',closeMenus);document.addEventListener('keydown',key);document.addEventListener('scroll',closeOnMove,true);window.addEventListener('resize',closeOnMove);
            deliveredLinks();
            request('news').then(data=>{state.news=data.html;state.newVersion=data.new_version;}).catch(()=>{/* Optional external feed. */});
            if(pendingFile){initialLinks.value=pendingFile;dialog.value='links';pendingFile='';}
            if('launchQueue' in window)window.launchQueue.setConsumer(async params=>{const file=await params.files[0]?.getFile();if(!file)return;initialLinks.value=ajlToLinks(await file.text()).join('\n');dialog.value='links';});
        });
        onUnmounted(()=>{document.removeEventListener('click',closeMenus);document.removeEventListener('keydown',key);document.removeEventListener('scroll',closeOnMove,true);window.removeEventListener('resize',closeOnMove);});
        return {state,t,url,nav,navSite,page,pageKey,title,sidebar,dropdown,dialog,initialLinks,shuttingDown,logout,shutdown,setTheme,speed,bytes,safeUrl};
    },
    template:`<a class="skip-link" href="#main">{{t('UI.skip')}}</a><div class="app"><aside class="app-sidebar" id="sidebar" :class="{'is-open':sidebar}" :aria-label="t('UI.page_of_nav')"><a class="app-brand" :href="url('start')"><img class="app-brand-logo" src="assets/img/apple-banner.jpg" alt="appleJuice" width="640" height="386"></a><nav><ul class="app-nav"><li v-for="[site,icon,label] in nav" :key="site"><a class="app-nav-link" :class="{'is-active':navSite===site}" :href="url(site)" :aria-current="navSite===site?'page':undefined" @click="sidebar=false"><Icon :name="icon"/><span class="app-nav-label">{{t('Navigation.'+label)}}</span><span v-if="['downloads','uploads'].includes(site) && state.status[site+'_active']>0" class="tag is-info is-rounded app-nav-badge" :data-badge="site">{{state.status[site+'_active']}}</span></a></li><li><a class="app-nav-link" :href="safeUrl(state.session.faq_url)" target="_blank" rel="noopener noreferrer"><Icon name="info-circle"/><span class="app-nav-label">{{t('Navigation.help')}}</span></a></li></ul></nav></aside><div class="app-scrim" :hidden="!sidebar" @click="sidebar=false"></div>
    <div class="app-main"><header class="app-topbar"><button class="app-icon-button app-menu-toggle" :aria-label="t('UI.menu')" :aria-expanded="sidebar" aria-controls="sidebar" @click="sidebar=!sidebar"><Icon name="list"/></button><h1 class="app-title">{{title}}</h1><div class="app-topbar-actions"><div class="app-transfer-speeds"><span :title="t('Start.download_speed')"><span id="aj-status-download">{{speed(state.status.download_speed)}}</span><Icon name="cloud-download"/></span><span :title="t('Start.upload_speed')"><span id="aj-status-upload">{{speed(state.status.upload_speed)}}</span><Icon name="cloud-upload"/></span></div><div class="app-credits" :class="{'is-negative':state.status.credits<0}"><Icon name="diamond"/><span><span id="aj-header-credits">{{bytes(state.status.credits)}}</span><small>{{t('UI.credits')}}</small></span></div><button class="app-icon-button" :aria-label="t('UI.add_links')" @click="initialLinks='';dialog='links'"><Icon name="plus-lg"/></button>
    <div class="dropdown is-right" :class="{'is-active':dropdown==='theme'}"><button class="app-icon-button" :aria-label="t('UI.theme')" :aria-expanded="dropdown==='theme'" @click="dropdown=dropdown==='theme'?'':'theme'"><Icon name="circle-half"/></button><div class="dropdown-menu"><div class="dropdown-content"><button v-for="[theme,icon] in [['light','sun'],['dark','moon-stars'],['auto','circle-half']]" :key="theme" class="dropdown-item" @click="setTheme(theme)"><Icon :name="icon"/>{{t('UI.theme_'+theme)}}</button></div></div></div>
    <div class="dropdown is-right" :class="{'is-active':dropdown==='user'}"><button class="app-icon-button" :aria-label="state.status.nick || t('Settings.nick')" :aria-expanded="dropdown==='user'" @click="dropdown=dropdown==='user'?'':'user'"><Icon name="person-circle"/></button><div class="dropdown-menu"><div class="dropdown-content"><div class="dropdown-item has-text-weight-semibold">{{state.status.nick}}</div><hr class="dropdown-divider"><a v-if="state.session.permalink" class="dropdown-item" :href="state.session.permalink">{{t('Navigation.permalink')}}</a><button class="dropdown-item" @click="logout">{{t('Navigation.logout')}}</button><button class="dropdown-item has-text-danger" @click="dropdown='';dialog='kick'">{{t('Navigation.kick_core')}}</button></div></div></div></div></header>
    <main id="main" class="app-content" tabindex="-1"><div v-if="state.newVersion" class="notification is-info is-light"><strong>{{t('System.version').replace('%version%',state.newVersion)}}</strong> <a href="https://github.com/applejuicenetz/phpgui/releases" target="_blank" rel="noopener noreferrer">{{t('System.version_akt')}}</a></div><div v-if="state.status.firewalled" class="notification is-danger is-light" role="alert"><strong>{{t('System.warning')}}</strong> {{t('System.firewall')}}</div><div v-if="state.status.connecting && state.route.site!=='server'" class="notification is-warning is-light"><strong>{{t('UI.connecting_title')}}</strong> {{t('UI.connecting_text')}}</div><component v-if="page" :is="page" :key="pageKey"/><section v-else-if="state.route.site==='help'" class="box"><a :href="safeUrl(state.session.faq_url)" target="_blank" rel="noopener noreferrer">{{t('Navigation.help')}} (FAQ)</a></section><section v-else class="box has-text-centered"><p class="title is-1">404</p><h2 class="title is-5">{{t('System.error404.title')}}</h2><p class="mb-4">{{t('System.error404.subtitle')}}</p><a class="button is-primary" :href="url('start')">{{t('UI.home')}}</a></section></main><footer class="app-footer"><span>{{t('UI.created_by')}} <Icon name="heart-fill"/> <b>kddk22</b> &amp; <b>red171</b>, {{t('UI.inspired_by')}} <b>UP</b></span><span class="has-text-weight-bold">v{{state.session.version}}</span></footer></div>
    <nav class="app-tabbar" :aria-label="t('UI.page_of_nav')"><a v-for="[site,icon,label] in nav.slice(0,4)" :key="site" class="app-tab" :class="{'is-active':navSite===site}" :href="url(site)"><span class="app-tab-icon"><Icon :name="icon"/><span v-if="state.status[site+'_active']>0" class="tag is-danger is-rounded app-tab-badge">{{state.status[site+'_active']}}</span></span><span class="app-tab-label">{{t('Navigation.'+label)}}</span></a><button class="app-tab" :aria-expanded="sidebar" aria-controls="sidebar" @click="sidebar=!sidebar"><span class="app-tab-icon"><Icon name="list"/></span><span class="app-tab-label">{{t('UI.more')}}</span></button></nav></div>
    <Links v-if="dialog==='links'" :initial="initialLinks" @close="dialog=''"/><Modal v-if="dialog==='kick'" :title="t('UI.kill_core_title')" danger @close="dialog=''"><p>{{t('UI.kill_core_text')}}</p><template #footer><button class="button is-danger" :disabled="shuttingDown" @click="shutdown">{{t('UI.kill_core_confirm')}}</button></template></Modal>`
};
let pendingFile='';
if('launchQueue' in window)window.launchQueue.setConsumer(async params=>{const file=await params.files[0]?.getFile();if(file)pendingFile=ajlToLinks(await file.text()).join('\n');});
if(location.protocol==='https:' && navigator.registerProtocolHandler){try{navigator.registerProtocolHandler('web+ajfsp',new URL('index.php?ajfsp_link=%s',document.baseURI).href);}catch{/* Not supported by every browser. */}}

const App={
    components:{Login,Shell},
    setup(){
        const bootError=ref(false);
        async function boot(){try{await session();if(state.session.authenticated && new URLSearchParams(location.search).has('l')) state.session.authenticated=false;routeFromLocation();bootError.value=false;}catch{bootError.value=true;}}
        watch(()=>state.session?.authenticated,authenticated=>{document.body.classList.toggle('login-page',!authenticated);},{immediate:true});
        function authenticated(){
            const params=Object.fromEntries(new URLSearchParams(location.search));
            if(params.ajfsp_link || params.link) navigate(params.site || 'start',{...(params.ajfsp_link?{ajfsp_link:params.ajfsp_link}:{}),...(params.link?{link:params.link}:{})},true);
            else navigate(params.site || 'start',{},true);
        }
        onMounted(boot);
        return {state,t,bootError,boot,authenticated};
    },
    template:`<template v-if="state.session"><Shell v-if="state.session.authenticated"/><Login v-else @authenticated="authenticated"/><div class="toast-host" aria-live="polite"><div v-for="alert in state.alerts" :key="alert.id" class="notification is-light app-toast" :class="'is-'+alert.level"><button class="delete" :aria-label="t('UI.close')" @click="state.alerts=state.alerts.filter(item=>item.id!==alert.id)"></button>{{alert.text}}</div></div></template><main v-else-if="bootError" class="login-card box" role="alert"><p>API connection failed.</p><button class="button" @click="boot">Retry</button></main>`
};
window.addEventListener('popstate',routeFromLocation);
document.addEventListener('click',event=>{
    const link=event.target.closest('a[href]');
    if(!link || event.defaultPrevented || event.button!==0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target || link.hasAttribute('download'))return;
    const target=new URL(link.href,location.href);
    if(target.origin!==location.origin || !['', 'index.php','index.html'].includes(target.pathname.split('/').at(-1)) || !target.searchParams.has('site'))return;
    event.preventDefault();history.pushState(null,'',target.href);routeFromLocation();
});
createApp(App).mount('#app');

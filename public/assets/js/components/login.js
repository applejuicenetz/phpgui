import { ref, computed, onMounted } from '../vue.js';
import { request, setCsrf } from '../api.js';
import { state, t, errorText, forgetLogin, rememberSet } from '../store.js';
import { Icon } from './common.js';
import { safeUrl } from '../format.js';

export default {
    components:{Icon},emits:['authenticated'],
    setup(props,{emit}) {
        const host=ref(state.session.default_host),password=ref(''),remember=ref(state.session.remember_login === true),error=ref(''),busy=ref(false);
        const hostHelp=ref(false),passwordHelp=ref(false);
        const passwordInput=ref(null);
        async function login(fields=null) {
            busy.value=true; error.value='';
            try {
                const result=await request('session',fields || {action:'login',host:host.value,cpass:password.value,remember_login:remember.value?'1':'0'});
                state.session=result;setCsrf(result.csrf);
                if(result.remember)rememberSet('aj_remember_login',JSON.stringify(result.remember));else forgetLogin();
                password.value='';emit('authenticated');
            } catch(err){error.value=errorText(err);forgetLogin();} finally{busy.value=false;}
        }
        onMounted(()=>{
            passwordInput.value?.focus();
            const params=new URLSearchParams(location.search);
            if(params.has('logout')) {forgetLogin();return;}
            if(params.has('l')) {login({action:'login',l:params.get('l')});return;}
            try {const saved=JSON.parse(localStorage.getItem('aj_remember_login') || 'null');if(saved && /^https?:$/.test(new URL(saved.url).protocol) && /^[a-f0-9]{32}$/i.test(saved.md5)){host.value=saved.url;password.value=saved.md5;remember.value=true;login();}}catch{/* Unavailable or invalid storage never blocks manual login. */}
        });
        // Links waiting for the login: ajfsp:// from the URL or an opened .ajl file.
        const pendingCount=computed(()=>{
            // Same tolerance as LinkProcessor::parse(): decode once more, accept web+ prefix and encoded pipes.
            const decode=text=>{try{return decodeURIComponent(String(text||'').replace(/\+(?=ajfsp:)/gi,'%2B'));}catch{return String(text||'');}};
            const count=text=>(decode(text).match(/ajfsp:\/\/(?:file\|[^|]*\|[a-f0-9]{32}\||server\|[^|]*\|\d{1,5}\/)/gi)||[]).length;
            return count(state.route?.ajfsp_link)+count(state.route?.link)+count(state.pendingFile);
        });
        const pendingText=computed(()=>t(pendingCount.value===1?'Login.pending_one':'Login.pending_many').replace('{count}',pendingCount.value));
        return {pendingCount,pendingText,state,host,password,passwordInput,remember,error,busy,hostHelp,passwordHelp,login,t,safeUrl};
    },
    template:`<main class="login-card box"><h1 class="title is-4 has-text-centered login-title">{{t('Login.title')}}</h1><div v-if="pendingCount" id="login-pending" class="notification is-info is-light" role="status">{{pendingText}}</div><div v-if="error" class="notification is-danger is-light" role="alert">{{error}}</div><form id="login_form" @submit.prevent="login()"><div class="field has-addons"><p class="control"><label class="button is-static" for="chost">{{t('UI.login_core_url')}}</label></p><p class="control is-expanded"><input class="input" type="url" id="chost" v-model="host" autocapitalize="off" autocorrect="off" spellcheck="false" enterkeyhint="next" required autocomplete="url"></p><p class="control"><button type="button" class="button" :aria-label="t('Login.url_info')" tabindex="-1" :aria-expanded="hostHelp" aria-controls="login-host-help" @click="hostHelp=!hostHelp"><Icon name="info-circle" /></button></p></div><p id="login-host-help" v-show="hostHelp" class="help mb-4">{{t('Login.url_info')}} <span class="tag">http://localhost:9851</span></p><div class="field has-addons"><p class="control"><label class="button is-static" for="cpass">{{t('Login.password')}}</label></p><p class="control is-expanded"><input class="input" type="password" id="cpass" ref="passwordInput" v-model="password" enterkeyhint="go" autocomplete="current-password"></p><p class="control"><button type="button" class="button" :aria-label="t('Login.password_info')" tabindex="-1" :aria-expanded="passwordHelp" aria-controls="login-password-help" @click="passwordHelp=!passwordHelp"><Icon name="info-circle" /></button></p></div><p id="login-password-help" v-show="passwordHelp" class="help mb-4">{{t('Login.password_info')}}</p><div class="field"><label class="login-remember"><input type="checkbox" v-model="remember"><span class="login-remember-switch" aria-hidden="true"></span><span>{{t('Login.remember')}}</span></label></div><button class="button is-primary is-fullwidth" :disabled="busy"><Icon name="box-arrow-in-right" />{{t('Login.login')}}</button></form></main><a v-if="state.session.repository_url" class="tag login-version" tabindex="-1" :href="safeUrl(state.session.repository_url)" target="_blank" rel="noopener noreferrer">v{{state.session.version}}</a>`
};

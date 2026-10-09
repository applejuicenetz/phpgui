import { ref, onMounted } from '../vue.js';
import { request, setCsrf } from '../api.js';
import { state, t, errorText, forgetLogin, rememberSet } from '../store.js';
import { Icon } from './common.js';
export default {
    components:{Icon},emits:['authenticated'],
    setup(props,{emit}) {
        const host=ref(state.session.default_host),password=ref(''),remember=ref(false),error=ref(''),busy=ref(false);
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
            const params=new URLSearchParams(location.search);
            if(params.has('logout')) {forgetLogin();return;}
            if(params.has('l')) {login({action:'login',l:params.get('l')});return;}
            try {const saved=JSON.parse(localStorage.getItem('aj_remember_login') || 'null');if(saved && /^https?:$/.test(new URL(saved.url).protocol) && /^[a-f0-9]{32}$/i.test(saved.md5)){host.value=saved.url;password.value=saved.md5;remember.value=true;login();}}catch{/* Unavailable or invalid storage never blocks manual login. */}
        });
        return {state,host,password,remember,error,busy,login,t};
    },
    template:`<main class="login-card box"><h1 class="title is-4 has-text-centered login-title">{{t('Login.title')}}</h1><div v-if="error" class="notification is-danger is-light" role="alert">{{error}}</div><form id="login_form" @submit.prevent="login()"><div class="field has-addons"><p class="control"><label class="button is-static" for="chost">{{t('UI.login_core_url')}}</label></p><p class="control is-expanded"><input class="input" type="url" id="chost" v-model="host" required autocomplete="url"></p><p class="control"><button type="button" class="button" :title="t('Login.url_info')" :aria-label="t('Login.url_info')"><Icon name="info-circle" /></button></p></div><div class="field has-addons"><p class="control"><label class="button is-static" for="cpass">{{t('Login.password')}}</label></p><p class="control is-expanded"><input class="input" type="password" id="cpass" v-model="password" autocomplete="current-password"></p><p class="control"><button type="button" class="button" :title="t('Login.password_info')" :aria-label="t('Login.password_info')"><Icon name="info-circle" /></button></p></div><div class="field"><label class="login-remember"><input type="checkbox" v-model="remember"><span class="login-remember-switch" aria-hidden="true"></span><span>{{t('Login.remember')}}</span></label></div><button class="button is-primary is-fullwidth" :disabled="busy">{{t('Login.login')}}</button></form></main>`
};

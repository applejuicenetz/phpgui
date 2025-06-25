import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Bootstrap CSS (wird von CoreUI benötigt)
import 'bootstrap/dist/css/bootstrap.min.css'

// CoreUI CSS
import '@coreui/coreui/dist/css/coreui.min.css'

// CoreUI Vue
import CoreuiVue from '@coreui/vue'

// AppleJuice Utilities
import { LanguagePlugin, lang } from './utils/language.js'
import { colorModeManager, WEBUI_CONFIG } from './utils/config.js'

// Initialize color mode
colorModeManager.init()

const app = createApp(App)

// Install plugins
app.use(router)
app.use(CoreuiVue)
app.use(LanguagePlugin)

// Global properties
app.config.globalProperties.$config = WEBUI_CONFIG
app.config.globalProperties.$colorMode = colorModeManager

// Provide global instances
app.provide('config', WEBUI_CONFIG)
app.provide('colorMode', colorModeManager)
app.provide('language', lang)

// Mount app
app.mount('#app')

// Hide loading screen after Vue is mounted
setTimeout(() => {
  const loadingScreen = document.getElementById('loading-screen')
  if (loadingScreen) {
    loadingScreen.style.opacity = '0'
    setTimeout(() => {
      loadingScreen.style.display = 'none'
    }, 500)
  }
}, 1500) // Show loading for at least 1.5 seconds

console.log('🍎 AppleJuice WebUI initialized')
console.log('Language:', lang.getCurrentLanguage())
console.log('Theme:', colorModeManager.getPreferredTheme())

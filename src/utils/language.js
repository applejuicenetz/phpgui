/**
 * Language/Internationalization utilities for AppleJuice WebUI
 * Equivalent to PHP Language class functionality
 */

// German language pack (default)
const LANG_DE = {
  // General
  'app.title': 'AppleJuice WebUI',
  'app.subtitle': 'P2P File Sharing',
  'app.loading': 'Wird geladen...',
  'app.error': 'Fehler',
  'app.success': 'Erfolgreich',
  'app.warning': 'Warnung',
  'app.info': 'Information',
  
  // Navigation
  'nav.dashboard': 'Dashboard',
  'nav.downloads': 'Downloads',
  'nav.uploads': 'Uploads',
  'nav.search': 'Suche',
  'nav.server': 'Server',
  'nav.settings': 'Einstellungen',
  'nav.about': 'Über',
  'nav.logout': 'Abmelden',
  
  // Login
  'login.title': 'Anmeldung',
  'login.subtitle': 'Melden Sie sich bei Ihrem AppleJuice Core an',
  'login.server': 'Server-Adresse',
  'login.server.placeholder': 'z.B. 192.168.1.100:9854',
  'login.password': 'Passwort',
  'login.password.placeholder': 'Core-Passwort eingeben',
  'login.button': 'Anmelden',
  'login.connecting': 'Verbinde...',
  'login.error.title': 'Anmeldung fehlgeschlagen',
  'login.error.invalid': 'Ungültige Server-Adresse oder Passwort',
  'login.error.connection': 'Verbindung zum Server fehlgeschlagen',
  'login.error.timeout': 'Verbindungs-Timeout',
  'login.info.title': 'Verbindung',
  'login.info.description': 'Stellen Sie sicher, dass:',
  'login.info.core_running': 'Ihr AppleJuice Core läuft',
  'login.info.address_correct': 'Die Server-Adresse korrekt ist',
  'login.info.password_correct': 'Das Passwort stimmt',
  
  // Dashboard
  'dashboard.title': 'Dashboard',
  'dashboard.welcome': 'Willkommen im AppleJuice P2P Dashboard!',
  'dashboard.subtitle': 'Verwalte deine Downloads, Uploads und Einstellungen über diese moderne Web-Oberfläche.',
  'dashboard.refresh': 'Aktualisieren',
  'dashboard.loading': 'Lade Core-Daten...',
  'dashboard.error.loading': 'Fehler beim Laden der Daten',
  'dashboard.connected': 'Verbunden',
  'dashboard.status': 'Status',
  
  // Downloads
  'downloads.title': 'Downloads',
  'downloads.count': 'Anzahl',
  'downloads.active': 'Aktive Downloads',
  'downloads.completed': 'Abgeschlossen',
  'downloads.paused': 'Pausiert',
  'downloads.error': 'Fehler',
  'downloads.no_active': 'Keine aktiven Downloads',
  'downloads.filename': 'Dateiname',
  'downloads.size': 'Größe',
  'downloads.progress': 'Fortschritt',
  'downloads.speed': 'Geschwindigkeit',
  'downloads.eta': 'Verbleibende Zeit',
  'downloads.sources': 'Quellen',
  
  // Uploads
  'uploads.title': 'Uploads',
  'uploads.count': 'Anzahl',
  'uploads.active': 'Aktive Uploads',
  'uploads.no_active': 'Keine aktiven Uploads',
  'uploads.filename': 'Dateiname',
  'uploads.size': 'Größe',
  'uploads.speed': 'Geschwindigkeit',
  'uploads.uploaded': 'Hochgeladen',
  'uploads.clients': 'Clients',
  
  // Shared Files
  'shared.title': 'Geteilte Dateien',
  'shared.files': 'Dateien',
  'shared.folders': 'Ordner',
  'shared.total_size': 'Gesamtgröße',
  'shared.count': 'Anzahl',
  
  // Credits
  'credits.title': 'Credits',
  'credits.upload': 'Upload Credits',
  'credits.download': 'Download Credits',
  'credits.ratio': 'Verhältnis',
  
  // Server
  'server.title': 'Server',
  'server.connected': 'Verbundene Server',
  'server.users': 'Benutzer online',
  'server.status': 'Server Status',
  'server.ping': 'Ping',
  'server.version': 'Version',
  
  // Traffic
  'traffic.title': 'Traffic',
  'traffic.upload_total': 'Upload Total',
  'traffic.download_total': 'Download Total',
  'traffic.upload_speed': 'Upload Speed',
  'traffic.download_speed': 'Download Speed',
  'traffic.session': 'Session',
  'traffic.total': 'Gesamt',
  
  // Core Information
  'core.title': 'Core Informationen',
  'core.version': 'Core Version',
  'core.uptime': 'Betriebszeit',
  'core.network_id': 'Netzwerk ID',
  'core.ip': 'IP Adresse',
  'core.port': 'Port',
  'core.status': 'Status',
  'core.unknown': 'Unbekannt',
  
  // Settings
  'settings.title': 'Einstellungen',
  'settings.general': 'Allgemein',
  'settings.network': 'Netzwerk',
  'settings.downloads': 'Downloads',
  'settings.uploads': 'Uploads',
  'settings.interface': 'Benutzeroberfläche',
  'settings.save': 'Speichern',
  'settings.reset': 'Zurücksetzen',
  'settings.apply': 'Anwenden',
  
  // Search
  'search.title': 'Suche',
  'search.placeholder': 'Nach Dateien suchen...',
  'search.button': 'Suchen',
  'search.results': 'Suchergebnisse',
  'search.no_results': 'Keine Ergebnisse gefunden',
  'search.filename': 'Dateiname',
  'search.size': 'Größe',
  'search.sources': 'Quellen',
  'search.download': 'Download',
  
  // File operations
  'file.download': 'Herunterladen',
  'file.pause': 'Pausieren',
  'file.resume': 'Fortsetzen',
  'file.cancel': 'Abbrechen',
  'file.remove': 'Entfernen',
  'file.priority.high': 'Hoch',
  'file.priority.normal': 'Normal',
  'file.priority.low': 'Niedrig',
  
  // Time units
  'time.seconds': 'Sekunden',
  'time.minutes': 'Minuten',
  'time.hours': 'Stunden',
  'time.days': 'Tage',
  'time.weeks': 'Wochen',
  'time.months': 'Monate',
  'time.years': 'Jahre',
  'time.unknown': 'Unbekannt',
  
  // File sizes
  'size.bytes': 'Bytes',
  'size.kb': 'KB',
  'size.mb': 'MB',
  'size.gb': 'GB',
  'size.tb': 'TB',
  
  // Status messages
  'status.connecting': 'Verbinde...',
  'status.connected': 'Verbunden',
  'status.disconnected': 'Getrennt',
  'status.error': 'Fehler',
  'status.loading': 'Lade...',
  'status.ready': 'Bereit',
  'status.working': 'Arbeitet...',
  
  // Buttons
  'button.ok': 'OK',
  'button.cancel': 'Abbrechen',
  'button.save': 'Speichern',
  'button.delete': 'Löschen',
  'button.edit': 'Bearbeiten',
  'button.close': 'Schließen',
  'button.back': 'Zurück',
  'button.next': 'Weiter',
  'button.refresh': 'Aktualisieren',
  'button.retry': 'Wiederholen',
  
  // Messages
  'message.no_data': 'Keine Daten verfügbar',
  'message.loading': 'Daten werden geladen...',
  'message.error': 'Ein Fehler ist aufgetreten',
  'message.success': 'Vorgang erfolgreich abgeschlossen',
  'message.confirm': 'Sind Sie sicher?',
  'message.unsaved_changes': 'Sie haben ungespeicherte Änderungen',
  
  // Errors
  'error.network': 'Netzwerkfehler',
  'error.timeout': 'Zeitüberschreitung',
  'error.unauthorized': 'Nicht autorisiert',
  'error.forbidden': 'Zugriff verweigert',
  'error.not_found': 'Nicht gefunden',
  'error.server': 'Serverfehler',
  'error.unknown': 'Unbekannter Fehler'
}

// English language pack
const LANG_EN = {
  // General
  'app.title': 'AppleJuice WebUI',
  'app.subtitle': 'P2P File Sharing',
  'app.loading': 'Loading...',
  'app.error': 'Error',
  'app.success': 'Success',
  'app.warning': 'Warning',
  'app.info': 'Information',
  
  // Navigation
  'nav.dashboard': 'Dashboard',
  'nav.downloads': 'Downloads',
  'nav.uploads': 'Uploads',
  'nav.search': 'Search',
  'nav.server': 'Server',
  'nav.settings': 'Settings',
  'nav.about': 'About',
  'nav.logout': 'Logout',
  
  // Login
  'login.title': 'Login',
  'login.subtitle': 'Sign in to your AppleJuice Core',
  'login.server': 'Server Address',
  'login.server.placeholder': 'e.g. 192.168.1.100:9854',
  'login.password': 'Password',
  'login.password.placeholder': 'Enter core password',
  'login.button': 'Sign In',
  'login.connecting': 'Connecting...',
  'login.error.title': 'Login Failed',
  'login.error.invalid': 'Invalid server address or password',
  'login.error.connection': 'Failed to connect to server',
  'login.error.timeout': 'Connection timeout',
  'login.info.title': 'Connection',
  'login.info.description': 'Make sure that:',
  'login.info.core_running': 'Your AppleJuice Core is running',
  'login.info.address_correct': 'The server address is correct',
  'login.info.password_correct': 'The password is correct',
  
  // Add more English translations as needed...
  // For brevity, I'll include key ones
  'dashboard.title': 'Dashboard',
  'downloads.title': 'Downloads',
  'uploads.title': 'Uploads',
  'shared.title': 'Shared Files',
  'credits.title': 'Credits',
  'server.title': 'Server',
  'traffic.title': 'Traffic',
  'core.title': 'Core Information'
}

// Language manager class
export class LanguageManager {
  constructor() {
    this.currentLanguage = 'de'
    this.languages = {
      'de': LANG_DE,
      'en': LANG_EN
    }
    this.fallbackLanguage = 'de'
    this.init()
  }
  
  init() {
    // Get stored language or detect browser language
    const storedLang = localStorage.getItem('aj-language')
    const browserLang = navigator.language.split('-')[0]
    
    if (storedLang && this.languages[storedLang]) {
      this.currentLanguage = storedLang
    } else if (this.languages[browserLang]) {
      this.currentLanguage = browserLang
    }
    
    console.log('Language initialized:', this.currentLanguage)
  }
  
  // Get translation for a key
  t(key, params = {}) {
    const lang = this.languages[this.currentLanguage] || this.languages[this.fallbackLanguage]
    let translation = lang[key] || key
    
    // Replace parameters in translation
    Object.keys(params).forEach(param => {
      translation = translation.replace(`{${param}}`, params[param])
    })
    
    return translation
  }
  
  // Set current language
  setLanguage(langCode) {
    if (this.languages[langCode]) {
      this.currentLanguage = langCode
      localStorage.setItem('aj-language', langCode)
      console.log('Language changed to:', langCode)
      
      // Emit language change event
      window.dispatchEvent(new CustomEvent('languageChanged', {
        detail: { language: langCode }
      }))
    }
  }
  
  // Get current language
  getCurrentLanguage() {
    return this.currentLanguage
  }
  
  // Get available languages
  getAvailableLanguages() {
    return Object.keys(this.languages).map(code => ({
      code,
      name: code === 'de' ? 'Deutsch' : 'English'
    }))
  }
  
  // Add new language pack
  addLanguage(code, translations) {
    this.languages[code] = translations
  }
}

// Create global language manager instance
export const lang = new LanguageManager()

// Export translation function for easy use
export const t = (key, params) => lang.t(key, params)

// Vue plugin for language support
export const LanguagePlugin = {
  install(app) {
    app.config.globalProperties.$t = t
    app.config.globalProperties.$lang = lang
    
    // Make reactive
    app.provide('language', lang)
  }
}
# 🍎 aj-vue - AppleJuice Vue.js Frontend

Ein modernes Vue.js Web-Interface für den appleJuice P2P-Client mit CoreUI Design System.

## 📋 Übersicht

aj-vue ist das modernste Web-Frontend für appleJuice P2P-Clients. Es bietet eine intuitive, responsive Benutzeroberfläche mit erweiterten Features wie Echtzeitmonitoring, sicherer API-Kommunikation und einem integrierten Proxy-System für die Entwicklung.

## ✨ Features

- 🎨 **Modernes UI** mit CoreUI Design System
- ⚡ **Vue 3** mit Composition API
- 🔄 **Echtzeitdaten** über WebSocket/Polling
- 🛡️ **Sichere API-Kommunikation** mit Verschlüsselung
- 📱 **Responsive Design** für Desktop und Mobile
- 🔌 **Proxy-System** für Development
- 🌐 **Multi-Server Support** 
- 🔒 **Session Management**

## 🏗️ Projektstruktur

```
aj-vue/
├── api/                     # PHP Backend API
│   ├── classes/            # API-Klassen
│   ├── config/             # Konfigurationsdateien
│   └── test/               # API-Tests
├── public/                 # Statische Assets
├── src/
│   ├── components/         # Vue-Komponenten
│   │   ├── AppHeader.vue
│   │   ├── AppSidebar.vue
│   │   └── AppFooter.vue
│   ├── layouts/            # Layout-Komponenten
│   │   ├── DefaultLayout.vue
│   │   └── AuthLayout.vue
│   ├── views/              # Seiten-Komponenten
│   │   ├── DashboardView.vue
│   │   ├── DownloadsView.vue
│   │   ├── SearchView.vue
│   │   ├── SettingsView.vue
│   │   └── LoginView.vue
│   ├── services/           # API Services
│   ├── utils/              # Utility-Funktionen
│   ├── router/             # Vue Router
│   └── assets/             # Styles & Assets
├── proxy-server.cjs        # Express Proxy Server
├── simple-proxy.cjs        # TCP Proxy Server
└── vite.config.js          # Vite Konfiguration
```

## 🚀 Installation

Features:
- **CORS-Unterstützung** für Cross-Origin Requests
- **Session Management** für Authentifizierung
- **TCP-Verbindungen** für direkte appleJuice Core Kommunikation
- **Automatische Passwort-Weiterleitung**

## 🎯 Hauptkomponenten

### Dashboard
- **Server-Status** Monitoring
- **Download/Upload** Statistiken
- **Verbindungs-Übersicht**
- **Performance-Metriken**

### Search & Downloads
- **P2P-Suche** mit Filtern
- **Download-Manager** mit Warteschlange
- **Upload-Monitoring**
- **Datei-Browser**

### Server Management
- **Verbindungs-Status** Überwachung

### Settings
- **Benutzereinstellungen**

## 🔐 Sicherheit

### API-Kommunikation
- **Verschlüsselung** mit Crypto-JS
- **Token-basierte** Authentifizierung
- **Rate Limiting** für API-Calls
- **Input Validation** und Sanitization

### Session Management
- **HTTP-Only Cookies** für Sessions
- **CSRF-Schutz**
- **Sichere Passwort-Speicherung**

## 📱 UI/UX Features

### CoreUI Integration
- **Responsive Grid** System
- **Dark/Light Mode** Support
- **Accessibility** Features
- **Icon Library** (@coreui/icons)

### Performance
- **Lazy Loading** für Komponenten
- **Virtual Scrolling** für große Listen
- **Caching** für API-Responses
- **Bundle Optimization**


### Backend PHP API
Das Projekt enthält auch eine PHP API unter `/api/`:
- **RESTful Endpoints**
- **JSON Responses**
- **Error Handling**
- **Security Features**

### Code Style
- **ESLint** Konfiguration befolgen
- **Vue Style Guide** befolgen
- **Semantic Commits** verwenden

## 📄 Lizenz

MIT License - siehe [LICENSE](../../LICENSE) für Details.

## 🔗 Links

- [Vue.js Dokumentation](https://vuejs.org/)
- [CoreUI Vue](https://coreui.io/vue/)
- [Vite Dokumentation](https://vite.dev/)
- [AppleJuice Netzwerk](https://applejuicenet.de/)

---

**Version:** 1.0.0 (Beta) | **Status:** 🧪 Aktive Entwicklung

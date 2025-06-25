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

### Voraussetzungen

- **Node.js** >= 18.0.0
- **npm** oder **yarn**
- **AppleJuice Core** läuft auf Port 9851/9854

### Setup

1. **Dependencies installieren:**
   ```bash
   npm install
   ```

2. **Environment konfigurieren:**
   ```bash
   cp .env.example .env
   ```
   
   Anpassen der `.env`:
   ```env
   VITE_API_BASE_URL=http://localhost:9851
   VITE_PROXY_PORT=3001
   VITE_DEFAULT_SERVER_HOST=localhost
   VITE_DEFAULT_SERVER_PORT=9851
   ```

## 🛠️ Entwicklung

### Development Server starten

```bash
# Nur Frontend
npm run dev

# Frontend + Proxy Server
npm run dev-with-proxy

# Nur Proxy Server
npm run proxy
```

Das Frontend ist dann unter `http://localhost:3000` verfügbar.

### Verfügbare Scripts

- `npm run dev` - Development Server (Port 3000)
- `npm run build` - Production Build
- `npm run preview` - Preview des Production Builds
- `npm run proxy` - Standalone Proxy Server (Port 3001)
- `npm run dev-with-proxy` - Frontend + Proxy parallel

## 🔧 Konfiguration

### Vite Proxy Konfiguration

Das integrierte Vite-Proxy System unterstützt mehrere appleJuice Instanzen:

```javascript
// vite.config.js
proxy: {
  '/api/localhost/9851': {
    target: 'http://localhost:9851',
    changeOrigin: true,
  },
  '/api/192.168.178.222/9854': {
    target: 'http://192.168.178.222:9854',
    changeOrigin: true,
  }
}
```

### Standalone Proxy Server

Für erweiterte Entwicklung steht ein TCP-Proxy zur Verfügung:

```bash
node simple-proxy.cjs
```

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
- **Multi-Server** Verbindungen
- **Verbindungs-Status** Überwachung
- **Konfiguration** von Server-Einstellungen

### Settings
- **Benutzereinstellungen**
- **Proxy-Konfiguration**
- **Erweiterte Optionen**

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

## 🧪 Development Tools

### IDE Setup
**Empfohlen:** VSCode mit folgenden Extensions:
- Vue - Official (Volar)
- ESLint
- Prettier
- Vue VSCode Snippets

### Debug Konfiguration
```json
// .vscode/launch.json
{
  "type": "node",
  "request": "launch",
  "name": "Debug Proxy",
  "program": "${workspaceFolder}/simple-proxy.cjs"
}
```

## 📦 Build & Deployment

### Production Build
```bash
npm run build
```

Generiert optimierte Dateien in `dist/`:
- **HTML/CSS/JS** Minification
- **Asset Optimization**
- **Tree Shaking**
- **Modern Browser** Targeting

### Deployment-Optionen

**Statische Hosts:**
```bash
# Netlify, Vercel, GitHub Pages
npm run build
# Upload dist/ Verzeichnis
```

**Nginx Konfiguration:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/dist;
    index index.html;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    location /api/ {
        proxy_pass http://localhost:3001/;
    }
}
```

## 🔗 API Integration

### AppleJuice Core API
```javascript
// Beispiel API Call
import { apiService } from '@/services/apiService'

// Server Status abrufen
const status = await apiService.getServerStatus()

// Downloads abrufen
const downloads = await apiService.getDownloads()

// Suche durchführen
const results = await apiService.search('searchterm')
```

### Backend PHP API
Das Projekt enthält auch eine PHP API unter `/api/`:
- **RESTful Endpoints**
- **JSON Responses**
- **Error Handling**
- **Security Features**

## 🐛 Troubleshooting

### Häufige Probleme

**CORS Errors:**
```bash
# Proxy Server verwenden
npm run dev-with-proxy
```

**Connection Refused:**
```bash
# AppleJuice Core Status prüfen
netstat -an | grep 9851
```

**Build Errors:**
```bash
# Cache leeren
rm -rf node_modules dist
npm install
```

## 🤝 Contributing

1. Fork das Repository
2. Feature Branch erstellen (`git checkout -b feature/amazing-feature`)
3. Änderungen committen (`git commit -m 'Add amazing feature'`)
4. Branch pushen (`git push origin feature/amazing-feature`)
5. Pull Request öffnen

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

**Version:** 0.1.0 (Beta) | **Status:** 🧪 Aktive Entwicklung

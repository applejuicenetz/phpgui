# 🍎 AppleJuice P2P WebUI - PHP API

## 📋 Übersicht

Diese moderne PHP API liest XML-Daten vom AppleJuice Core aus und konvertiert sie zu JSON für das Vue.js Frontend. Die API basiert auf der ursprünglichen Core-Klasse, wurde aber modernisiert und um REST-Endpoints erweitert.

## 🚀 Features

### **🔧 Modernisierte Core-Klasse:**
- ✅ **Sicherer XML-Parser** ohne `eval()` Verwendung
- ✅ **Exception-Handling** für robuste Fehlerbehandlung  
- ✅ **Session-Management** für Core-Authentifizierung
- ✅ **Timeout-Handling** für Core-Verbindungen
- ✅ **Caching-System** für Performance-Optimierung

### **🌐 REST API Endpoints:**

#### **Server-Management:**
- `GET /api/servers` - Liste aller verfügbaren Server
- `GET /api/server/{id}` - Details zu spezifischem Server
- `POST /api/server/connect` - Verbindung zu Server herstellen
- `POST /api/server/disconnect` - Verbindung trennen

#### **Downloads:**
- `GET /api/downloads` - Aktuelle Downloads abrufen
- `POST /api/download` - Neuen Download starten

#### **Uploads:**
- `GET /api/uploads` - Aktuelle Uploads abrufen

#### **Suche:**
- `GET /api/search?q={query}` - Suchergebnisse abrufen
- `POST /api/search` - Neue Suche starten

#### **Status & Info:**
- `GET /api/status` - Aktueller Core-Status
- `GET /api/info` - Core-Versionsinformationen

## 📁 Dateistruktur

```
api/
├── index.php              # Haupt-API-Router
├── .htaccess             # URL-Rewriting & CORS
├── classes/
│   ├── Core.php          # Modernisierte Core-Klasse
│   └── ApiResponse.php   # JSON-Response-Handler
├── test/
│   └── server.xml        # Beispiel-XML-Datei
├── test.php              # API-Test-Script
└── README.md             # Diese Dokumentation
```

## 🔧 Installation & Setup

### **1. Dateien hochladen:**
```bash
# Alle API-Dateien in den Webserver-Ordner kopieren
cp -r api/ /var/www/html/applejuice/
```

### **2. Berechtigungen setzen:**
```bash
chmod 755 api/
chmod 644 api/*.php
chmod 644 api/classes/*.php
```

### **3. Apache-Module aktivieren:**
```bash
# Für .htaccess URL-Rewriting
a2enmod rewrite
a2enmod headers
a2enmod deflate

# Apache neustarten
systemctl restart apache2
```

### **4. PHP-Konfiguration:**
```ini
# php.ini Einstellungen
allow_url_fopen = On
session.auto_start = Off
max_execution_time = 30
memory_limit = 128M
```

## 🧪 API Testen

### **1. Test-Script ausführen:**
```bash
# Browser öffnen:
http://localhost/applejuice/api/test.php
```

### **2. API-Endpoints testen:**
```bash
# Server-Liste abrufen
curl -X GET http://localhost/applejuice/api/servers

# Server-Details abrufen  
curl -X GET http://localhost/applejuice/api/server/1

# Status abrufen
curl -X GET http://localhost/applejuice/api/status
```

### **3. POST-Requests testen:**
```bash
# Server-Verbindung
curl -X POST http://localhost/applejuice/api/server/connect \
  -H "Content-Type: application/json" \
  -d '{"server_id": 1}'

# Suche starten
curl -X POST http://localhost/applejuice/api/search \
  -H "Content-Type: application/json" \
  -d '{"query": "ubuntu"}'
```

## 📊 JSON Response Format

### **Erfolgreiche Antwort:**
```json
{
  "success": true,
  "message": "Success",
  "timestamp": 1705320000,
  "data": {
    "servers": [...],
    "total": 6
  }
}
```

### **Fehler-Antwort:**
```json
{
  "success": false,
  "error": {
    "message": "Server not found",
    "code": 404,
    "details": null
  },
  "timestamp": 1705320000
}
```

## 🔌 AppleJuice Core Integration

### **Core-Verbindung konfigurieren:**
```php
// Session-Variablen setzen
$_SESSION['core_host'] = 'http://localhost:9851';
$_SESSION['core_pass'] = 'your_password';
```

### **XML-Endpoints des Cores:**
- `http://localhost:9851/xml/information.xml` - Core-Informationen
- `http://localhost:9851/xml/server.xml` - Server-Liste
- `http://localhost:9851/xml/downloads.xml` - Download-Liste
- `http://localhost:9851/xml/uploads.xml` - Upload-Liste
- `http://localhost:9851/xml/search.xml` - Suchergebnisse

## 🛡️ Sicherheit

### **Implementierte Schutzmaßnahmen:**
- ✅ **CORS-Headers** für Cross-Origin-Requests
- ✅ **Input-Validierung** für alle Parameter
- ✅ **SQL-Injection-Schutz** (keine Datenbank-Queries)
- ✅ **XSS-Schutz** durch JSON-Encoding
- ✅ **Timeout-Limits** für Core-Requests
- ✅ **Error-Handling** ohne sensitive Daten

### **Zusätzliche Empfehlungen:**
```apache
# .htaccess Sicherheits-Headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY  
Header always set X-XSS-Protection "1; mode=block"
```

## 🚀 Performance-Optimierung

### **Caching-System:**
```php
// Session-basiertes Caching
$_SESSION['cache']['STATUSBAR']['VERSION'] = $version;
$_SESSION['cache']['STATUSBAR']['SYSTEM'] = $system;
```

### **Komprimierung:**
```apache
# Gzip-Komprimierung für JSON
AddOutputFilterByType DEFLATE application/json
```

### **Chunked XML-Parsing:**
```php
// Große XML-Dateien in 4KB-Blöcken verarbeiten
$chunks = str_split($xml_data, 4096);
foreach ($chunks as $chunk) {
    xml_parse($xml_parser, $chunk);
}
```

## 🔄 Frontend-Integration

### **Vue.js API-Client:**
```javascript
// API-Service erstellen
const apiClient = axios.create({
  baseURL: 'http://localhost/applejuice/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json'
  }
});

// Server-Liste abrufen
const servers = await apiClient.get('/servers');

// Server verbinden
await apiClient.post('/server/connect', {
  server_id: 1
});
```

## 🐛 Debugging

### **Error-Logging aktivieren:**
```php
// PHP Error-Logging
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php_errors.log');
```

### **Debug-Modus:**
```php
// Debug-Informationen in Response
if ($_GET['debug'] === '1') {
    $response['debug'] = [
        'xml_data' => $xml_data,
        'parsed_array' => $this->xml_array
    ];
}
```

## 📈 Monitoring

### **API-Metriken:**
- ⏱️ **Response-Zeit** pro Endpoint
- 📊 **Request-Anzahl** pro Stunde
- ❌ **Error-Rate** und Fehlertypen
- 🔌 **Core-Verbindungsstatus**

### **Health-Check Endpoint:**
```bash
# API-Gesundheit prüfen
curl -X GET http://localhost/applejuice/api/status
```

Die API ist jetzt bereit für die Integration mit dem Vue.js Frontend und dem AppleJuice Core! 🎉
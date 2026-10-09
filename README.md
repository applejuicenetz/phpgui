# appleJuice phpGUI

![](https://img.shields.io/github/release/applejuicenetz/phpgui.svg)
![](https://img.shields.io/github/downloads/applejuicenetz/phpgui/total)
![](https://img.shields.io/github/license/applejuicenetz/phpgui.svg)

![](https://github.com/applejuicenetz/phpgui/actions/workflows/container.yml/badge.svg)
![](https://img.shields.io/docker/pulls/applejuicenetz/phpgui)
![](https://img.shields.io/docker/image-size/applejuicenetz/phpgui)

Weboberfläche für den appleJuice Client: statisches Vue-3-Frontend ohne Build-Schritt und PHP-Backend als XML→JSON-Proxy zum Core.

Das Frontend besteht aus statischen Dateien unter `public/`, das PHP-Backend liefert JSON unter `public/api.php`. Beide müssen unter derselben öffentlichen Adresse erreichbar sein.

Beim Selbsthosting ohne Docker genügt `php -S 127.0.0.1:8088 -t public`; danach `http://127.0.0.1:8088/index.html` öffnen.

### Hinweise zur Sicherheit

Core-Adressen sind frei wählbar. phpGUI deshalb nicht als offenen Proxy im Internet betreiben. „Login merken“ speichert einen zugriffsfähigen Passwort-Hash im Browser; nur auf vertrauenswürdigen Geräten aktivieren.

## Abhängigkeiten

Benötigt wird mindestens PHP `8.5`.

## Konfiguration (beim Selbsthosting ohne Docker)

Die Datei `.env.dist` als `.env` kopieren und die gewünschten Einstellungen mit einem Texteditor anpassen.

### Umgebungsvariablen

| Variable             | Beispielwert         | Beschreibung                                                         |
|----------------------|----------------------|----------------------------------------------------------------------|
| `CORE_HOST`          | `http://192.168.2.1` | IP-Adresse oder Hostname des Core, einschließlich Protokoll          |
| `CORE_PORT`          | `9851`               | XML-Port des Core                                                    |
| `GUI_LANGUAGE`       | `de`                 | Sprache: `de` oder `en`                                              |
| `GUI_REFRESH_INTERVAL` | `5`                | Intervall der Live-Aktualisierung in Sekunden (1–3600)               |
| `GUI_SHOW_NEWS`      | `1`                  | Nachrichten auf der Statusseite anzeigen                             |
| `GUI_SHOW_SHARE`     | `1`                  | Freigabestatistiken auf der Statusseite anzeigen                     |
| `PHP_MEMORY_LIMIT`   | `256M`               | PHP `memory_limit` (Standard im Container: `256M`)                   |
| `TOP_SHOW_PERMALINK` | `1`                  | Dauerlink zur Instanz im Benutzermenü anzeigen (`0` blendet ihn aus) |
| `NEWS_URL`           | `http://XY`          | URL für Nachrichten                                                  |
| `SERVERLIST_URL`     | `http://ABC`         | URL zum Abrufen neuer Server                                         |
| `FAQ_URL`            | `http://XY`          | URL der FAQ (Hilfe-Seite und Seitenleiste)                           |
| `RELEASE_URL`        | `http://XY`          | GitHub-API-URL des neuesten Releases (`tag_name`) für die Versionsprüfung |
| `TZ`                 | `Europe/Berlin`      | Zeitzone                                                             |
| `ALLOWED_SERVERMSG_TAGS` | `<a><b><i><u><br>` | Erlaubte HTML-Tags in Servernachrichten                            |
| `PHP_INI_DISPLAY_ERRORS` | `On`             | PHP-Fehlerausgabe (`Off` für Produktivbetrieb empfohlen)             |
| `PHP_INI_ERROR_REPORTING` | `1`             | PHP-`error_reporting`-Wert                                           |
| `REL_INFO`           | `http://MN/ajfps/%s` | Leer lassen, um die Spalte mit Release-Informationen auszublenden    |

Der Dauerlink enthält die Adresse des Core und einen Passwort-Hash. Wer den Link besitzt, kann auf diesen Core zugreifen. Deshalb sollte der Link wie ein Passwort aufbewahrt und nur entsprechend weitergegeben werden. Bestehende Links im Format `index.php?l=...` aus älteren Versionen werden weiterhin unterstützt.

## Docker

### Freigegebene Ports

- `80` – HTTP-Port

### docker run

Den `phpgui`-Container mit folgendem Befehl erstellen und starten:

```bash
docker run -d \
        -p 8080:80 \
        --name phpgui \
        ghcr.io/applejuicenetz/phpgui:latest
```

Optional können `CORE_HOST` und/oder `CORE_PORT` als Umgebungsvariablen gesetzt werden.

Beispiel:

```bash
docker run -d \
        -p 8080:80 \
        -e "CORE_HOST=http://192.168.1.2" \
        -e "CORE_PORT=9851" \
        --name phpgui \
        ghcr.io/applejuicenetz/phpgui:latest
```

### docker-compose.yml

```yaml
services:
  php-gui:
    image: ghcr.io/applejuicenetz/phpgui:latest
    restart: always
    container_name: phpgui
    network_mode: bridge
    ports:
      - "8080:80/tcp"
    environment:
      TZ: Europe/Berlin
      CORE_HOST: http://192.168.1.2
      CORE_PORT: 9851
      GUI_LANGUAGE: de
      PHP_MEMORY_LIMIT: 256M
    deploy:
      resources:
        limits:
          memory: 512M
```

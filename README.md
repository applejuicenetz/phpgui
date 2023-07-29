# appleJuice phpGUI

![](https://img.shields.io/github/release/applejuicenetz/phpgui.svg)
![](https://img.shields.io/github/downloads/applejuicenetz/phpgui/total)
![](https://img.shields.io/github/license/applejuicenetz/phpgui.svg)

![](https://github.com/applejuicenetz/phpgui/actions/workflows/container.yml/badge.svg)
![](https://img.shields.io/docker/pulls/applejuicenetz/phpgui)
![](https://img.shields.io/docker/image-size/applejuicenetz/phpgui)

appleJuice Client GUI geschrieben in PHP.


## Abhängigkeiten

Es wird mindestens PHP `7.4.0` benötigt!


## Konfiguration (nur bei selbst hosting ohne Docker)

Die Datei `.env.dist` kopieren, zu `.env` umbenennen und mit einem Texteditor die gewünschte Konfiguration vornehmen.


## auto login via url

Zwischen dem `Core Beenden` und `Logout` Button befindet sich eine `Permalink` Button.
Dieser kann als Lesezeichen gesetzt werden und logt dich automatisch in den gerade eingeloggten Core ein.

Zusätzlich kann manuell der URL-Parameter `&tab=NAME_DES_TAB` hinzugefügt werden, um bspw. direkt in den `downloads` oder `uploads` Tab zu springen.

### Environment Variables

| Variable                | Value                | Description                                |
|-------------------------|----------------------|--------------------------------------------|
| `CORE_HOST`             | `http://192.168.2.1` | IP/HOST where Core is running, with scheme |
| `CORE_PORT`             | `9851`               | Core XML Port                              |
| `GUI_LANGUAGE`          | `deutsch`            | `deutsch` or `englisch`                    |
| `GUI_STYLE`             | `tango`              | style name (view styles folder)            |
| `GUI_REFRESH_STATUS`    | `10`                 | refresh `status bar` in seconds            |
| `GUI_REFRESH_DOWNLOADS` | `30`                 | refresh `downloads` view in seconds        |
| `GUI_REFRESH_UPLOADS`   | `30`                 | refresh `uploads` view in seconds          |
| `GUI_REFRESH_SEARCH`    | `30`                 | refresh `search` view in seconds           |
| `GUI_SHOW_NEWS`         | `1`                  | show news on `status page`                 |
| `GUI_SHOW_SHARE`        | `1`                  | show share stats on `status page`          |
| `TOP_SHOW_PERMALINK`    | `1`                  | show `Perma Link` in Top Navbar            |
| `NEWS_URL`              | `http://XY`          | url where to get news from                 |
| `SERVERLIST_URL`        | `http://ABC`         | url where to find new servers              |
| `REL_INFO`              | `http://MN/ajfps/%s` | set them to empty to disable rel info col  |


## Docker

### Exposed Ports

- `80` - HTTP Port

### docker run

create and run `phpgui` container with the following command

```bash
docker run -d \
        -p 8080:80 \
        --name phpgui \
        ghcr.io/applejuicenetz/phpgui:latest
```

optional: add `CORE_HOST` and/or `CORE_PORT` with your environment

eg.

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
version: '3.9'

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
      GUI_STYLE: tango
```

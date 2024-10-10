# appleJuice phpGUI

![](https://img.shields.io/github/release/applejuicenetz/phpgui.svg)
![](https://img.shields.io/github/downloads/applejuicenetz/phpgui/total)
![](https://img.shields.io/github/license/applejuicenetz/phpgui.svg)

![](https://github.com/applejuicenetz/phpgui/actions/workflows/container.yml/badge.svg)
![](https://img.shields.io/docker/pulls/applejuicenetz/phpgui)
![](https://img.shields.io/docker/image-size/applejuicenetz/phpgui)

appleJuice Client GUI geschrieben in PHP.

## Abhängigkeiten

Es wird mindestens PHP `8.2` benötigt!

## Hosted instances

- Current: https://phpgui.applejuicenet.cc
- Beta: https://phpgui-beta.applejuicenet.cc
- Old: https://phpgui-old.applejuicenet.cc

## Konfiguration (nur bei selbst hosting ohne Docker)

Die Datei `.env.dist` kopieren, zu `.env` umbenennen und mit einem Texteditor die gewünschte Konfiguration vornehmen.

### Environment Variables

| Variable             | Value                | Description                                |
|----------------------|----------------------|--------------------------------------------|
| `CORE_HOST`          | `http://192.168.2.1` | IP/HOST where Core is running, with scheme |
| `CORE_PORT`          | `9851`               | Core XML Port                              |
| `GUI_LANGUAGE`       | `de`                 | `de` or `en`                               |
| `GUI_SHOW_NEWS`      | `1`                  | show news on `status page`                 |
| `GUI_SHOW_SHARE`     | `1`                  | show share stats on `status page`          |
| `NEWS_URL`           | `http://XY`          | url where to get news from                 |
| `SERVERLIST_URL`     | `http://ABC`         | url where to find new servers              |
| `REL_INFO`           | `http://MN/ajfps/%s` | set them to empty to disable rel info col  |

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
```

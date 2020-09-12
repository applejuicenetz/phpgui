# appleJuice phpGUI

![](https://img.shields.io/docker/pulls/applejuicenet/phpgui)
![](https://github.com/applejuicenet/phpgui/workflows/docker/badge.svg)
![](https://img.shields.io/docker/image-size/applejuicenet/phpgui)

appleJuice Client GUI geschrieben in PHP.


## Abhängigkeiten

Es wird mindestens PHP `7.4.0` benötigt!


## Konfiguration (nur bei selbst hosting ohne Docker)

Die Datei `.env.dist` kopieren, zu `.env` umbenennen und mit einem Texteditor die gewünschte Konfiguration vornehmen.


## auto login via url

`/main/index.php?l=base64encodedCredentials`

`base64encodedCredentials` ist ein base64 kodierter Werte von folgendem: `http://HOST:PORT|PASSWORD`

```bash
echo "http://HOST:PORT|PASSWORD" | base64
```
Das Passwort kann sowohl ein `md5sum` sein, als auch `plain`.

## Docker

https://hub.docker.com/r/applejuicenet/phpgui


### Exposed Ports

- `80` - HTTP Port


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


### docker run

create and run `ajgui-php` container with the following command

```bash
docker run -d \
        -p 8080:80 \
        --name ajgui-php \
        applejuicenet/phpgui:latest
```

optional: add `CORE_HOST` and/or `CORE_PORT` with your environment

eg.

```bash
docker run -d \
        -p 8080:80 \
        -e "CORE_HOST=http://192.168.1.2" \
        -e "CORE_PORT=9851" \
        --name ajgui-php \
        applejuicenet/phpgui:latest
```

### docker-compose.yml

```yaml
version: '2.4'

services:
    php-gui:
        image: applejuicenet/phpgui:latest
        restart: always
        container_name: ajgui-php
        mem_limit: 128MB
        networks:
            - bridge
        ports:
            - 8080:80/tcp
        environment:
            TZ: Europe/Berlin
            CORE_HOST: http://192.168.1.2
            CORE_PORT: 9851
            GUI_STYLE: tango
```

# appleJuice phpGUI

![Docker Pulls](https://img.shields.io/docker/pulls/red171/ajgui-php.svg)
![Docker Stars](https://img.shields.io/docker/stars/red171/ajgui-php.svg)
![MicroBadger Size](https://img.shields.io/microbadger/image-size/red171/ajgui-php.svg)

modified again to run with PHP 7

## auto login from url

`index.php?l=base64encodedCredentials`

`base64encodedCredentials` should be a base64 encoded string from this `http://HOST:PORT|PASSWORD`

```bash
echo "http://HOST:PORT|PASSWORD" | base64
```

## Docker

https://hub.docker.com/r/red171/ajgui-php

### Exposed Ports

- `80` - HTTP Port

### Environment Variables

| Variable                | Value                | Description                              |
|-------------------------|----------------------|------------------------------------------|
| `CORE_HOST`             | `http://192.168.2.1` | IP/HOST where Core is running, with http |
| `CORE_PORT`             | `9851`               | Core XML Port                            |
| `GUI_LANGUAGE`          | `deutsch`            | `deutsch` or `englisch`                  |
| `GUI_STYLE`             | `tango`              | style name (view styles folder)          |
| `GUI_REFRESH_STATUS`    | `10`                 | refresh `status bar` in seconds          |
| `GUI_REFRESH_DOWNLOADS` | `30`                 | refresh `downloads` view in seconds      |
| `GUI_REFRESH_UPLOADS`   | `30`                 | refresh `uploads` view in seconds        |
| `GUI_REFRESH_SEARCH`    | `30`                 | refresh `search` view in seconds         |
| `GUI_SHOW_NEWS`         | `1`                  | show news on `status page`               |
| `GUI_SHOW_SHARE`        | `1`                  | show share stats on `status page`        |
| `GUI_PROGRESSBARS_TYPE` | `3`                  | 1,2,3 see [vars.php](vars.php)           |


### docker run

create and run `ajgui-php` container with the following command

```bash
docker run -d \
        -p 8080:80 \
        --name ajgui-php \
        red171/ajgui-php:latest
```

optional: add `CORE_HOST` and/or `CORE_PORT` with your environment

eg.

```bash
docker run -d \
        -p 8080:80 \
        -e "CORE_HOST=http://192.168.1.2" \
        -e "CORE_PORT=9851" \
        --name ajgui-php \
        red171/ajgui-php:latest
```

### docker-compose.yml

```yaml
version: '2.4'

services:
    php-gui:
        image: red171/ajgui-php:latest
        restart: always
        container_name: ajgui-php
        mem_limit: 128MB
        ports:
            - 8080:80/tcp
        environment:
            TZ: Europe/Berlin
            CORE_HOST: http://192.168.1.2
            CORE_PORT: 9851
            GUI_STYLE: tango
        networks:
            - bridge
```
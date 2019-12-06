# appleJuice phpGUI

![Docker Pulls](https://img.shields.io/docker/pulls/red171/ajgui-php.svg)
![Docker Stars](https://img.shields.io/docker/stars/red171/ajgui-php.svg)
![Docker Cloud Automated build](https://img.shields.io/docker/cloud/automated/red171/ajgui-php.svg)
![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/red171/ajgui-php.svg)
![MicroBadger Size](https://img.shields.io/microbadger/image-size/red171/ajgui-php.svg)

modified again to run with PHP 7

## docker

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
        -e "CORE_HOST=192.168.1.2" \
        -e "CORE_PORT=9851" \
        --name ajgui-php \
        red171/ajgui-php:latest
```
# appleJuice phpGUI

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
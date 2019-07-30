# appleJuice phpGUI

modified again to run with PHP 7 

## docker

create and run `ajgui-php` container with the following command

```bash
docker run -d \
        -p 8080:80 \
        # optional
        -e "CORE_HOST=192.168.1.2" \
        # optional
        -e "CORE_PORT=9851" \
        --name ajgui-php \
        red171/ajgui-php:latest
```

change `CORE_HOST` and `CORE_PORT` with your environment or leave empty for 
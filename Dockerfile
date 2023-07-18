FROM php:8-apache

ARG BUILD_DATE
ARG VCS_REF
ARG BUILD_VERSION

ENV VERSION=$BUILD_VERSION \
    CORE_HOST="" \
    CORE_PORT=9851 \
    GUI_LANGUAGE="deutsch"  \
    GUI_STYLE="tango"  \
    GUI_REFRESH_STATUS=10 \
    GUI_REFRESH_DOWNLOADS=30 \
    GUI_REFRESH_UPLOADS=30 \
    GUI_REFRESH_SEARCH=30 \
    GUI_SHOW_NEWS=1 \
    GUI_SHOW_SHARE=1

RUN apt update && \
    apt install -y --no-install-recommends libpng-dev \
    && docker-php-ext-install gd opcache \
    && apt clean && \
    mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini" && \
    echo "variables_order=EGPCS" > /usr/local/etc/php/conf.d/phpaj.ini && \
    echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/phpaj.ini && \
    ln -sf /dev/null /var/log/apache2/access.log && \
    ln -sf /dev/null /var/log/apache2/error.log && \
    ln -sf /dev/null /var/log/apache2/other_vhosts_access.log

COPY . /var/www/html/

EXPOSE 80

HEALTHCHECK --interval=60s --start-period=5s CMD curl -I --fail http://localhost:80 || exit 1

LABEL org.opencontainers.image.version=${VERSION} \
      org.opencontainers.image.vendor="appleJuiceNETZ" \
      org.opencontainers.image.url="https://applejuicenet.cc" \
      org.opencontainers.image.created=${BUILD_DATE} \
      org.opencontainers.image.revision=${VCS_REF} \
      org.opencontainers.image.source="https://github.com/applejuicenetz/phpgui"

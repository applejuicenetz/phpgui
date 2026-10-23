# Basis webdevops/php-apache (Alpine, Apache 2.4, amd64+arm64). Die APR-Bibliothek dort nutzt nur /dev/urandom
# und laeuft damit auch auf Kernel < 3.17 (z.B. Synology DSM, 3.10). Die offiziellen php:*-apache Images
# (Debian >= bullseye) brechen dort mit "AH00141: Could not initialize random number generator" ab.
# prod (letzte Stage = Default-Target im Workflow): nur HTTP, mod_ssl braucht getrandom() und ist entfernt.
# dev: HTTPS auf Port 443 mit dem selbstsignierten Zertifikat des Basis-Images; nur auf modernen Kerneln.
ARG PHP_TAG=8.3-alpine
FROM docker.io/webdevops/php-apache:${PHP_TAG} AS base

# LOG_STDOUT/LOG_STDERR muessen gesetzt sein, sonst zeigt die Synology DSM die Container-Logs als Fehler an.
ENV CORE_HOST="" \
    CORE_PORT=9851 \
    REPOSITORY_URL="https://github.com/applejuicenetz/phpgui" \
    GUI_LANGUAGE="de"  \
    GUI_REFRESH_INTERVAL=2 \
    GUI_REMEMBER_LOGIN=0 \
    GUI_SHOW_NEWS=1 \
    GUI_SHOW_SHARE=1 \
    WEB_DOCUMENT_ROOT=/var/www/html/public \
    PHP_MEMORY_LIMIT=256M \
    PHP_DISPLAY_ERRORS=0 \
    LOG_STDOUT=/dev/stdout \
    LOG_STDERR=/dev/stderr

RUN echo "variables_order=EGPCS" > /usr/local/etc/php/conf.d/zz-phpaj.ini && \
    echo "expose_php=Off" >> /usr/local/etc/php/conf.d/zz-phpaj.ini

# COPY --from=docker.io/composer /usr/bin/composer /usr/local/bin/

# Static assets: ETag/Last-Modified for conditional requests (304 when unchanged). Cache policy is set per stage below.
RUN printf '%s\n' \
      'ServerTokens Prod' \
      'ServerSignature Off' \
      'Header onsuccess unset X-Powered-By' \
      'Header always unset X-Powered-By' > /etc/apache2/conf.d/zz-response-headers.conf

FROM base AS dev

# Dev: always revalidate, so edited files load without a hard reload.
RUN printf '%s\n' \
      '<Directory "/var/www/html/public/assets">' \
      '    FileETag MTime Size' \
      '    Header set Cache-Control "no-cache"' \
      '</Directory>' > /etc/apache2/conf.d/assets-cache.conf

EXPOSE 80 443

FROM base AS prod

# vhost.ssl.conf wird von vhost.conf eingebunden: leeren statt loeschen, sonst bricht Apache mit Syntax error ab.
RUN rm -f /etc/apache2/conf.d/ssl.conf && : > /opt/docker/etc/httpd/vhost.ssl.conf

COPY . /var/www/html/

# Prod: cache for one hour, then revalidate via ETag. Asset URLs carry no version parameter, so keep this short.
RUN printf '%s\n' \
      '<Directory "/var/www/html/public/assets">' \
      '    FileETag MTime Size' \
      '    Header set Cache-Control "public, max-age=3600, must-revalidate"' \
      '</Directory>' > /etc/apache2/conf.d/assets-cache.conf

# RUN cd /var/www/html && composer install --no-dev --no-interaction --optimize-autoloader

EXPOSE 80

HEALTHCHECK --interval=60s --start-period=5s CMD curl -I --fail http://localhost:80 || exit 1

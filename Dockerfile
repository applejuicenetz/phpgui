FROM docker.io/php:8.5-apache AS base

ENV CORE_HOST="" \
    CORE_PORT=9851 \
    REPOSITORY_URL="https://github.com/applejuicenetz/phpgui" \
    GUI_LANGUAGE="de"  \
    GUI_REFRESH_INTERVAL=2 \
    GUI_SHOW_NEWS=1 \
    GUI_SHOW_SHARE=1 \
    PHP_MEMORY_LIMIT=256M

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini" && \
    echo "variables_order=EGPCS" > /usr/local/etc/php/conf.d/phpaj.ini && \
    echo 'memory_limit=${PHP_MEMORY_LIMIT}' >> /usr/local/etc/php/conf.d/phpaj.ini && \
    echo 'expose_php=Off' >> /usr/local/etc/php/conf.d/phpaj.ini && \
    ln -sf /dev/null /var/log/apache2/access.log && \
    ln -sf /dev/null /var/log/apache2/error.log && \
    ln -sf /dev/null /var/log/apache2/other_vhosts_access.log

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# COPY --from=docker.io/composer /usr/bin/composer /usr/local/bin/

# Static assets: ETag/Last-Modified for conditional requests (304 when unchanged). Cache policy is set per stage below.
RUN a2enmod headers && \
    printf '%s\n' \
      'ServerTokens Prod' \
      'ServerSignature Off' \
      'Header onsuccess unset X-Powered-By' \
      'Header always unset X-Powered-By' > /etc/apache2/conf-available/zz-response-headers.conf && \
    a2enconf zz-response-headers

FROM base AS dev

RUN apt-get update && apt-get install -y --no-install-recommends ssl-cert && a2enmod ssl && a2ensite default-ssl

# Dev: always revalidate, so edited files load without a hard reload.
RUN printf '%s\n' \
      '<Directory "${APACHE_DOCUMENT_ROOT}/assets">' \
      '    FileETag MTime Size' \
      '    Header set Cache-Control "no-cache"' \
      '</Directory>' > /etc/apache2/conf-available/assets-cache.conf && \
    a2enconf assets-cache

EXPOSE 80 443

FROM base AS prod

COPY . /var/www/html/

# Prod: cache for one hour, then revalidate via ETag. Asset URLs carry no version parameter, so keep this short.
RUN printf '%s\n' \
      '<Directory "${APACHE_DOCUMENT_ROOT}/assets">' \
      '    FileETag MTime Size' \
      '    Header set Cache-Control "public, max-age=3600, must-revalidate"' \
      '</Directory>' > /etc/apache2/conf-available/assets-cache.conf && \
    a2enconf assets-cache

# RUN cd /var/www/html && composer install --no-dev --no-interaction --optimize-autoloader

EXPOSE 80

HEALTHCHECK --interval=60s --start-period=5s CMD curl -I --fail http://localhost:80 || exit 1

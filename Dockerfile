FROM docker.io/php:8.5-apache

ENV CORE_HOST="" \
    CORE_PORT=9851 \
    GUI_LANGUAGE="de"  \
    GUI_REFRESH_INTERVAL=5 \
    GUI_SHOW_NEWS=1 \
    GUI_SHOW_SHARE=1

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini" && \
    echo "variables_order=EGPCS" > /usr/local/etc/php/conf.d/phpaj.ini && \
    echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/phpaj.ini && \
    ln -sf /dev/null /var/log/apache2/access.log && \
    ln -sf /dev/null /var/log/apache2/error.log && \
    ln -sf /dev/null /var/log/apache2/other_vhosts_access.log

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=docker.io/composer /usr/bin/composer /usr/local/bin/

COPY . /var/www/html/

RUN cd /var/www/html && composer install --no-dev --no-interaction --optimize-autoloader

EXPOSE 80

HEALTHCHECK --interval=60s --start-period=5s CMD curl -I --fail http://localhost:80 || exit 1

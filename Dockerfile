FROM php:7-apache

ARG BUILD_DATE
ARG VCS_REF

ENV CORE_HOST ""
ENV CORE_PORT ""

RUN apt update && apt install -y --no-install-recommends libpng-dev \
    && docker-php-ext-install gd \
    && apt clean

COPY . /var/www/html/

EXPOSE 80

HEALTHCHECK --interval=60s --start-period=5s CMD curl -I --fail http://localhost:80 || exit 1

LABEL org.label-schema.name="appleJuice phpGUI" \
      org.label-schema.vcs-ref=${VCS_REF} \
      org.label-schema.build-date=${BUILD_DATE} \
      org.label-schema.vcs-url="https://bitbucket.org/red171/applejuice-phpgui" \
      org.label-schema.schema-version="1.0"
FROM php:7-apache

ENV CORE_HOST ""

ENV CORE_PORT ""

RUN apt update && apt install -y --no-install-recommends libpng-dev \
    && docker-php-ext-install gd \
    && apt clean

COPY . /var/www/html/

EXPOSE 80
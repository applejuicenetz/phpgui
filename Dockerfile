FROM php:7.2-apache

ENV CORE_HOST ""

ENV CORE_PORT ""

RUN apt update && apt install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && apt clean

COPY . /var/www/html/

EXPOSE 80
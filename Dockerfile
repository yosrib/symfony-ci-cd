
ARG PHP_VERSION=8.1.0

FROM php:${PHP_VERSION}-fpm-alpine

RUN apk add --no-cache wget procps libzip icu


COPY --from=composer:2.1.8 /usr/bin/composer /usr/bin/composer
RUN composer selfupdate --2

WORKDIR /var/www/symfony

COPY symfony .

# Set execution mod on App console binary
RUN chmod +x /var/www/api/bin/console; sync

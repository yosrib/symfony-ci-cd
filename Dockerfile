
ARG PHP_VERSION=8.1.5

FROM php:${PHP_VERSION}-fpm-alpine

RUN apk add --no-cache mysql-client msmtp perl wget procps shadow libzip libpng libjpeg-turbo libwebp freetype icu

RUN apk add --no-cache --virtual build-essentials \
    icu-dev icu-libs zlib-dev g++ make automake autoconf libzip-dev \
    libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install intl && \
    docker-php-ext-install opcache && \
    docker-php-ext-install exif && \
    docker-php-ext-install zip && \
    apk del build-essentials && rm -rf /usr/src/php*

COPY --from=composer:2.1.8 /usr/bin/composer /usr/bin/composer
RUN composer selfupdate --2

WORKDIR /var/www/symfony

COPY symfony .

# Set execution mod on App console binary
RUN chmod +x /var/www/api/bin/console; sync



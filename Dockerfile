
ARG PHP_VERSION=8.1.0

FROM php:${PHP_VERSION}-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

COPY --from=composer:2.1.8 /usr/bin/composer /usr/bin/composer
RUN composer selfupdate --2

WORKDIR /var/www/symfony

COPY symfony .

# Set execution mod on App console binary
RUN chmod +x /var/www/api/bin/console; sync



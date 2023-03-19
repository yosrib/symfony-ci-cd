
ARG PHP_VERSION=8.1.0

FROM php:${PHP_VERSION}-fpm-alpine as common

RUN apk add --no-cache wget procps libzip icu
RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer
RUN composer selfupdate --2

WORKDIR /var/www/symfony

HEALTHCHECK --interval=5s --timeout=3s --retries=3 CMD php -v

CMD ["php-fpm", "-F"]

EXPOSE 9000

FROM common as prod

ENV APP_ENV=prod

### INSTALL DEPENDENCIES
COPY symfony/composer.json ./
COPY symfony/composer.lock ./
COPY symfony/symfony.lock ./

RUN set -eux;  \
    composer install --prefer-dist --no-progress --no-scripts --no-dev --no-interaction --optimize-autoloader; \
    composer clear-cache

### COPY PROJECT FILES AND DIRECTORY
COPY symfony/.env ./
COPY symfony/bin/console ./bin/
COPY symfony/config config/
COPY symfony/migrations migrations/
COPY symfony/public public/
COPY symfony/src src/
COPY symfony/templates templates/

RUN set -eux; \
    mkdir -p /var/www/symfony/var/cache /var/www/symfony/var/log; \
    composer dump-autoload --classmap-authoritative; \
    composer run-script post-install-cmd; \
    chmod +x bin/console; sync;

### CLEAN VAR DIRECTORIES
RUN set -eux; \
    chmod -R 0775 /var/www/symfony/var/cache /var/www/symfony/var/log; \
    chown -R www-data:www-data /var/www/symfony/var/cache /var/www/symfony/var/log;

FROM common as dev

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

### XDEBUG
RUN install-php-extensions xdebug-3.1.5;
COPY .docker/php-fpm/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

### COPY SOURCE CODE FROM PROD TARGET
COPY --from=prod /var/www/symfony/ ./

### COPY PROJECT FILES AND DIRECTORY FOR DEV MODE
COPY symfony/.env.test ./
COPY symfony/phpunit.xml.dist ./
COPY symfony/bin/phpunit ./bin/
COPY symfony/tests tests/

RUN set -eux; \
    composer install --prefer-dist --no-interaction --no-scripts --no-progress; \
    composer run-script post-install-cmd \
    composer clear-cache

### CLEAN VAR DIRECTORIES
RUN set -eux; \
    chmod -R 0775 /var/www/symfony/var/cache /var/www/symfony/var/log; \
    chown -R www-data:www-data /var/www/symfony/var/cache /var/www/symfony/var/log;

FROM php:8.4-fpm-alpine

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions xdebug
RUN install-php-extensions intl
RUN install-php-extensions bcmath

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-progress --no-cache

ENTRYPOINT ["tail", "-f", "/dev/null"]

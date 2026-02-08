# syntax=docker/dockerfile:1

FROM composer:lts as deps

WORKDIR /app

# Download dependencies as a separate step to take advantage of Docker's caching.
# Leverage a bind mounts to composer.json and composer.lock to avoid having to copy them
# into this layer.
# Leverage a cache mount to /tmp/cache so that subsequent builds don't have to re-download packages.
RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction

FROM php:8.5-apache as base

RUN a2enmod rewrite && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable intl mbstring

COPY --from=deps app/vendor/ /var/www/vendor
COPY ./src /var/www/src

COPY ./config/custom-php.ini /usr/local/etc/php/conf.d
COPY ./public /var/www/html
RUN mkdir /var/www/Logs

FROM base as production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
USER www-data

FROM base as development
RUN pecl install xdebug && \
docker-php-ext-enable xdebug
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
USER www-data
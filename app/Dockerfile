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

FROM php:8.5-apache as final

RUN a2enmod rewrite
RUN docker-php-ext-install pdo_mysql

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring
RUN docker-php-ext-enable intl mbstring

# Use the default production configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./config/custom-php.ini /usr/local/etc/php/conf.d

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY --from=deps app/vendor/ /var/www/html/vendor
COPY . /var/www/html

RUN rm -rf /var/www/html/config

USER www-data

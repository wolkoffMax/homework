FROM php:8.2-fpm-alpine

WORKDIR /var/www/project

RUN apk update && apk upgrade \
    && apk add --no-cache \
        unzip \
        libzip-dev \
        zlib-dev \
        postgresql-dev \
        postgresql-client \
        autoconf \
        g++ \
        make \
        curl

RUN apk add --no-cache autoconf build-base
RUN pecl install pcov && docker-php-ext-enable pcov

RUN docker-php-ext-install zip pdo pdo_pgsql \
    && docker-php-ext-enable pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/project

ENV COMPOSER_ALLOW_SUPERUSER=1

EXPOSE 9000

CMD ["php-fpm"]

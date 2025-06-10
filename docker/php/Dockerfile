FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    zip \
    unzip \
    autoconf \
    gcc \
    g++ \
    make

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./

RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

EXPOSE 9000
CMD ["php-fpm"] 
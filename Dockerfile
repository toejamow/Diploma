FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
        zip \
        unzip \
        git \
        curl \
        && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer 

WORKDIR /var/www/app

COPY ./laravel-app/composer.* ./

RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY ./laravel-app/ .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/
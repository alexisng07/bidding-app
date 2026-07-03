FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql
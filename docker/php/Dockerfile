FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

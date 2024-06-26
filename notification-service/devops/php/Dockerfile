# Start with the official PHP 8.2 image
FROM php:8.2-fpm

# Install system dependencies and clean cache
RUN apt-get update && apt-get install -y \
 git \
 curl \
 libpng-dev \
 libonig-dev \
 libxml2-dev \
 zip \
 unzip \
 libpq-dev \
 netcat-traditional \
 librabbitmq-dev

RUN pecl install amqp

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring exif pcntl bcmath gd
RUN docker-php-ext-enable amqp

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data ../ /var/www

# Change current user to www
USER www-data
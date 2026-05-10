FROM php:8.2-cli

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev libxml2-dev zip \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy all files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel setup
RUN php artisan key:generate || true

CMD php artisan serve --host=0.0.0.0 --port=$PORT
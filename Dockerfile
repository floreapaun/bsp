# Set the base image for subsequent instructions
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    default-mysql-client \
    libpng-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Remove default server definition
RUN rm -rf /var/www/html

# Copy existing application directory and set correct permissions
COPY --chown=www-data:www-data . /var/www

# Ensure necessary permissions
RUN chmod -R 777 storage bootstrap/cache

# Change current user to www-data
USER www-data

# Install PHP dependencies as www-data
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM and run migrations on container startup
CMD php artisan key:generate && php artisan migrate --force && php-fpm

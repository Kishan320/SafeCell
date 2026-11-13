# ==========================================
# SafeCell Production Dockerfile
# Laravel 12 + PHP 8.4 + Nginx + Vite
# ==========================================

# ------------------------------------------
# Stage 1: Build Frontend Assets (Vite)
# ------------------------------------------
FROM node:22-bookworm-slim AS frontend
WORKDIR /app

# Install dependencies first for layer caching
COPY package*.json ./
RUN npm ci || npm install

# Copy source code and build Vite frontend assets
COPY . .
RUN npx vite build

# ------------------------------------------
# Stage 2: Production PHP-FPM + Nginx Runtime
# ------------------------------------------
FROM php:8.4-fpm-bookworm

LABEL maintainer="SafeCell Team"
LABEL description="SafeCell Application Container"

ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    zip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    default-mysql-client \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Configure & install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        curl \
        dom \
        exif \
        fileinfo \
        gd \
        intl \
        mysqli \
        opcache \
        pcntl \
        pdo_mysql \
        zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy the rest of the application files (including storage/app/public)
COPY . .

# Copy Vite-compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Finish Composer autoload optimization and builder bundle packaging
RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && php artisan prepare:builder-bundle build.zip || true \
    && (test -L build || ln -s public/build build) \
    && rm -rf public/storage \
    && ln -s /var/www/html/storage/app/public public/storage

# Copy configuration files and entrypoint script
COPY docker/php.ini /usr/local/etc/php/conf.d/docker-safecell.ini
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Set proper permissions for Laravel directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Port 80 exposed by default (dynamic $PORT handled by entrypoint.sh)
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

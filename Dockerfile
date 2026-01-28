# ---- Build stage: install Node deps and compile frontend assets ----
FROM node:20-alpine AS assets

WORKDIR /build

COPY package.json package-lock.json* ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# ---- Application image ----
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    curl-dev \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    poppler-utils

# Install PHP extensions (curl extension is already built into php:8.2-fpm-alpine)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    gd \
    intl \
    bcmath \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy the rest of the application
COPY . .

# Copy compiled frontend assets from build stage
COPY --from=assets /build/public/build ./public/build

# Create .env so artisan commands work during build and key:generate has a target
RUN cp .env.example .env || touch .env

# Run composer scripts (post-autoload-dump, package discovery)
RUN composer dump-autoload --optimize

# Create required directories and set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p storage/app/uploads \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Supervisord configuration (runs both nginx and php-fpm)
COPY docker/supervisord.conf /etc/supervisord.conf

# PHP production settings
COPY docker/php.ini /usr/local/etc/php/conf.d/99-production.ini

# Entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

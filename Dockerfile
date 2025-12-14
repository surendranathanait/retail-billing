# Build stage
FROM node:18-alpine AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

# PHP stage
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    gcc \
    g++ \
    make \
    curl \
    sqlite \
    postgresql-client \
    mysql-client \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    bcmath \
    ctype \
    fileinfo \
    json \
    mbstring \
    tokenizer \
    xml \
    curl \
    zip \
    gd \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist

# Copy application files
COPY . .

# Copy frontend build
COPY --from=frontend-builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /app && \
    chmod -R 755 /app && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Expose port
EXPOSE 9000

# Create entrypoint script
RUN echo '#!/bin/sh\nset -e\n\necho "Running migrations..."\nphp artisan migrate --force\n\necho "Clearing cache..."\nphp artisan config:cache\nphp artisan route:cache\n\necho "Starting PHP-FPM..."\nexec docker-php-entrypoint php-fpm' > /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

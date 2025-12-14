# Build stage
FROM node:18 AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

# PHP stage
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    curl \
    git \
    zip \
    unzip \
    && docker-php-ext-configure gd \
      --enable-gd \
      --with-freetype \
      --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
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
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

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

# Create .env file if not exists and generate app key
RUN if [ ! -f .env ]; then cp .env.example .env; fi && \
    php artisan key:generate --force || true

# Clear and cache config
RUN php artisan config:cache || true && \
    php artisan route:cache || true

# Expose port
EXPOSE 9000

# Create entrypoint script
RUN echo '#!/bin/sh\n\
set -e\n\
\n\
echo "Waiting for database to be ready..."\n\
for i in $(seq 1 30); do\n\
  php artisan migrate --force 2>/dev/null && break\n\
  echo "Attempt $i/30: Waiting for database..."\n\
  sleep 2\n\
done\n\
\n\
echo "Cache cleared and regenerated"\n\
php artisan config:cache\n\
php artisan route:cache\n\
\n\
echo "Starting PHP-FPM..."\n\
php-fpm' > /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

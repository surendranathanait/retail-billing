# Build stage
FROM node:18 AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

# PHP stage
FROM php:8.2-fpm

# Install minimal dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip \
    && docker-php-ext-install pdo pdo_mysql bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy all files
COPY . .

# Install dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts && \
    composer dump-autoload --no-dev --optimize

# Copy frontend build
COPY --from=frontend-builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /app && \
    chmod -R 755 /app && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Setup .env
RUN if [ ! -f .env ]; then cp .env.example .env; fi && \
    php artisan key:generate --force || true && \
    touch database/database.sqlite && \
    php artisan migrate --force || true

EXPOSE 3000

CMD ["php", "-S", "0.0.0.0:3000", "-t", "public"]

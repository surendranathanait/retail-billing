# Build stage
FROM node:18 AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

# PHP stage
FROM php:8.2-fpm

# Install only essential dependencies
RUN apt-get update --allow-insecure-repositories && \
    apt-get install -y \
    git \
    zip \
    unzip \
    nginx \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql bcmath && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files first
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts && \
    composer dump-autoload --no-dev --optimize

# Copy frontend build
COPY --from=frontend-builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /app && \
    chmod -R 755 /app && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Create .env file
RUN if [ ! -f .env ]; then cp .env.example .env; fi && \
    php artisan key:generate --force || true

# Configure Nginx
RUN mkdir -p /var/log/supervisor && \
    rm -f /etc/nginx/sites-enabled/default

COPY docker/nginx/conf.d/app.conf /etc/nginx/sites-available/app
RUN ln -s /etc/nginx/sites-available/app /etc/nginx/sites-enabled/app

# Configure Supervisor to run both Nginx and PHP-FPM
RUN mkdir -p /etc/supervisor/conf.d && \
    echo '[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=/usr/local/sbin/php-fpm\nautostart=true\nautorestart=true\n\n[program:nginx]\ncommand=/usr/sbin/nginx -g "daemon off;"\nautostart=true\nautorestart=true' > /etc/supervisor/conf.d/app.conf

# Expose HTTP port
EXPOSE 80 9000

# Start services with supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

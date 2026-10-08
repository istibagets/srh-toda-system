FROM php:8.3-fpm-alpine

# Install system dependencies & Nginx & GMP
RUN apk add --no-cache \
    ca-certificates \
    nginx \
    curl \
    git \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    mariadb-client \
    gmp-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip opcache gmp posix

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Run composer install with --no-scripts to prevent build-time discovery issues
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/http.d/default.conf

# Production PHP, OPcache & Upload Size tuning
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "upload_max_filesize=30M" >> /usr/local/etc/php/conf.d/docker-php-ext-custom.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/docker-php-ext-custom.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/docker-php-ext-custom.ini

# Expose port 80
EXPOSE 80

# Start Reverb WebSocket server in background, PHP-FPM, and Nginx
CMD php artisan storage:link --force && php artisan package:discover --ansi && (php artisan migrate --force || true) && php artisan config:cache && php artisan route:cache && (php artisan reverb:start --host=0.0.0.0 --port=8080 &) && php-fpm -D && nginx -g 'daemon off;'

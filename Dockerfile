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

# Copy backend files
COPY laravel_backend/ .
COPY laravel_backend/.env.production /var/www/html/.env

# Copy pre-built Ionic frontend assets
COPY ionic_frontend/www/ /var/www/html/ionic_www/

# Run composer install with --no-scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/ionic_www \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/ionic_www

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/http.d/default.conf

# Production PHP, OPcache & Upload Size tuning
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "upload_max_filesize=30M" >> /usr/local/etc/php/conf.d/docker-php-ext-custom.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/docker-php-ext-custom.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/docker-php-ext-custom.ini

EXPOSE 80

CMD php artisan storage:link --force && php artisan package:discover --ansi && (php artisan migrate --force || true) && php artisan config:cache && php artisan route:cache && (nohup php artisan reverb:start --host=0.0.0.0 --port=8080 > /var/log/reverb.log 2>&1 &) && php-fpm -D && nginx -g 'daemon off;'

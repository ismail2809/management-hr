FROM php:8.3-fpm-alpine AS base

# Extensions PHP
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dépendances PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Dépendances Node + build assets
COPY package.json package-lock.json* ./
RUN npm ci --prefer-offline

COPY . .

RUN npm run build \
    && rm -rf node_modules \
    && composer run-script post-autoload-dump || true

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Config nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Cache Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan filament:cache-components

EXPOSE 80

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh
CMD ["/start.sh"]

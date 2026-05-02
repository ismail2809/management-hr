#!/bin/sh
set -e

# Migrations
php artisan migrate --force

# Storage link
php artisan storage:link || true

# Start PHP-FPM en arrière-plan
php-fpm -D

# Start nginx au premier plan
nginx -g "daemon off;"

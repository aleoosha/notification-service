#!/bin/sh
set -e

git config --global --add safe.directory /var/www/html

chown -R www-data:www-data storage bootstrap/cache
chmod -R 777 storage bootstrap/cache

if [ ! -d "vendor" ] || [ "$RUN_MIGRATIONS" = "true" ]; then
    composer install --no-interaction --optimize-autoloader --ignore-platform-reqs
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan key:generate --force
    php artisan migrate --force --seed
fi

exec "$@"

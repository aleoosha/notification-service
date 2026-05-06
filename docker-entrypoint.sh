#!/bin/sh
set -e

# Установка зависимостей, если их нет
if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

# Генерация ключа, если его нет
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

php artisan key:generate --force

git config --global --add safe.directory /var/www/html

exec "$@"

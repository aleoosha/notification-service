FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libxml2-dev \
    zip unzip git oniguruma-dev linux-headers \
    # Добавляем инструменты для сборки расширений
    $PHPIZE_DEPS 

# Устанавливаем redis через pecl
RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo_pgsql pgsql bcmath gd mbstring pcntl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

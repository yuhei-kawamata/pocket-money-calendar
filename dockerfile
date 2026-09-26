FROM php:8.2-fpm

# Node.js と npm、その他必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    zip unzip git libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql

# Composer のインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# PHP の依存関係インストール
RUN composer install --no-dev --optimize-autoloader

# Node.js（CSS/JS）の依存関係インストールとビルド
RUN npm install && npm run build

EXPOSE 8000
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
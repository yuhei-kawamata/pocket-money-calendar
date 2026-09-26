FROM php:8.2-fpm

# Node.js と npm、その他必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    zip unzip git libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql

# Composer のインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# メモリ制限を解除し、軽量化して Composer インストールを実行
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --no-scripts

# Node.js（CSS/JS）の依存関係インストールとビルド
RUN npm install && npm run build

# アセットのパブリッシュ（ビルド時に実行）
RUN php artisan livewire:publish --assets || true

EXPOSE 8000
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
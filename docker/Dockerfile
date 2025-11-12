FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupadd -g 1000 zebra && \
    useradd -u 1000 -g zebra -m zebra

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize && \
    composer run-script post-autoload-dump && \
    mkdir -p storage storage/framework/{cache,views,sessions} bootstrap/cache logs && \
    chown -R zebra:zebra /var/www && \
    chmod -R 775 storage bootstrap/cache

RUN echo "alias please='php artisan'" >> /etc/bash.bashrc && \
    echo "alias pa='php artisan'" >> /etc/bash.bashrc

USER zebra

CMD ["php-fpm"]

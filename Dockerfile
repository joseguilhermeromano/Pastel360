FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# RUN composer install --no-dev --no-scripts --no-autoloader

# RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/ \
    && chmod -R 755 /var/www/

EXPOSE 9000

CMD ["php-fpm"]
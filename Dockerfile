FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    postgresql-client \ 
 && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN wget https://phar.phpunit.de/phpunit.phar && \
    chmod +x phpunit.phar && \
    mv phpunit.phar /usr/local/bin/phpunit

RUN apt-get update && apt-get install -y default-jre && apt-get clean

RUN wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-5.0.1.3006.zip && \
    unzip sonar-scanner-cli-5.0.1.3006.zip -d /opt && \
    mv /opt/sonar-scanner-5.0.1.3006 /opt/sonar-scanner && \
    rm sonar-scanner-cli-5.0.1.3006.zip

ENV PATH="/opt/sonar-scanner/bin:${PATH}"

EXPOSE 9000
CMD ["php-fpm"]

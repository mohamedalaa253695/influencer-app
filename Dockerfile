FROM php:8.0-fpm
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip\
    openssl

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install redis \
    && docker-php-ext-enable redis 


RUN docker-php-ext-install pdo pdo_mysql  exif pcntl bcmath gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . .
RUN composer update  

CMD php artisan serve --host=0.0.0.0 --port=8080

EXPOSE 8080
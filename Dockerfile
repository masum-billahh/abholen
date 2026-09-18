FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Install libcurl headers, then the PHP curl extension
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

RUN mkdir -p /var/www/html/api/data \
    && chown -R www-data:www-data /var/www/html/api/data

EXPOSE 80

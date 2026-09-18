FROM php:8.2-apache

# Enable mod_rewrite in case you need clean URLs later
RUN a2enmod rewrite

# Install curl extension (needed for Swiss Post / WooCommerce API calls)
RUN docker-php-ext-install curl

# Copy app files into Apache's web root
COPY . /var/www/html/

# Make sure the data dir is writable (pickup-orders.jsonl gets written here)
RUN mkdir -p /var/www/html/api/data \
    && chown -R www-data:www-data /var/www/html/api/data

EXPOSE 80

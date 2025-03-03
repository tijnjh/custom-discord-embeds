FROM php:8.2-apache

WORKDIR /var/www/html
COPY . .

# Enable Apache rewrite module if needed
RUN a2enmod rewrite

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

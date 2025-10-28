# Use the official PHP 8.2 Apache image
FROM php:8.2-apache

# CRITICAL FIX: Install mysqli and other required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql 

# Copy your entire application source code into the web root
COPY . /var/www/html/

# Optional: Enable Apache rewrite module (often needed for clean URLs)
RUN a2enmod rewrite

# Ensure Apache runs in the foreground (required by Render)
CMD ["apache2-foreground"]
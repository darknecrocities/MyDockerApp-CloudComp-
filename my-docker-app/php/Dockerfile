FROM php:8.2-fpm

# Install mysqli and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]

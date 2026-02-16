FROM serversideup/php:8.3-fpm-nginx

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN composer install --optimize-autoloader

EXPOSE 8080

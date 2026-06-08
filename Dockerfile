FROM php:8.3-apache
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd pdo pdo_mysql
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN a2enmod rewrite
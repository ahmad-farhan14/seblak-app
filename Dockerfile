# Gunakan PHP 8.3 dengan Apache
FROM php:8.3-apache

# Install ekstensi yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Aktifkan mod rewrite untuk routing Laravel
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy semua file ke dalam container
COPY . .

# Set permission agar folder storage bisa diakses Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ubah document root apache ke folder public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
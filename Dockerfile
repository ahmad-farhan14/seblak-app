FROM php:8.3-apache

# 1. Install system dependencies + Node.js & NPM (Untuk compile Vite/Mix)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 2. Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy source code
COPY . .

# 6. Install PHP Dependencies
RUN composer install --no-dev --optimize-autoloader

# 7. Compile Frontend Assets (Vite/Tailwind kamu akan di-build di sini)
RUN npm install && npm run build

# 8. FIX MPM: Matikan modul event agar tidak bentrok di Railway
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && a2enmod mpm_prefork

# 9. FIX DOCUMENT ROOT: Arahkan langsung ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 10. Set permissions & aktifkan mod_rewrite untuk routing web.php
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN a2enmod rewrite

# 11. Buka port standar
EXPOSE 80
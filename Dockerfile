FROM webdevops/php-nginx:8.3

# 1. FORCE INSTALL NODE.JS V20 (UNTUK TAILWIND V4)
RUN apt-get update && apt-get install -y curl \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Set working directory ke standar path webdevops
WORKDIR /app

# 3. Copy seluruh source code project Seblak
COPY . .

# 4. Install PHP Dependencies via Composer
RUN composer install --no-dev --optimize-autoloader

# 5. Compile Frontend Assets (Sekarang dijamin sukses karena Node sudah v20)
RUN npm install && npm run build

# 6. Set Environment Variables internal untuk mengarahkan Nginx ke folder public Laravel
ENV WEB_DOCUMENT_ROOT=/app/public
ENV ENTRYPOINT_QUIET_MODE=true

# 7. Set permissions folder agar Laravel bisa menulis log dan cache
RUN chown -R application:application /app/storage /app/bootstrap/cache

# 8. Jalankan port standar HTTP
EXPOSE 80
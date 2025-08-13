# Gunakan FrankenPHP sebagai base
FROM dunglas/frankenphp:php8.3

# Ganti dengan domain kamu (untuk Caddy)
ENV SERVER_NAME="localhost"

# Install PHP extensions dan tools
RUN apt-get update && apt-get install -y \
    ca-certificates curl unzip git gnupg2 \
    && install-php-extensions \
        bcmath \
        pdo_pgsql \
        pdo_mysql \
        xml \
        mbstring \
        zip \
        curl \
        pcntl \
        gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js v18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && node -v && npm -v

# Salin file Laravel
WORKDIR /app
COPY . .

# Install dependencies dan build asset (jika pakai Vite)
RUN composer install --no-dev --optimize-autoloader \
  && npm install && npm run build \
  && php artisan config:cache \
  && php artisan route:cache \
  && php artisan key:generate \
  && php artisan optimize:clear \
  && php artisan migrate --force 




# Jalankan FrankenPHP dengan Octane (worker mode)
CMD ["php", "artisan", "octane:frankenphp", "--workers=4", "--max-requests=1000"]


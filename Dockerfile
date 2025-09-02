FROM php:8.2-fpm

# Install system dependencies including Node.js and Chromium for Browsershot
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev zip unzip libpq-dev \
    nodejs npm chromium-browser \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev

# Generate optimized autoloader and clear caches
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Run entrypoint
CMD ["/usr/local/bin/docker-entrypoint.sh"]

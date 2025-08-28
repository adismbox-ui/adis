FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev zip unzip libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql bcmath

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies (sans dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Cache Laravel config & routes (empêche plantage si .env absent en build)
RUN php -r "file_exists('.env') || copy('.env.example', '.env');" \
    && php artisan config:clear \
    && php artisan route:clear

# Port exposé (Render utilise 10000 par défaut)
EXPOSE 10000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000


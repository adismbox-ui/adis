FROM php:8.2-fpm

# Installer dépendances système + Node + Nginx + utilitaires
RUN apt-get update && apt-get install -y \
    git curl unzip zip libpng-dev libjpeg-dev libfreetype6-dev libpq-dev \
    nodejs npm nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Exposer le port
EXPOSE 10000

# Installer Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Définir dossier de travail
WORKDIR /var/www/html

# Copier code source
COPY . .

# Installer dépendances PHP
RUN composer install --optimize-autoloader --no-dev

# Créer un .env temporaire pour build
RUN cp .env.example .env || true

# Optimiser Laravel
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Supprimer .env (Render fournira ses propres variables)
RUN rm .env || true

# Configurer les permissions Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copier le fichier Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copier script d'entrée
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Lancer entrypoint
CMD ["/usr/local/bin/docker-entrypoint.sh"]

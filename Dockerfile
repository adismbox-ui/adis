# Utiliser l'image PHP officielle avec Apache pour Laravel
FROM php:8.2-apache

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor \
    cron \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer Apache pour Laravel
RUN a2enmod rewrite
RUN a2enmod headers

# Copier la configuration Apache personnalisée
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Installer les dépendances Node.js et construire les assets
RUN npm install && npm run build

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Créer le répertoire pour les logs Laravel
RUN mkdir -p /var/log/laravel && chown -R www-data:www-data /var/log/laravel

# Copier les scripts de démarrage
COPY docker/scripts/start.sh /usr/local/bin/start.sh
COPY docker/scripts/start-no-db-check.sh /usr/local/bin/start-no-db-check.sh
RUN chmod +x /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start-no-db-check.sh

# Copier la configuration supervisor pour les tâches cron
COPY docker/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Exposer le port 80
EXPOSE 80

# Script de démarrage (utilise le script sans vérification DB)
CMD ["/usr/local/bin/start-no-db-check.sh"]

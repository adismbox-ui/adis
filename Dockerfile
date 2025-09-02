# Étape 1 : Installer les dépendances PHP et Composer
FROM php:8.2-fpm

# Installer dépendances système + extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git curl unzip zip libpng-dev libjpeg-dev libfreetype6-dev libpq-dev \
    nginx chromium \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier les fichiers Laravel
COPY . .

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-dev

# Exposer le port (Render utilisera celui-ci)
EXPOSE 10000

# Copier le fichier Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copier script d'entrée
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Lancer le script d'entrée
CMD ["/usr/local/bin/docker-entrypoint.sh"]

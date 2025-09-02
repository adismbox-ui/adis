#!/bin/sh
set -e

# Nettoyer les caches Laravel
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Vérifier si la DB est configurée et lancer migrations
if [ -n "$DB_CONNECTION" ] && [ -n "$DB_HOST" ]; then
    echo "Tentative de migration sur $DB_CONNECTION..."
    php artisan migrate --force || echo "Migration échouée, mais on continue..."
else
    echo "Aucune base de données configurée, skip migrations."
fi

# Lancer Nginx + PHP-FPM
service nginx start
php-fpm

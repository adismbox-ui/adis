#!/bin/sh
set -e

# Fixer les permissions Laravel
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear caches Laravel
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Vérifier si la DB est configurée
if [ "$DB_CONNECTION" = "pgsql" ] && [ -n "$DB_HOST" ]; then
    echo "Tentative de migration sur PostgreSQL..."
    php artisan migrate --force || echo "Migration échouée, mais on continue..."
else
    echo "Aucune DB PostgreSQL configurée, skip migrations."
fi

# Lancer PHP-FPM en arrière-plan puis Nginx
php-fpm -D

# Attendre que PHP-FPM soit prêt
sleep 2

# Lancer Nginx en premier plan
nginx -g "daemon off;"

#!/bin/sh
set -e

# Attendre que la DB soit prête (optionnel)
# until nc -z -v -w30 $DB_HOST $DB_PORT; do
#   echo "En attente de la DB..."
#   sleep 1
# done

# Exécuter les commandes Laravel
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force

# Lancer PHP-FPM + Nginx
service nginx start
php-fpm

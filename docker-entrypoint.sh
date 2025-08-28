#!/bin/sh
set -e

# Exécuter les migrations (mais ne pas planter si ça échoue au premier coup)
php artisan migrate --force || true

# Lancer Laravel
php artisan serve --host=0.0.0.0 --port=10000

#!/bin/bash

# Script de démarrage alternatif pour Laravel avec Docker

echo "🚀 Démarrage de l'application Laravel..."

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "🔑 Génération de la clé d'application..."
    php artisan key:generate --force
fi

# Attendre un peu que la base de données soit prête
echo "⏳ Attente de la base de données..."
sleep 10

# Exécuter les migrations en arrière-plan
echo "📊 Exécution des migrations en arrière-plan..."
php artisan migrate --force &

# Optimiser l'application pour la production
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Créer le lien symbolique pour le stockage
echo "📁 Configuration du stockage..."
php artisan storage:link

# Démarrer supervisor pour les tâches en arrière-plan
echo "🔄 Démarrage des tâches en arrière-plan..."
supervisord -c /etc/supervisor/supervisord.conf

# Démarrer Apache
echo "🌐 Démarrage d'Apache..."
exec apache2-foreground

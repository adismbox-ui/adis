#!/bin/bash

# Script de post-déploiement pour Dokploy
echo "🚀 Post-déploiement ADIS..."

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données..."
timeout=30
counter=0
while ! php artisan migrate:status > /dev/null 2>&1; do
    if [ $counter -ge $timeout ]; then
        echo "⚠️ Timeout atteint, continuons sans vérification DB..."
        break
    fi
    echo "En attente de la connexion à la base de données... ($counter/$timeout)"
    sleep 2
    counter=$((counter + 2))
done

# Exécuter les migrations
echo "📊 Exécution des migrations..."
php artisan migrate --force

# Créer le lien symbolique pour le stockage
echo "📁 Configuration du stockage..."
php artisan storage:link

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Vérifier le statut
echo "✅ Post-déploiement terminé !"
echo "🔍 Vérification du statut..."
php artisan migrate:status

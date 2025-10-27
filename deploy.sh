#!/bin/bash

# Script de déploiement pour ADIS
echo "🚀 Déploiement de l'application ADIS..."

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Veuillez installer Docker d'abord."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé. Veuillez installer Docker Compose d'abord."
    exit 1
fi

# Créer le fichier .env pour la production
echo "📝 Création du fichier .env pour la production..."
cat > .env << EOF
APP_NAME=ADIS
APP_ENV=production
APP_KEY=base64:xE00X2iAqCAniXjAS3JHL8Ctu+nFxzsaWhvH6+roMJI=
APP_DEBUG=false
APP_URL=https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Configuration MySQL Dokploy pour la production
DB_CONNECTION=mysql
DB_HOST=adis-database-rjki7t
DB_PORT=3306
DB_DATABASE=mysql
DB_USERNAME=mysql
DB_PASSWORD=pw18jkayq10rlx3x

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Configuration Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=adis.mbox@gmail.com
MAIL_PASSWORD=qfsjfuqxrmzqmwru
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=adis.mbox@gmail.com
MAIL_FROM_NAME="ADIS"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="\${APP_NAME}"
EOF

echo "✅ Fichier .env créé"

# Arrêter les conteneurs existants
echo "🛑 Arrêt des conteneurs existants..."
docker-compose down

# Construire et démarrer les conteneurs
echo "🔨 Construction et démarrage des conteneurs..."
docker-compose up --build -d

# Attendre que les services soient prêts
echo "⏳ Attente que les services soient prêts..."
sleep 30

# Exécuter les migrations
echo "📊 Exécution des migrations..."
docker-compose exec app php artisan migrate --force

# Créer le lien symbolique pour le stockage
echo "📁 Création du lien symbolique pour le stockage..."
docker-compose exec app php artisan storage:link

# Optimiser l'application
echo "⚡ Optimisation de l'application..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Vérifier le statut des conteneurs
echo "🔍 Vérification du statut des conteneurs..."
docker-compose ps

echo "✅ Déploiement terminé !"
echo "🌐 Votre application est accessible sur : https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me"
echo ""
echo "📋 Commandes utiles :"
echo "  - Voir les logs : docker-compose logs -f"
echo "  - Redémarrer : docker-compose restart"
echo "  - Arrêter : docker-compose down"
echo "  - Accéder au conteneur : docker-compose exec app bash"

# Guide de Déploiement ADIS

## 🚀 Déploiement Rapide

### Prérequis
- Docker et Docker Compose installés
- Accès au serveur de déploiement
- Variables d'environnement configurées

### Déploiement en une commande
```bash
./deploy.sh
```

## 📋 Configuration Manuelle

### 1. Variables d'environnement
Créez un fichier `.env` avec les variables suivantes :

```env
APP_NAME=ADIS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me
APP_KEY=base64:xE00X2iAqCAniXjAS3JHL8Ctu+nFxzsaWhvH6+roMJI=

# Base de données MySQL Dokploy
DB_CONNECTION=mysql
DB_HOST=adis-database-rjki7t
DB_PORT=3306
DB_DATABASE=mysql
DB_USERNAME=mysql
DB_PASSWORD=pw18jkayq10rlx3x

# Cache et sessions
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=adis.mbox@gmail.com
MAIL_PASSWORD=qfsjfuqxrmzqmwru
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=adis.mbox@gmail.com
MAIL_FROM_NAME=ADIS
```

### 2. Déploiement avec Docker Compose
```bash
# Construire et démarrer les services
docker-compose up --build -d

# Vérifier le statut
docker-compose ps

# Exécuter les migrations
docker-compose exec app php artisan migrate --force

# Optimiser l'application
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## 🔧 Services Inclus

### Application Laravel (adis-app)
- **Port interne** : 80
- **Image** : Construite depuis Dockerfile
- **Volumes** : storage, bootstrap/cache

### Base de données MySQL (adis-postgres)
- **Port** : 3306
- **Host** : adis-database-rjki7t
- **Database** : mysql
- **Username** : mysql
- **Password** : pw18jkayq10rlx3x

### Cache Redis (adis-redis)
- **Port** : 6379
- **Image** : redis:7-alpine
- **Volume** : redis_data

### Proxy Nginx (adis-nginx)
- **Ports** : 80, 443
- **Image** : nginx:alpine
- **Configuration** : docker/nginx/

## 🌐 Configuration Traefik

L'application est configurée pour fonctionner avec Traefik :

```yaml
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.adis.rule=Host(`adis-frontend-svngue-0589c7-45-130-104-114.traefik.me`)"
  - "traefik.http.routers.adis.entrypoints=websecure"
  - "traefik.http.routers.adis.tls.certresolver=letsencrypt"
  - "traefik.http.services.adis.loadbalancer.server.port=80"
```

## 📊 Monitoring et Logs

### Vérifier les logs
```bash
# Tous les services
docker-compose logs -f

# Service spécifique
docker-compose logs -f app
docker-compose logs -f postgres
docker-compose logs -f nginx
```

### Vérifier le statut
```bash
# Statut des conteneurs
docker-compose ps

# Utilisation des ressources
docker stats
```

## 🔍 Diagnostic

### Tester l'application
```bash
# Test de connectivité
curl -I https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me

# Test de l'API
curl https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me/api/health
```

### Commandes Laravel utiles
```bash
# Accéder au conteneur
docker-compose exec app bash

# Vérifier la configuration
php artisan config:show

# Vérifier les routes
php artisan route:list

# Vérifier les migrations
php artisan migrate:status

# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🛠️ Maintenance

### Redémarrage des services
```bash
# Redémarrer un service
docker-compose restart app

# Redémarrer tous les services
docker-compose restart
```

### Mise à jour de l'application
```bash
# Arrêter les services
docker-compose down

# Reconstruire et redémarrer
docker-compose up --build -d

# Exécuter les migrations
docker-compose exec app php artisan migrate --force
```

### Sauvegarde
```bash
# Sauvegarder la base de données
docker-compose exec postgres pg_dump -U adis_user adis_production > backup.sql

# Sauvegarder les fichiers
tar -czf storage_backup.tar.gz storage/
```

## 🚨 Résolution de Problèmes

### Problème : Page nginx par défaut
- Vérifiez la configuration nginx
- Vérifiez les labels Traefik
- Vérifiez que l'application Laravel est démarrée

### Problème : Erreur de base de données
- Vérifiez les variables d'environnement
- Vérifiez que PostgreSQL est démarré
- Vérifiez les logs PostgreSQL

### Problème : Erreur 500
- Vérifiez les logs Laravel
- Vérifiez les permissions des fichiers
- Vérifiez la configuration

Consultez le fichier `GUIDE_RESOLUTION_DEPLOIEMENT.md` pour plus de détails.

## 📞 Support

En cas de problème :
1. Consultez les logs : `docker-compose logs -f`
2. Vérifiez la configuration : `docker-compose config`
3. Testez la connectivité : `curl -I https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me`
4. Consultez la documentation Docker et Traefik

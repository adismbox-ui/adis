# Configuration Dokploy pour ADIS

## Variables d'environnement à configurer dans Dokploy

### Variables Obligatoires
```
APP_NAME=ADIS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me
APP_KEY=base64:xE00X2iAqCAniXjAS3JHL8Ctu+nFxzsaWhvH6+roMJI=

# Configuration MySQL Dokploy
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

## Configuration du Projet dans Dokploy

### 1. Configuration du Projet
- **Provider:** GitHub
- **Repository:** adismbox-ui/adis
- **Branch:** `master`
- **Build Path:** `/`
- **Trigger Type:** On Push

### 2. Configuration du Build
- **Build Type:** Dockerfile
- **Dockerfile Path:** `./Dockerfile`

### 3. Configuration des Volumes
```
/storage -> Pour les fichiers uploadés
/bootstrap/cache -> Pour le cache Laravel
```

### 4. Configuration des Ports
- **Port interne:** 80
- **Port externe:** 80 (HTTP) et 443 (HTTPS)

### 5. Configuration des Ressources
- **RAM:** 2GB minimum (4GB recommandé)
- **CPU:** 1 core minimum (2 cores recommandé)
- **Stockage:** 10GB minimum

## Commandes Post-Déploiement

Après le déploiement, exécutez ces commandes dans Dokploy :

```bash
# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate --force

# Créer le lien symbolique pour le stockage
php artisan storage:link

# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Configuration de la Base de Données

### Informations de connexion MySQL Dokploy
- **Host:** adis-database-rjki7t
- **Port:** 3306
- **Database:** mysql
- **Username:** mysql
- **Password:** pw18jkayq10rlx3x
- **Root Password:** 3s2zyfmpcxhjzh2b

### URL de connexion interne
```
mysql://mysql:pw18jkayq10rlx3x@adis-database-rjki7t:3306/mysql
```

### URL de connexion externe
```
mysql://mysql:pw18jkayq10rlx3x@45.130.104.114:3306/mysql
```

## Dépannage

### Vérifier la connexion à la base de données
```bash
# Tester la connexion
php artisan migrate:status

# Vérifier la configuration
php artisan config:show
```

### Logs utiles
```bash
# Logs de l'application
tail -f /var/log/laravel/laravel.log

# Logs Apache
tail -f /var/log/apache2/error.log
```

## Sécurité

1. **Variables d'environnement:** Ne jamais commiter le fichier `.env`
2. **HTTPS:** Toujours utiliser HTTPS en production
3. **Firewall:** Configurez les règles de firewall appropriées
4. **Updates:** Maintenez les dépendances à jour

## Performance

1. **Cache:** Activez tous les caches Laravel
2. **CDN:** Utilisez un CDN pour les assets statiques
3. **Compression:** Activez la compression gzip
4. **Monitoring:** Surveillez les performances avec Dokploy

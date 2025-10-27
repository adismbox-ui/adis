# Guide de Résolution des Problèmes de Déploiement ADIS

## Problème : Page nginx par défaut au lieu de l'application Laravel

### Symptômes
- L'URL `https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me` affiche la page par défaut de nginx
- Message "Welcome to nginx!" au lieu de l'application Laravel

### Causes possibles
1. **Configuration nginx incorrecte** : Le serveur nginx n'est pas configuré pour proxy vers l'application Laravel
2. **Routage Traefik** : Les labels Traefik ne pointent pas vers le bon service
3. **Conteneur Laravel non démarré** : L'application Laravel n'est pas accessible
4. **Configuration de base de données** : L'application ne peut pas se connecter à la base de données

### Solutions

#### 1. Vérifier la configuration nginx
```bash
# Vérifier que nginx pointe vers l'application Laravel
docker-compose exec nginx cat /etc/nginx/conf.d/default.conf
```

#### 2. Vérifier les logs
```bash
# Logs nginx
docker-compose logs nginx

# Logs application Laravel
docker-compose logs app

# Logs base de données
docker-compose logs postgres
```

#### 3. Vérifier le statut des conteneurs
```bash
docker-compose ps
```

#### 4. Tester la connectivité
```bash
# Tester l'accès direct à l'application Laravel
curl http://localhost:8080

# Tester la base de données
docker-compose exec app php artisan migrate:status
```

### Configuration recommandée

#### docker-compose.yml
```yaml
services:
  app:
    build: .
    container_name: adis-app
    restart: unless-stopped
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_CONNECTION=pgsql
      - DB_HOST=postgres
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.adis.rule=Host(`adis-frontend-svngue-0589c7-45-130-104-114.traefik.me`)"
      - "traefik.http.services.adis.loadbalancer.server.port=80"

  nginx:
    image: nginx:alpine
    container_name: adis-nginx
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.nginx.rule=Host(`adis-frontend-svngue-0589c7-45-130-104-114.traefik.me`)"
      - "traefik.http.services.nginx.loadbalancer.server.port=80"
```

#### Configuration nginx (docker/nginx/default.conf)
```nginx
server {
    listen 80;
    server_name adis-frontend-svngue-0589c7-45-130-104-114.traefik.me;
    
    location / {
        proxy_pass http://adis-app:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Problème : Erreur de base de données PostgreSQL

### Symptômes
- Message "Aucune DB PostgreSQL configurée, skip migrations"
- Erreurs de connexion à la base de données

### Solutions

#### 1. Vérifier la configuration de base de données
```bash
# Vérifier les variables d'environnement
docker-compose exec app env | grep DB_

# Tester la connexion
docker-compose exec app php artisan migrate:status
```

#### 2. Redémarrer les services
```bash
# Arrêter tous les services
docker-compose down

# Redémarrer avec reconstruction
docker-compose up --build -d
```

#### 3. Vérifier les logs PostgreSQL
```bash
docker-compose logs postgres
```

## Problème : Configuration Traefik

### Vérifier les labels Traefik
```bash
# Vérifier que les labels sont corrects
docker inspect adis-app | grep -A 10 "Labels"
```

### Configuration Traefik recommandée
```yaml
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.adis.rule=Host(`adis-frontend-svngue-0589c7-45-130-104-114.traefik.me`)"
  - "traefik.http.routers.adis.entrypoints=websecure"
  - "traefik.http.routers.adis.tls.certresolver=letsencrypt"
  - "traefik.http.services.adis.loadbalancer.server.port=80"
```

## Commandes de diagnostic

### Vérifier l'état général
```bash
# Statut des conteneurs
docker-compose ps

# Utilisation des ressources
docker stats

# Logs en temps réel
docker-compose logs -f
```

### Tester l'application
```bash
# Test de connectivité
curl -I https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me

# Test de l'API
curl https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me/api/health
```

### Commandes Laravel
```bash
# Accéder au conteneur Laravel
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

## Redémarrage complet

Si rien ne fonctionne, effectuez un redémarrage complet :

```bash
# Arrêter tous les services
docker-compose down -v

# Supprimer les images
docker rmi $(docker images -q)

# Reconstruire et redémarrer
docker-compose up --build -d

# Attendre que les services soient prêts
sleep 30

# Exécuter les migrations
docker-compose exec app php artisan migrate --force

# Optimiser l'application
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Contact et support

En cas de problème persistant :
1. Vérifiez les logs : `docker-compose logs -f`
2. Vérifiez la configuration : `docker-compose config`
3. Testez la connectivité : `curl -I https://adis-frontend-svngue-0589c7-45-130-104-114.traefik.me`
4. Consultez la documentation Traefik et Docker

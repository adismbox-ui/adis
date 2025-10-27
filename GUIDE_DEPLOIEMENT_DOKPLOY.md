# Guide de Déploiement Dokploy pour ADIS

## Configuration Recommandée sur Dokploy

### 1. Configuration du Projet

**Provider:** GitHub
**Repository:** Votre repository GitHub
**Branch:** `main` ou `master`
**Build Path:** `/` (racine du projet)
**Trigger Type:** On Push
**Watch Paths:** Laissez vide pour surveiller tous les fichiers

### 2. Configuration du Build

**Build Type:** Dockerfile
**Dockerfile Path:** `./Dockerfile` (par défaut)

### 3. Variables d'Environnement

Configurez les variables suivantes dans Dokploy :

#### Variables Obligatoires
```
APP_NAME=ADIS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
APP_KEY=base64:votre_cle_generer_automatiquement

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=adis_production
DB_USERNAME=adis_user
DB_PASSWORD=votre_mot_de_passe_securise

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME=ADIS
```

#### Variables Optionnelles
```
REDIS_HOST=redis
REDIS_PORT=6379
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 4. Configuration de la Base de Données

#### Option 1: Base de données externe (Recommandé)
- Utilisez un service de base de données externe (PlanetScale, Railway, etc.)
- Configurez les variables `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

#### Option 2: Base de données Dokploy
- Créez un service MySQL dans Dokploy
- Utilisez le nom du service comme `DB_HOST`

### 5. Configuration du Domaine

1. **Domaine Principal:**
   - Ajoutez votre domaine principal dans la section "Domains"
   - Exemple: `adis.votre-domaine.com`

2. **SSL:**
   - Activez SSL automatique avec Let's Encrypt
   - Dokploy gère automatiquement le certificat

### 6. Configuration des Volumes

Ajoutez les volumes suivants pour la persistance :

```
/storage/app/public -> Pour les fichiers uploadés
/storage/logs -> Pour les logs de l'application
```

### 7. Configuration des Ports

- **Port interne:** 80
- **Port externe:** 80 (HTTP) et 443 (HTTPS)

### 8. Configuration des Ressources

**Recommandations minimales:**
- **RAM:** 2GB minimum (4GB recommandé)
- **CPU:** 1 core minimum (2 cores recommandé)
- **Stockage:** 10GB minimum

### 9. Commandes de Déploiement

Le Dockerfile exécute automatiquement :
- Installation des dépendances PHP et Node.js
- Build des assets frontend
- Configuration des permissions
- Optimisation Laravel pour la production
- Démarrage des services

### 10. Surveillance et Monitoring

#### Logs
- Les logs sont disponibles dans la section "Logs" de Dokploy
- Logs Laravel: `/var/log/laravel/`
- Logs Apache: `/var/log/apache2/`

#### Monitoring
- Activez le monitoring dans Dokploy
- Surveillez l'utilisation CPU, RAM et stockage

### 11. Sauvegarde

#### Base de données
- Configurez des sauvegardes automatiques de votre base de données
- Utilisez la fonctionnalité "Volume Backups" de Dokploy

#### Fichiers
- Les fichiers uploadés sont stockés dans `/storage/app/public`
- Configurez une sauvegarde régulière de ce volume

### 12. Déploiement Automatique

1. **Autodeploy:** Activez cette option
2. **Webhook:** Dokploy créera automatiquement un webhook GitHub
3. **Déploiement:** Chaque push sur la branche principale déclenchera un déploiement

### 13. Commandes Post-Déploiement

Après le premier déploiement, exécutez ces commandes via Dokploy :

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

### 14. Dépannage

#### Problèmes Courants

1. **Erreur 500:**
   - Vérifiez les logs dans Dokploy
   - Vérifiez la configuration des variables d'environnement
   - Vérifiez les permissions des fichiers

2. **Problèmes de base de données:**
   - Vérifiez la connectivité réseau
   - Vérifiez les credentials de la base de données
   - Vérifiez que la base de données est accessible

3. **Problèmes de mail:**
   - Vérifiez la configuration SMTP
   - Testez l'envoi d'emails via les logs

#### Commandes de Debug

```bash
# Vérifier la configuration
php artisan config:show

# Vérifier les routes
php artisan route:list

# Vérifier les migrations
php artisan migrate:status

# Vérifier les queues
php artisan queue:work --once
```

### 15. Sécurité

1. **Variables d'environnement:** Ne jamais commiter le fichier `.env`
2. **HTTPS:** Toujours utiliser HTTPS en production
3. **Firewall:** Configurez les règles de firewall appropriées
4. **Updates:** Maintenez les dépendances à jour

### 16. Performance

1. **Cache:** Activez tous les caches Laravel
2. **CDN:** Utilisez un CDN pour les assets statiques
3. **Compression:** Activez la compression gzip
4. **Monitoring:** Surveillez les performances avec Dokploy

## Checklist de Déploiement

- [ ] Repository GitHub configuré
- [ ] Variables d'environnement définies
- [ ] Base de données configurée
- [ ] Domaine configuré avec SSL
- [ ] Volumes configurés
- [ ] Ressources allouées
- [ ] Autodeploy activé
- [ ] Monitoring configuré
- [ ] Sauvegardes configurées
- [ ] Tests de fonctionnement effectués

## Support

En cas de problème, consultez :
1. Les logs de Dokploy
2. Les logs Laravel
3. La documentation Dokploy
4. La documentation Laravel

# Commandes Docker pour le Serveur

## 🔧 Accès au Conteneur

Une fois connecté au conteneur Docker, vous devez d'abord naviguer vers le répertoire de l'application.

## 📍 Navigation vers le Répertoire de l'Application

```bash
# Trouver le répertoire de l'application
cd /var/www/html

# Vérifier que vous êtes au bon endroit
ls -la
# Vous devriez voir : artisan, composer.json, app/, routes/, etc.
```

## 🚀 Commandes Essentielles

### 1. Vérifier l'Installation de Sanctum

```bash
cd /var/www/html
composer show laravel/sanctum
```

### 2. Exécuter les Migrations

```bash
cd /var/www/html
php artisan migrate
```

### 3. Vérifier le Statut des Migrations

```bash
cd /var/www/html
php artisan migrate:status
```

### 4. Publier la Configuration Sanctum

```bash
cd /var/www/html
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 5. Vider les Caches

```bash
cd /var/www/html
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 6. Vérifier les Logs

```bash
cd /var/www/html
tail -f storage/logs/laravel.log
```

## 🔍 Vérification Rapide

```bash
# Vérifier que vous êtes au bon endroit
cd /var/www/html
pwd
# Devrait afficher : /var/www/html

# Vérifier que artisan existe
ls -la artisan
# Devrait afficher les informations du fichier artisan

# Vérifier que composer.json existe
ls -la composer.json
# Devrait afficher les informations du fichier composer.json
```

## ⚠️ Si le Répertoire est Différent

Si `/var/www/html` ne contient pas l'application, cherchez le bon répertoire :

```bash
# Chercher le fichier artisan
find / -name "artisan" 2>/dev/null

# Ou chercher composer.json
find / -name "composer.json" 2>/dev/null | grep -v node_modules
```

## 📝 Commandes Complètes pour Résoudre l'Erreur 500

```bash
# 1. Aller dans le répertoire de l'application
cd /var/www/html

# 2. Vérifier Sanctum
composer show laravel/sanctum

# 3. Exécuter les migrations (créer la table personal_access_tokens)
php artisan migrate

# 4. Vérifier que la table existe
php artisan tinker
>>> \DB::table('personal_access_tokens')->limit(1)->get();
>>> exit

# 5. Vider les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 6. Tester l'API
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"test"}'
```









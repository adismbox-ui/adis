# Fix : Configuration Base de Données dans Dokploy

## 🔍 Problème

L'application mobile affiche "Erreur de configuration serveur (500)" car Laravel utilise la base de données `mysql` au lieu de la base de données de l'application.

## ✅ Solution : Corriger dans Dokploy

### Étape 1 : Trouver le Nom de la Base de Données

Dans le conteneur Docker, exécutez :

```bash
cd /var/www/html

# Trouver la base qui contient la table utilisateurs
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -N -e "
SELECT DISTINCT TABLE_SCHEMA 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME = 'utilisateurs' 
AND TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
LIMIT 1;
"
```

### Étape 2 : Mettre à Jour dans Dokploy

1. Allez dans **Dokploy** → Votre projet → **Environment**
2. Trouvez la variable `DB_DATABASE`
3. Modifiez la valeur de `mysql` vers le nom trouvé à l'étape 1
4. **Sauvegardez**

### Étape 3 : Redémarrer le Conteneur

Après avoir modifié la variable d'environnement, redémarrez le conteneur dans Dokploy.

### Étape 4 : Vérifier dans le Conteneur

```bash
cd /var/www/html
php artisan config:clear
php artisan cache:clear

# Vérifier
php artisan tinker
>>> \DB::connection()->getDatabaseName();
>>> exit
```

Le nom retourné doit être celui de votre base de données (pas "mysql").

### Étape 5 : Exécuter les Migrations

```bash
cd /var/www/html
php artisan migrate
```

Cela créera la table `personal_access_tokens` dans la bonne base de données.

## 🔧 Si la Base de Données N'Existe Pas

Si aucune base ne contient les tables, créez-la :

```bash
# Créer la base de données
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "CREATE DATABASE IF NOT EXISTS adis_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Mettre à jour DB_DATABASE=adis_production dans Dokploy
# Puis exécuter les migrations
php artisan migrate
```

## 📝 Variables d'Environnement à Vérifier dans Dokploy

```
DB_CONNECTION=mysql
DB_HOST=adis-database-rjki7t
DB_PORT=3306
DB_DATABASE=nom_de_votre_base  ⚠️ IMPORTANT : Pas "mysql"
DB_USERNAME=mysql
DB_PASSWORD=pw18jkayq10rlx3x
```

## ✅ Vérification Finale

Après correction, testez depuis l'application mobile. L'erreur 500 devrait disparaître.









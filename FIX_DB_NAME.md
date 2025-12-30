# Fix : Nom de la Base de Données

## 🔍 Problème

La configuration utilise `DB_DATABASE=mysql` qui est la base système MySQL, pas la base de données de l'application.

## ✅ Solution

### 1. Trouver le Nom de la Base de Données Réelle

Dans le conteneur Docker, exécutez :

```bash
cd /var/www/html

# Option 1 : Lister toutes les bases de données
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "SHOW DATABASES;"

# Option 2 : Trouver la base qui contient la table utilisateurs
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys');"
```

### 2. Vérifier Quelle Base Contient les Tables de l'Application

```bash
# Pour chaque base trouvée, vérifier si elle contient la table utilisateurs
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "USE nom_de_la_base; SHOW TABLES LIKE 'utilisateurs';"
```

### 3. Mettre à Jour la Configuration dans Dokploy

Dans Dokploy, allez dans **Environment** et modifiez la variable :

```
DB_DATABASE=nom_de_votre_base_reelle
```

Remplacez `nom_de_votre_base_reelle` par le nom réel trouvé à l'étape 1.

### 4. Ou Créer/Modifier le Fichier .env dans le Conteneur

```bash
cd /var/www/html

# Si .env n'existe pas, créer depuis .env.example
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Éditer .env
nano .env
```

Mettez à jour :
```env
DB_CONNECTION=mysql
DB_HOST=adis-database-rjki7t
DB_PORT=3306
DB_DATABASE=nom_de_votre_base_reelle  # ⚠️ IMPORTANT : Pas "mysql"
DB_USERNAME=mysql
DB_PASSWORD=pw18jkayq10rlx3x
```

### 5. Vider le Cache et Vérifier

```bash
cd /var/www/html
php artisan config:clear
php artisan cache:clear

# Vérifier
php artisan tinker
>>> config('database.connections.mysql.database');
>>> \DB::connection()->getDatabaseName();
>>> exit
```

Les deux doivent retourner le même nom (pas "mysql").

### 6. Exécuter les Migrations

```bash
cd /var/www/html
php artisan migrate
```

## 🔧 Si la Base de Données N'Existe Pas

Si aucune base ne contient les tables, il faut créer la base et exécuter les migrations :

```bash
# Créer la base de données
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "CREATE DATABASE IF NOT EXISTS adis_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Mettre à jour .env
# DB_DATABASE=adis_production

# Exécuter les migrations
php artisan migrate
```


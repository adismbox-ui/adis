# Fix : Configuration Base de Données

## 🔍 Problème

Laravel utilise la base de données `mysql` au lieu de la base de données de l'application.

## ✅ Solution

### 1. Trouver le Nom de la Base de Données

Dans le conteneur Docker, exécutez :

```bash
cd /var/www/html

# Vérifier les bases de données disponibles
php artisan tinker
>>> \DB::select('SHOW DATABASES');
>>> exit
```

Ou directement avec MySQL :

```bash
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "SHOW DATABASES;"
```

### 2. Vérifier le Fichier .env

```bash
cd /var/www/html
ls -la .env
# Si le fichier n'existe pas, copiez .env.example
cp .env.example .env
```

### 3. Configurer le Fichier .env

```bash
cd /var/www/html
nano .env
```

Mettez à jour ces lignes :

```env
DB_CONNECTION=mysql
DB_HOST=adis-database-rjki7t
DB_PORT=3306
DB_DATABASE=nom_de_votre_base  # Remplacez par le nom réel de votre base
DB_USERNAME=mysql
DB_PASSWORD=pw18jkayq10rlx3x
```

### 4. Vider le Cache

```bash
cd /var/www/html
php artisan config:clear
php artisan cache:clear
```

### 5. Vérifier la Configuration

```bash
cd /var/www/html
php artisan tinker
>>> config('database.connections.mysql.database');
>>> \DB::connection()->getDatabaseName();
>>> exit
```

Les deux doivent retourner le même nom de base de données (pas "mysql").

### 6. Exécuter les Migrations

```bash
cd /var/www/html
php artisan migrate
```

Cela créera la table `personal_access_tokens` dans la bonne base de données.

## 🔧 Si le Fichier .env N'Existe Pas

```bash
cd /var/www/html
cp .env.example .env
php artisan key:generate
# Puis éditez .env avec les bonnes valeurs
```









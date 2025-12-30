# Trouver la Base de Données Sans Client MySQL

## 🔍 Méthode 1 : Utiliser Tinker (Recommandé)

Dans le conteneur Docker :

```bash
cd /var/www/html
php artisan tinker
```

Puis dans tinker :

```php
// Vérifier la configuration actuelle
config('database.connections.mysql.database');

// Vérifier la base connectée
\DB::connection()->getDatabaseName();

// Chercher la base qui contient utilisateurs
\DB::select("SELECT DISTINCT TABLE_SCHEMA FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'utilisateurs' AND TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys') LIMIT 1");

// Si une base est trouvée, vérifier ses tables
\DB::select("SHOW TABLES FROM nom_de_la_base");
exit
```

## 🔍 Méthode 2 : Utiliser le Script PHP

```bash
cd /var/www/html
php find_database_tinker.php
```

Ce script trouvera automatiquement la base de données.

## 🔍 Méthode 3 : Vérifier Directement avec Laravel

```bash
cd /var/www/html
php artisan tinker
```

```php
// Tester si on peut accéder à la table utilisateurs
\DB::table('utilisateurs')->count();

// Si ça fonctionne, on est sur la bonne base
// Si ça échoue avec "Table 'mysql.utilisateurs' doesn't exist", 
// c'est qu'on utilise la mauvaise base
exit
```

## ✅ Une Fois la Base Trouvée

1. **Dans Dokploy** → Environment → Modifiez `DB_DATABASE` avec le nom trouvé
2. **Redémarrez le conteneur**
3. **Dans le conteneur** :
   ```bash
   cd /var/www/html
   php artisan config:clear
   php artisan cache:clear
   php artisan migrate
   ```

## 🔧 Si Aucune Base N'Est Trouvée

Créez la base de données via Dokploy ou directement :

```bash
# Via Dokploy : Créer une nouvelle base de données
# Puis mettre à jour DB_DATABASE dans Environment
# Puis exécuter les migrations
php artisan migrate
```


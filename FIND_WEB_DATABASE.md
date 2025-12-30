# Trouver la Base de Données Utilisée par le Web

## 🔍 Méthode 1 : Vérifier les Tables Existantes

Le web fonctionne, donc les tables existent déjà. Trouvons dans quelle base :

```bash
cd /var/www/html

# Trouver toutes les bases et vérifier laquelle contient la table utilisateurs
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "
SELECT TABLE_SCHEMA 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME = 'utilisateurs' 
AND TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys');
"
```

## 🔍 Méthode 2 : Vérifier la Configuration Actuelle du Web

Si le web fonctionne, il doit avoir une configuration quelque part :

```bash
cd /var/www/html

# Vérifier les variables d'environnement du conteneur
env | grep DB_

# Ou vérifier si .env existe et le lire
if [ -f .env ]; then
    cat .env | grep DB_DATABASE
fi
```

## 🔍 Méthode 3 : Tester Chaque Base

```bash
# Lister toutes les bases
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "SHOW DATABASES;"

# Pour chaque base (sauf mysql, information_schema, etc.), vérifier
mysql -u mysql -ppw18jkayq10rlx3x -h adis-database-rjki7t -e "USE nom_base; SELECT COUNT(*) FROM utilisateurs;"
```

## ✅ Une Fois la Base Trouvée

Mettre à jour dans Dokploy → Environment :

```
DB_DATABASE=nom_de_la_base_trouvée
```

Puis dans le conteneur :

```bash
cd /var/www/html
php artisan config:clear
php artisan cache:clear
php artisan migrate
```


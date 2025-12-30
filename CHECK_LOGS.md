# Vérification des Logs pour Diagnostiquer l'Erreur 500

## 🔍 Étapes de Diagnostic

### 1. Vérifier les Logs en Temps Réel

Dans le conteneur Docker, exécutez :

```bash
cd /var/www/html
tail -f storage/logs/laravel.log
```

Puis, depuis l'application mobile, tentez une connexion. Vous verrez l'erreur exacte dans les logs.

### 2. Vérifier les Dernières Erreurs

```bash
cd /var/www/html
tail -n 100 storage/logs/laravel.log | grep -A 20 "API Login"
```

### 3. Vérifier les Permissions

```bash
cd /var/www/html
ls -la storage/logs/
# Les logs doivent être accessibles en écriture
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

### 4. Tester l'API Directement

```bash
cd /var/www/html
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"adis.mbox@gmail.com","password":"@Adls_2025@"}' \
  -v
```

### 5. Vérifier la Configuration Sanctum

```bash
cd /var/www/html
php artisan tinker
>>> config('sanctum');
>>> exit
```

### 6. Vérifier la Connexion à la Base de Données

```bash
cd /var/www/html
php artisan tinker
>>> \DB::connection()->getPdo();
>>> exit
```

## 📝 Informations à Collecter

Après avoir exécuté ces commandes, notez :
1. Le message d'erreur exact dans les logs
2. La stack trace complète
3. Le résultat du test curl
4. La configuration Sanctum


# Test Direct de l'API dans le Conteneur

## 🧪 Test de l'API Login Directement

Dans le conteneur Docker, testez l'API directement :

```bash
cd /var/www/html

# Test avec curl
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"adis.mbox@gmail.com","password":"@Adls_2025@"}' \
  -v
```

## 🔍 Vérifications Supplémentaires

### 1. Vérifier la Configuration Sanctum

```bash
cd /var/www/html
php artisan tinker
>>> config('sanctum');
>>> exit
```

### 2. Tester la Création de Token Manuellement

```bash
cd /var/www/html
php artisan tinker
>>> $user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
>>> $user->createToken('test-token')->plainTextToken;
>>> exit
```

Si cette commande échoue, vous verrez l'erreur exacte.

### 3. Vérifier les Permissions

```bash
cd /var/www/html
ls -la storage/logs/
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

### 4. Vérifier la Connexion à la Base de Données

```bash
cd /var/www/html
php artisan tinker
>>> \DB::connection()->getPdo();
>>> \DB::table('personal_access_tokens')->count();
>>> exit
```

## 📝 Informations à Noter

Après ces tests, notez :
1. Le résultat du test curl
2. L'erreur lors de la création manuelle du token
3. Le nombre de tokens dans la table
4. Les logs d'erreur complets


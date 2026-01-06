# Guide de Débogage - API Login

## 🔍 Problème : "Identifiants invalides"

Si l'application mobile affiche "Identifiants invalides", voici comment diagnostiquer le problème.

## 📝 Vérifications à Effectuer

### 1. Vérifier les Logs Laravel

Sur le serveur, consultez les logs :

```bash
tail -f storage/logs/laravel.log
```

Vous devriez voir des entrées comme :
```
[INFO] API Login attempt: {"email":"...","user_found":true,"user_id":1,"user_active":true,"user_type":"apprenant"}
[WARNING] API Login: Invalid password: {"email":"...","user_id":1}
```

### 2. Vérifier l'Utilisateur dans la Base de Données

Connectez-vous à la base de données et vérifiez :

```sql
SELECT id, email, type_compte, actif, email_verified_at 
FROM utilisateurs 
WHERE email = 'adis.mbox@gmail.com';
```

**Vérifications importantes** :
- ✅ `actif` doit être `1` (true)
- ✅ Pour les apprenants : `email_verified_at` ne doit pas être NULL
- ✅ Pour les formateurs : vérifier que `formateurs.valide = 1`

### 3. Vérifier le Mot de Passe

Le mot de passe doit être hashé avec bcrypt. Pour tester :

```php
// Dans tinker
php artisan tinker
>>> $user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
>>> \Hash::check('adis2025', $user->mot_de_passe);
```

Si cela retourne `false`, le mot de passe est incorrect ou mal hashé.

### 4. Réinitialiser le Mot de Passe (si nécessaire)

Si le mot de passe est incorrect, vous pouvez le réinitialiser :

```php
php artisan tinker
>>> $user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
>>> $user->mot_de_passe = \Hash::make('adis2025');
>>> $user->save();
```

### 5. Vérifier les Conditions de Connexion

L'API bloque la connexion si :

1. **Compte désactivé** : `actif = 0`
   - Solution : Activer le compte
   ```sql
   UPDATE utilisateurs SET actif = 1 WHERE email = 'adis.mbox@gmail.com';
   ```

2. **Apprenant non vérifié** : `email_verified_at IS NULL`
   - Solution : Vérifier l'email
   ```sql
   UPDATE utilisateurs SET email_verified_at = NOW() WHERE email = 'adis.mbox@gmail.com';
   ```

3. **Formateur non validé** : `formateurs.valide = 0`
   - Solution : Valider le formateur
   ```sql
   UPDATE formateurs SET valide = 1 WHERE utilisateur_id = (SELECT id FROM utilisateurs WHERE email = 'adis.mbox@gmail.com');
   ```

## 🧪 Test Direct de l'API

Testez l'API directement avec curl :

```bash
curl -X POST https://adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"adis.mbox@gmail.com","password":"adis2025"}' \
  -v
```

**Résultats possibles** :
- **200** : Connexion réussie (retourne token)
- **401** : Identifiants invalides
- **403** : Compte désactivé, non vérifié, ou formateur non validé

## 📱 Vérifier les Logs de l'Application Mobile

Dans la console Flutter, vous devriez voir :

```
[DEBUG] Login URL: https://adis-ci.net/api/login
[DEBUG] Login data: {"email":"adis.mbox@gmail.com","password":"***"}
[DEBUG] Login response status: 401
[DEBUG] Login response data: {"success":false,"error":"Identifiants invalides"}
```

## 🔧 Solutions Rapides

### Solution 1 : Vérifier et Activer le Compte

```sql
-- Vérifier le compte
SELECT * FROM utilisateurs WHERE email = 'adis.mbox@gmail.com';

-- Activer le compte
UPDATE utilisateurs SET actif = 1 WHERE email = 'adis.mbox@gmail.com';

-- Vérifier l'email (pour apprenant)
UPDATE utilisateurs SET email_verified_at = NOW() WHERE email = 'adis.mbox@gmail.com';
```

### Solution 2 : Réinitialiser le Mot de Passe

```php
php artisan tinker
>>> $user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
>>> $user->mot_de_passe = \Hash::make('adis2025');
>>> $user->actif = true;
>>> $user->email_verified_at = now();
>>> $user->save();
>>> echo "Mot de passe réinitialisé avec succès";
```

### Solution 3 : Créer un Nouvel Utilisateur de Test

```php
php artisan tinker
>>> $user = \App\Models\Utilisateur::create([
    'prenom' => 'Test',
    'nom' => 'User',
    'email' => 'test@adis.com',
    'mot_de_passe' => \Hash::make('test123'),
    'type_compte' => 'apprenant',
    'sexe' => 'Homme',
    'actif' => true,
    'email_verified_at' => now(),
]);
>>> echo "Utilisateur créé: test@adis.com / test123";
```

## 📊 Checklist de Diagnostic

- [ ] L'utilisateur existe dans la base de données
- [ ] Le mot de passe est correctement hashé
- [ ] Le compte est actif (`actif = 1`)
- [ ] Pour apprenant : email vérifié (`email_verified_at IS NOT NULL`)
- [ ] Pour formateur : compte validé (`formateurs.valide = 1`)
- [ ] L'API répond correctement (test avec curl)
- [ ] Les logs Laravel montrent la tentative de connexion
- [ ] Les logs Flutter montrent la requête et la réponse

## 🚀 Après Correction

Une fois le problème résolu :

1. Testez à nouveau depuis l'application mobile
2. Vérifiez que le token est bien sauvegardé
3. Testez un endpoint protégé pour confirmer que l'authentification fonctionne









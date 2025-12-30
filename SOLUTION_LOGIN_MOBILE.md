# Solution : Connexion Mobile - "Identifiants invalides"

## 🔍 Diagnostic

Si vous pouvez vous connecter sur le web mais pas depuis l'application mobile, voici les étapes pour résoudre le problème.

## ✅ Améliorations Apportées

1. **Nettoyage des données** : Les espaces sont automatiquement retirés (trim)
2. **Logs détaillés** : Toutes les tentatives de connexion sont enregistrées
3. **Vérification du format du hash** : Détection si le mot de passe n'est pas correctement hashé

## 🧪 Test Direct sur le Serveur

### Option 1 : Utiliser le script de test

```bash
php test_api_login.php
```

Ce script va :
- Vérifier que l'utilisateur existe
- Tester le mot de passe
- Vérifier toutes les conditions de connexion
- Vous donner des solutions précises

### Option 2 : Test manuel avec Tinker

```bash
php artisan tinker
```

```php
// Vérifier l'utilisateur
$user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
$user->id; // Doit retourner un ID
$user->actif; // Doit retourner true (1)
$user->type_compte; // Type de compte

// Tester le mot de passe
\Hash::check('adis2025', $user->mot_de_passe); // Doit retourner true

// Si false, réinitialiser le mot de passe
$user->mot_de_passe = \Hash::make('adis2025');
$user->actif = true;
$user->email_verified_at = now();
$user->save();
```

## 📝 Vérifier les Logs

Sur le serveur, consultez les logs après une tentative de connexion depuis l'app mobile :

```bash
tail -f storage/logs/laravel.log | grep "API Login"
```

Vous devriez voir :
```
[INFO] API Login attempt: {"email":"adis.mbox@gmail.com","password_length":8,"user_found":true,"user_id":1,...}
[WARNING] API Login: Invalid password: {"email":"...","user_id":1,...}
```

## 🔧 Solutions selon le Problème

### Problème 1 : Mot de passe incorrect

**Symptôme** : `Hash::check()` retourne `false`

**Solution** :
```php
php artisan tinker
>>> $user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
>>> $user->mot_de_passe = \Hash::make('votre_mot_de_passe');
>>> $user->save();
```

### Problème 2 : Compte désactivé

**Symptôme** : `actif = 0`

**Solution** :
```sql
UPDATE utilisateurs SET actif = 1 WHERE email = 'adis.mbox@gmail.com';
```

### Problème 3 : Email non vérifié (apprenant)

**Symptôme** : `email_verified_at IS NULL` pour un apprenant

**Solution** :
```sql
UPDATE utilisateurs SET email_verified_at = NOW() WHERE email = 'adis.mbox@gmail.com';
```

### Problème 4 : Formateur non validé

**Symptôme** : `formateurs.valide = 0`

**Solution** :
```sql
UPDATE formateurs SET valide = 1 
WHERE utilisateur_id = (SELECT id FROM utilisateurs WHERE email = 'adis.mbox@gmail.com');
```

## 🚀 Test Rapide depuis le Serveur

Testez l'API directement depuis le serveur :

```bash
curl -X POST https://adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"adis.mbox@gmail.com","password":"adis2025"}' \
  -v
```

**Résultats attendus** :
- **200** : Connexion réussie (retourne token)
- **401** : Identifiants invalides (vérifier le mot de passe)
- **403** : Compte désactivé, non vérifié, ou formateur non validé

## 📱 Vérifier depuis l'Application Mobile

Dans la console Flutter, vous devriez voir :

```
[DEBUG] Login URL: https://adis-ci.net/api/login
[DEBUG] Login data: {"email":"adis.mbox@gmail.com","password":"***"}
[DEBUG] Login response status: 200 (si succès) ou 401/403 (si erreur)
[DEBUG] Login response data: {...}
```

## ✅ Checklist de Vérification

- [ ] L'utilisateur existe dans la base de données
- [ ] Le mot de passe est correctement hashé (commence par `$2y$` ou `$2a$`)
- [ ] `Hash::check()` retourne `true` avec le bon mot de passe
- [ ] Le compte est actif (`actif = 1`)
- [ ] Pour apprenant : email vérifié (`email_verified_at IS NOT NULL`)
- [ ] Pour formateur : compte validé (`formateurs.valide = 1`)
- [ ] L'API répond correctement (test avec curl)
- [ ] Les logs Laravel montrent la tentative de connexion
- [ ] Sanctum est installé et configuré

## 🎯 Prochaines Étapes

1. Exécutez `php test_api_login.php` sur le serveur
2. Vérifiez les logs Laravel après une tentative de connexion
3. Corrigez le problème identifié (mot de passe, compte désactivé, etc.)
4. Testez à nouveau depuis l'application mobile

Une fois ces vérifications faites, la connexion devrait fonctionner ! 🎉


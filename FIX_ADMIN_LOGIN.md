# Fix : Problème de Connexion Admin sur le Web

## 🔍 Problème

Le compte admin ne peut plus se connecter sur le web alors que ça fonctionnait avant.

## ✅ Corrections Apportées

### 1. Correction du Trim sur le Mot de Passe
- **Avant** : Le mot de passe était trimmé (espaces retirés)
- **Après** : Seul l'email est trimmé, le mot de passe reste intact
- **Raison** : Certains mots de passe peuvent contenir des espaces intentionnels

### 2. Amélioration des Logs
- Logs détaillés pour chaque tentative de connexion
- Vérification du format du hash de mot de passe
- Messages d'erreur plus précis

### 3. Gestion Spécifique pour Admin
- Les admins n'ont pas besoin de vérification d'email
- Les admins peuvent se connecter directement si le compte est actif

## 🧪 Vérification sur le Serveur

### Option 1 : Utiliser le Script de Correction

```bash
php fix_admin_login.php
```

Ce script va :
- Lister tous les comptes admin
- Vérifier leur statut (actif, email vérifié)
- Proposer de corriger les problèmes
- Permettre de réinitialiser le mot de passe

### Option 2 : Vérification Manuelle avec Tinker

```bash
php artisan tinker
```

```php
// Trouver le compte admin
$admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();

// Vérifier les informations
$admin->email;
$admin->actif; // Doit être true (1)
$admin->email_verified_at; // Peut être null pour admin

// Tester le mot de passe
\Hash::check('votre_mot_de_passe', $admin->mot_de_passe); // Doit retourner true

// Si le mot de passe ne fonctionne pas, le réinitialiser
$admin->mot_de_passe = \Hash::make('nouveau_mot_de_passe');
$admin->actif = true;
$admin->save();
```

### Option 3 : Vérification Directe en Base de Données

```sql
-- Vérifier le compte admin
SELECT id, email, type_compte, actif, email_verified_at 
FROM utilisateurs 
WHERE type_compte = 'admin';

-- Activer le compte si nécessaire
UPDATE utilisateurs 
SET actif = 1, email_verified_at = NOW() 
WHERE type_compte = 'admin' AND email = 'votre@email.com';
```

## 📝 Vérifier les Logs

Après une tentative de connexion, consultez les logs :

```bash
tail -f storage/logs/laravel.log | grep "Web Login"
```

Vous devriez voir :
```
[INFO] Web Login attempt: {"email":"...","user_found":true,"user_id":1,"user_type":"admin","user_active":true}
[INFO] Web Login: Password check: {"user_id":1,"password_check":true}
[INFO] Web Login: Success: {"user_id":1,"type_compte":"admin"}
```

## 🔧 Solutions selon le Problème

### Problème 1 : Mot de Passe Incorrect

**Symptôme** : `password_check: false` dans les logs

**Solution** :
```php
php artisan tinker
>>> $admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();
>>> $admin->mot_de_passe = \Hash::make('votre_mot_de_passe');
>>> $admin->save();
```

### Problème 2 : Compte Désactivé

**Symptôme** : `user_active: false` dans les logs

**Solution** :
```sql
UPDATE utilisateurs SET actif = 1 WHERE type_compte = 'admin';
```

### Problème 3 : Format de Hash Incorrect

**Symptôme** : Le hash ne commence pas par `$2y$` ou `$2a$`

**Solution** :
```php
php artisan tinker
>>> $admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();
>>> $admin->mot_de_passe = \Hash::make('votre_mot_de_passe');
>>> $admin->save();
```

## ✅ Checklist de Vérification

- [ ] Le compte admin existe dans la base de données
- [ ] Le compte est actif (`actif = 1`)
- [ ] Le mot de passe est correctement hashé (commence par `$2y$` ou `$2a$`)
- [ ] `Hash::check()` retourne `true` avec le bon mot de passe
- [ ] Les logs montrent la tentative de connexion
- [ ] Aucune erreur dans les logs Laravel

## 🚀 Test Final

1. Allez sur : `https://adis-ci.net/login`
2. Entrez l'email et le mot de passe de l'admin
3. Vérifiez que vous êtes redirigé vers `/admin/dashboard`
4. Si erreur, consultez les logs pour voir la cause exacte

## 📌 Note Importante

Les modifications récentes ont amélioré la gestion des erreurs et les logs. Si le problème persiste, les logs vous indiqueront précisément ce qui bloque la connexion.


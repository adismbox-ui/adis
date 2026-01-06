# Fix : Erreur 500 sur Login Mobile

## 🔍 Problème

L'application mobile affiche une erreur 500 "Erreur de configuration serveur" lors de la tentative de connexion, alors que le login web fonctionne correctement.

## 🔎 Cause

Le problème vient de la table `personal_access_tokens` qui n'existe pas dans la base de données. Cette table est nécessaire pour Laravel Sanctum pour créer les tokens d'authentification API.

**Pourquoi le web fonctionne ?**
- Le login web utilise les sessions Laravel, pas les tokens Sanctum
- L'API mobile nécessite des tokens Sanctum pour l'authentification

## ✅ Solution

### Option 1 : Utiliser les migrations Laravel (Recommandé)

```bash
cd /var/www/html
php artisan migrate
```

Cela créera toutes les tables manquantes, y compris `personal_access_tokens`.

### Option 2 : Créer la table manuellement

Si les migrations ne fonctionnent pas, utilisez le script fourni :

```bash
cd /var/www/html
php create_sanctum_table.php
```

Ce script :
- Vérifie si la table existe
- La crée si elle n'existe pas
- Affiche la structure de la table

### Option 3 : Créer la table via SQL direct

Si vous avez accès à la base de données directement :

```sql
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 🧪 Vérification

Après avoir créé la table, testez :

### 1. Vérifier que la table existe

```bash
cd /var/www/html
php check_sanctum_table.php
```

### 2. Tester la création de token dans Tinker

```bash
cd /var/www/html
php artisan tinker
```

Puis :

```php
$user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
$token = $user->createToken('test-token')->plainTextToken;
echo "Token créé : $token\n";
exit
```

### 3. Tester l'API depuis l'application mobile

Essayez de vous connecter depuis l'application mobile. L'erreur 500 devrait disparaître.

## 📋 Checklist

- [ ] Table `personal_access_tokens` créée
- [ ] Sanctum installé (`composer show laravel/sanctum`)
- [ ] Modèle `Utilisateur` utilise le trait `HasApiTokens`
- [ ] Test de création de token réussi dans Tinker
- [ ] Login mobile fonctionne

## 🔧 Si le problème persiste

1. **Vérifier les logs Laravel** :
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Vérifier la configuration Sanctum** :
   ```bash
   php artisan tinker
   >>> config('sanctum');
   ```

3. **Vérifier les permissions de la base de données** :
   - L'utilisateur MySQL doit avoir les permissions CREATE, INSERT, UPDATE, DELETE sur la base de données






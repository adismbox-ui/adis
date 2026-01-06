# Debug : Création de Token Sanctum

## 🧪 Test dans Tinker

Une fois que vous avez l'utilisateur :

```php
// Dans tinker
$user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();

// Tester la création du token
$user->createToken('test-token')->plainTextToken;
```

## 🔍 Vérifications

### 1. Vérifier la Configuration Sanctum

```php
// Dans tinker
config('sanctum');
```

### 2. Vérifier la Table personal_access_tokens

```php
// Dans tinker
\DB::table('personal_access_tokens')->count();
\DB::table('personal_access_tokens')->get();
```

### 3. Vérifier les Relations

```php
// Dans tinker
$user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
$user->tokens; // Devrait retourner une collection
```

### 4. Vérifier le Trait HasApiTokens

```php
// Dans tinker
$user = \App\Models\Utilisateur::where('email', 'adis.mbox@gmail.com')->first();
class_uses($user); // Devrait contenir Laravel\Sanctum\HasApiTokens
```

## ⚠️ Problèmes Possibles

1. **Trait HasApiTokens non chargé** : Vérifier que le modèle Utilisateur utilise bien le trait
2. **Table personal_access_tokens vide** : Normal si aucun token n'a été créé
3. **Problème de permissions** : Vérifier les permissions sur la table
4. **Configuration Sanctum incorrecte** : Vérifier config/sanctum.php









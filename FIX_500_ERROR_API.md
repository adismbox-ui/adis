# Fix : Erreur 500 sur API Login

## 🔍 Problème

L'API retourne une erreur 500 (Server Error) lors de la tentative de connexion depuis l'application mobile.

## ✅ Corrections Apportées

### 1. Gestion Complète des Exceptions

Toutes les exceptions sont maintenant capturées et retournent des messages d'erreur clairs au lieu d'une erreur 500 générique.

### 2. Vérification de la Table Sanctum

Le code vérifie maintenant si la table `personal_access_tokens` existe avant de créer un token.

### 3. Logs Détaillés

Toutes les erreurs sont enregistrées dans les logs avec des détails complets pour faciliter le débogage.

## 🚀 Actions à Effectuer sur le Serveur

### Étape 1 : Vérifier les Migrations

```bash
php artisan migrate:status
```

Vérifiez que la migration pour `personal_access_tokens` a été exécutée.

### Étape 2 : Exécuter les Migrations si Nécessaire

```bash
php artisan migrate
```

Cela créera la table `personal_access_tokens` si elle n'existe pas.

### Étape 3 : Vérifier Sanctum

```bash
composer show laravel/sanctum
```

Vérifiez que Sanctum est bien installé.

### Étape 4 : Publier la Configuration Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Étape 5 : Vider les Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Étape 6 : Vérifier les Logs

```bash
tail -f storage/logs/laravel.log
```

Après une tentative de connexion, vous devriez voir des logs détaillés.

## 📋 Causes Possibles de l'Erreur 500

1. **Table `personal_access_tokens` manquante**
   - Solution : Exécuter `php artisan migrate`

2. **Sanctum non installé**
   - Solution : Exécuter `composer install`

3. **Relation manquante** (formateur, apprenant, assistant)
   - Solution : Vérifier que les relations existent dans la base de données

4. **Problème de configuration**
   - Solution : Vérifier `config/sanctum.php`

## 🧪 Test

Après les corrections, testez l'API :

```bash
curl -X POST https://www.adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"test"}' \
  -v
```

**Résultats attendus** :
- **200** : Connexion réussie
- **401** : Identifiants invalides
- **403** : Compte désactivé ou non vérifié
- **422** : Erreur de validation
- **500** : Erreur serveur (avec message détaillé dans les logs)

## 📝 Messages d'Erreur Améliorés

Maintenant, au lieu d'une erreur 500 générique, vous recevrez :
- Messages d'erreur clairs
- Détails dans les logs
- Indication si la table Sanctum est manquante

## 🔧 Si le Problème Persiste

1. Consultez les logs : `storage/logs/laravel.log`
2. Vérifiez que toutes les migrations sont exécutées
3. Vérifiez que Sanctum est correctement configuré
4. Testez avec curl pour voir la réponse exacte


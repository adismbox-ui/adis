# Fix: Erreur 500 sur /login

## 🔍 Problème identifié

L'erreur 500 sur la route `/login` était causée par :
1. **Laravel Sanctum manquant dans `composer.json`** : Le trait `HasApiTokens` était utilisé dans le modèle `Utilisateur` mais Sanctum n'était pas déclaré comme dépendance
2. **Protection insuffisante contre les relations null** dans `AuthController`

## ✅ Corrections apportées

1. **Ajout de Laravel Sanctum dans `composer.json`** :
   ```json
   "laravel/sanctum": "^4.1"
   ```

2. **Amélioration de la protection dans `AuthController`** :
   ```php
   if ($utilisateur->formateur && isset($utilisateur->formateur->valide) && !$utilisateur->formateur->valide) {
   ```

## 🚀 Actions à effectuer sur le serveur

Après le déploiement, exécutez ces commandes sur le serveur Dokploy :

```bash
# 1. Installer les dépendances (incluant Sanctum)
composer install --no-dev --optimize-autoloader

# 2. Publier la configuration Sanctum (si nécessaire)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 3. Exécuter les migrations (pour créer la table personal_access_tokens)
php artisan migrate

# 4. Vider les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📋 Vérification

Après ces étapes, vérifiez que :
1. La route `/login` fonctionne correctement
2. L'API mobile peut se connecter via `/api/login`
3. Les tokens Sanctum sont générés correctement

## 🔧 Si le problème persiste

1. **Vérifier les logs** :
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Vérifier que Sanctum est installé** :
   ```bash
   composer show laravel/sanctum
   ```

3. **Vérifier la table personal_access_tokens** :
   ```bash
   php artisan tinker
   >>> \DB::table('personal_access_tokens')->count();
   ```

4. **Vérifier la configuration Sanctum** :
   - Le fichier `config/sanctum.php` doit exister
   - Les middlewares doivent être correctement configurés dans `bootstrap/app.php`

## 📝 Notes

- Les changements ont été poussés vers le dépôt GitHub
- Dokploy devrait déployer automatiquement si configuré pour "On Push"
- Si le déploiement automatique ne fonctionne pas, déclenchez un déploiement manuel dans Dokploy









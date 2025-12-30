# Test des Endpoints API

## 🧪 Tests à Effectuer

### 1. Test de la Route de Test (GET)
```bash
curl https://adis-ci.net/api/test
```
**Résultat attendu** : JSON avec `success: true`

### 2. Test du Login (POST)
```bash
curl -X POST https://adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

**Résultats possibles** :
- **200** : Connexion réussie (retourne token)
- **401** : Identifiants invalides
- **403** : Compte désactivé ou non vérifié
- **405** : Méthode non autorisée (si GET au lieu de POST)
- **422** : Erreur de validation (email/password manquants)

### 3. Test depuis l'Application Mobile

L'application mobile devrait :
1. Faire une requête POST vers `https://adis-ci.net/api/login`
2. Inclure les headers `Content-Type: application/json` et `Accept: application/json`
3. Envoyer les données `{"email": "...", "password": "..."}`

## 🔍 Vérification des Routes

Pour vérifier que les routes sont bien enregistrées, exécutez sur le serveur :

```bash
php artisan route:list --path=api
```

Vous devriez voir :
```
POST   api/login ................ ApiAuthController@login
POST   api/register ............. ApiAuthController@register
GET    api/supports ............. ApiModuleController@getSupports
GET    api/test ................. Closure
```

## ⚠️ Problèmes Courants

### Erreur 405
- **Cause** : Méthode HTTP incorrecte (GET au lieu de POST)
- **Solution** : Vérifier que l'application mobile utilise `dio.post()` ou `http.post()`

### Erreur 404
- **Cause** : Route non trouvée
- **Solution** : Vérifier que le préfixe `/api` est correctement configuré dans `bootstrap/app.php`

### Erreur 500
- **Cause** : Erreur serveur
- **Solution** : Vérifier les logs Laravel : `storage/logs/laravel.log`

## 📝 Logs à Vérifier

### Côté Serveur
```bash
tail -f storage/logs/laravel.log
```

### Côté Application Mobile
Les logs devraient afficher :
```
[DEBUG] Login URL: https://adis-ci.net/api/login
[DEBUG] Login data: {"email":"...","password":"***"}
[DEBUG] Login response status: 200
[DEBUG] Login response data: {...}
```


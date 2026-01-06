# Fix: Erreur 405 sur /api/login

## 🔍 Problème

L'erreur 405 "Méthode non autorisée" se produit lorsque vous accédez à `https://www.adis-ci.net/api/login` via un navigateur.

## ✅ Explication

L'erreur 405 est normale car :
- La route `/api/login` est définie comme **POST** (pour les requêtes depuis l'application mobile)
- Un navigateur fait une requête **GET** par défaut
- Laravel retourne donc une erreur 405 car la méthode GET n'est pas autorisée pour cette route

## 🔧 Solution

### Pour tester l'API depuis un navigateur

Utilisez un outil comme **Postman** ou **curl** pour faire une requête POST :

```bash
curl -X POST https://www.adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### Route de test ajoutée

Une route de test a été ajoutée pour vérifier que l'API fonctionne :

**GET** `https://www.adis-ci.net/api/test`

Cette route retourne :
```json
{
  "success": true,
  "message": "API ADIS fonctionne correctement",
  "version": "1.0",
  "endpoints": {
    "login": "POST /api/login",
    "register": "POST /api/register",
    "supports": "GET /api/supports"
  }
}
```

### Pour l'application mobile

L'application mobile Flutter utilise correctement la méthode POST :

```dart
final response = await dio.post(
  '${ApiConstants.baseUrl}/login',
  data: {
    'email': email,
    'password': password,
  },
);
```

## 📝 Méthodes HTTP autorisées

| Route | Méthode | Description |
|-------|---------|-------------|
| `/api/login` | **POST** | Connexion (nécessite email et password) |
| `/api/register` | **POST** | Inscription |
| `/api/supports` | **GET** | Liste des supports publics |
| `/api/test` | **GET** | Test de l'API |

## ✅ Vérification

1. **Tester la route de test** :
   ```
   https://www.adis-ci.net/api/test
   ```
   Devrait retourner un JSON avec `success: true`

2. **Tester le login avec curl** :
   ```bash
   curl -X POST https://www.adis-ci.net/api/login \
     -H "Content-Type: application/json" \
     -d '{"email":"votre@email.com","password":"votre_mot_de_passe"}'
   ```

3. **Tester depuis l'application mobile** :
   - L'application mobile devrait fonctionner correctement car elle utilise POST

## 🚀 Statut

- ✅ Route `/api/login` correctement configurée (POST)
- ✅ Route de test `/api/test` ajoutée (GET)
- ✅ Préfixe API configuré dans `bootstrap/app.php`
- ✅ Application mobile configurée pour utiliser POST

L'erreur 405 est normale et attendue lorsque vous accédez à `/api/login` via un navigateur. L'API fonctionne correctement pour les requêtes POST depuis l'application mobile.









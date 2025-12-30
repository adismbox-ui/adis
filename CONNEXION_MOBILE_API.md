# Guide de Connexion de l'Application Mobile à l'API

## ✅ Configuration Complète

### 1. Configuration de l'URL de l'API

L'application mobile est configurée pour se connecter à :
- **Production** : `https://adis-ci.net/api`
- **Développement** : `http://192.168.1.5:8000/api` (commenté)

Fichier : `adis_mobile/lib/services/api_constants.dart`

### 2. Configuration CORS

CORS est configuré pour permettre toutes les origines (nécessaire pour les applications mobiles) :

Fichier : `adis/config/cors.php`
- `allowed_origins` : `['*']`
- `supports_credentials` : `true`

### 3. Routes API Disponibles

#### Authentification
- `POST /api/login` - Connexion
- `POST /api/register` - Inscription  
- `POST /api/logout` - Déconnexion (protégé)
- `GET /api/user` - Utilisateur connecté (protégé)

#### Apprenant (protégé)
- `GET /api/apprenant/mes-formations` - Formations
- `GET /api/apprenant/modules` - Modules
- `GET /api/apprenant/mes-documents` - Documents
- `GET /api/apprenant/questionnaires` - Questionnaires
- `GET /api/apprenant/questionnaires/{id}` - Détail questionnaire
- `POST /api/apprenant/questionnaires/{id}/repondre` - Répondre
- `GET /api/apprenant/resultats-questionnaires` - Résultats
- `GET /api/apprenant/profile` - Profil
- `PUT /api/apprenant/profile` - Mettre à jour profil
- `GET /api/apprenant/progression` - Progression
- `GET /api/apprenant/paiements` - Paiements

#### Admin (protégé)
- `GET /api/admin/statistiques` - Statistiques
- `GET /api/admin/utilisateurs` - Utilisateurs
- `GET /api/admin/apprenants` - Apprenants
- `GET /api/admin/formateurs` - Formateurs
- `GET /api/admin/niveaux` - Niveaux

#### Formateur (protégé)
- `GET /api/formateur/calendrier` - Calendrier
- `GET /api/formateur/modules` - Modules
- `GET /api/formateur/niveaux` - Niveaux
- `GET /api/formateur/profile` - Profil

#### Assistant (protégé)
- `GET /api/assistant/profile` - Profil
- `GET /api/assistant/apprenants` - Apprenants
- `GET /api/assistant/formateurs` - Formateurs

## 🔐 Authentification

### Connexion

```dart
import 'package:adis_mobile/lib/services/authService.dart';

final result = await AuthService.login(
  email: 'user@example.com',
  password: 'password',
);

if (result['success']) {
  final token = result['access_token'];
  final user = result['user'];
  final typeCompte = result['type_compte'];
  
  // Sauvegarder le token
  final prefs = await SharedPreferences.getInstance();
  await prefs.setString('access_token', token);
  await prefs.setString('user_type', typeCompte);
}
```

### Réponse de l'API

```json
{
  "success": true,
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "nom": "Doe",
    "prenom": "John",
    "email": "user@example.com",
    "type_compte": "apprenant",
    "sexe": "Homme",
    "telephone": "+2250123456789"
  },
  "type_compte": "apprenant",
  "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer",
  "expires_in": null
}
```

### Utilisation du Token

Toutes les requêtes authentifiées doivent inclure le token :

```dart
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:adis_mobile/lib/services/api_constants.dart';

final prefs = await SharedPreferences.getInstance();
final token = prefs.getString('access_token');

final response = await http.get(
  Uri.parse('${ApiConstants.baseUrl}/apprenant/modules'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
);
```

## 🧪 Test de Connexion

### 1. Test avec curl

```bash
# Test de connexion
curl -X POST https://adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### 2. Test depuis l'application mobile

1. Ouvrir l'application Flutter
2. Aller à la page de connexion
3. Entrer les identifiants
4. Vérifier les logs dans la console :
   ```
   [DEBUG] Login URL: https://adis-ci.net/api/login
   [DEBUG] Login response status: 200
   [DEBUG] Login response data: {...}
   ```

## 🔧 Vérification de la Configuration

### Côté Serveur (Laravel)

1. **Vérifier que l'API est accessible** :
   ```bash
   curl https://adis-ci.net/api/supports
   ```

2. **Vérifier CORS** :
   - Le fichier `config/cors.php` doit avoir `allowed_origins => ['*']`

3. **Vérifier Sanctum** :
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

### Côté Mobile (Flutter)

1. **Vérifier l'URL dans `api_constants.dart`** :
   ```dart
   static const String baseUrl = 'https://adis-ci.net/api';
   ```

2. **Vérifier les services** :
   - `AuthService` utilise `ApiConstants.baseUrl`
   - Tous les services incluent le token dans les headers

## ⚠️ Dépannage

### Erreur de Connexion

**Problème** : Impossible de se connecter à l'API

**Solutions** :
1. Vérifier l'URL dans `api_constants.dart`
2. Vérifier la connexion internet
3. Vérifier que l'API est accessible depuis le navigateur
4. Vérifier les logs Laravel : `storage/logs/laravel.log`

### Erreur CORS

**Problème** : Erreur CORS dans la console

**Solutions** :
1. Vérifier `config/cors.php` : `allowed_origins => ['*']`
2. Vider le cache : `php artisan config:clear`
3. Vérifier que le middleware CORS est actif dans `Kernel.php`

### Erreur 401 (Non autorisé)

**Problème** : Token invalide ou expiré

**Solutions** :
1. Vérifier que le token est inclus dans l'en-tête `Authorization`
2. Vérifier le format : `Bearer {token}`
3. Se reconnecter pour obtenir un nouveau token

### Erreur 500 (Erreur serveur)

**Problème** : Erreur côté serveur

**Solutions** :
1. Vérifier les logs Laravel
2. Vérifier que Sanctum est installé : `composer show laravel/sanctum`
3. Vérifier que les migrations sont exécutées

## 📝 Checklist de Connexion

- [x] URL de l'API configurée dans `api_constants.dart`
- [x] CORS configuré pour permettre toutes les origines
- [x] Sanctum installé et configuré
- [x] Routes API créées et fonctionnelles
- [x] Services Flutter configurés pour utiliser l'API
- [ ] Test de connexion réussi
- [ ] Test des endpoints protégés réussi
- [ ] Application mobile fonctionnelle

## 🚀 Prochaines Étapes

1. Tester la connexion depuis l'application mobile
2. Vérifier tous les endpoints
3. Implémenter la gestion des erreurs
4. Ajouter le refresh token si nécessaire
5. Implémenter la déconnexion automatique en cas d'expiration


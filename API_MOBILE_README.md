# API Mobile ADIS - Documentation

## 📋 Vue d'ensemble

Cette API Laravel a été créée pour permettre à l'application mobile Flutter (adis_mobile) de communiquer avec le backend Laravel.

## 🚀 Structure de l'API

### Routes API

Toutes les routes API sont définies dans `routes/api.php` et sont préfixées par `/api`.

### Authentification

L'API utilise **Laravel Sanctum** pour l'authentification par token.

#### Routes publiques (sans authentification)
- `POST /api/login` - Connexion
- `POST /api/register` - Inscription
- `GET /api/supports` - Liste des supports publics

#### Routes protégées (nécessitent un token Bearer)
Toutes les autres routes nécessitent l'en-tête `Authorization: Bearer {token}`.

### Contrôleurs API

Tous les contrôleurs API sont dans `app/Http/Controllers/Api/`:

1. **ApiAuthController** - Authentification (login, register, logout, user)
2. **ApiApprenantController** - Endpoints pour les apprenants
   - Mes formations (en cours, terminées, à venir)
   - Mes modules
   - Mes documents
   - Questionnaires (liste, détail, répondre, résultats)
   - Profil (get, update)
   - Progression
   - Paiements

3. **ApiAdminController** - Endpoints pour les administrateurs
   - Statistiques
   - Gestion utilisateurs
   - Gestion apprenants
   - Gestion formateurs
   - Gestion niveaux
   - Gestion modules
   - Transformation formateur en assistant

4. **ApiFormateurController** - Endpoints pour les formateurs
   - Calendrier
   - Modules
   - Niveaux
   - Profil
   - Apprenants
   - Documents
   - Questionnaires

5. **ApiAssistantController** - Endpoints pour les assistants
   - Profil
   - Liste apprenants
   - Liste formateurs

6. **ApiModuleController** - Gestion des modules
7. **ApiQuestionnaireController** - Gestion des questionnaires
8. **ApiPaiementController** - Gestion des paiements (CinetPay)
9. **ApiDocumentController** - Téléchargement de documents

## 🔧 Configuration

### Sanctum

L'API utilise Laravel Sanctum pour l'authentification. Le modèle `Utilisateur` utilise le trait `HasApiTokens`.

### Base URL

L'API est accessible à l'URL configurée dans l'application mobile:
- Production: `http://adis-frontend-svngue-a2806d-45-130-104-114.traefik.me/api`
- Développement: `http://192.168.1.5:8000/api`

## 📱 Utilisation depuis l'application mobile

### Exemple de connexion

```dart
final response = await http.post(
  Uri.parse('${ApiConstants.baseUrl}/login'),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'email': 'user@example.com',
    'password': 'password',
  }),
);

final data = jsonDecode(response.body);
final token = data['access_token'];
```

### Exemple de requête authentifiée

```dart
final response = await http.get(
  Uri.parse('${ApiConstants.baseUrl}/apprenant/modules'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
);
```

## 🔐 Sécurité

- Toutes les routes protégées utilisent le middleware `auth:sanctum`
- Les tokens sont générés lors de la connexion
- Les tokens peuvent être révoqués via la route `/api/logout`

## 📝 Notes importantes

1. **CinetPay**: L'intégration CinetPay dans `ApiPaiementController` nécessite une configuration supplémentaire avec les clés API CinetPay.

2. **Documents**: Les documents sont stockés dans `storage/app/public/documents/`. Assurez-vous que le lien symbolique est créé: `php artisan storage:link`

3. **Relations**: Les relations entre modèles doivent être correctement configurées pour que l'API fonctionne correctement.

## 🚀 Déploiement

L'API est prête à être déployée sur Dokploy. Assurez-vous que:

1. Les variables d'environnement sont configurées
2. Les migrations sont exécutées
3. Le lien symbolique de storage est créé
4. Sanctum est correctement configuré

## 📚 Endpoints principaux

### Apprenant
- `GET /api/apprenant/mes-formations` - Formations (en cours, terminées, à venir)
- `GET /api/apprenant/modules` - Modules de l'apprenant
- `GET /api/apprenant/mes-documents` - Documents de l'apprenant
- `GET /api/apprenant/questionnaires` - Questionnaires disponibles
- `GET /api/apprenant/questionnaires/{id}` - Détail d'un questionnaire
- `POST /api/apprenant/questionnaires/{id}/repondre` - Répondre à un questionnaire
- `GET /api/apprenant/resultats-questionnaires` - Résultats des questionnaires
- `GET /api/apprenant/profile` - Profil de l'apprenant
- `PUT /api/apprenant/profile` - Mettre à jour le profil

### Admin
- `GET /api/admin/statistiques` - Statistiques générales
- `GET /api/admin/utilisateurs` - Liste des utilisateurs
- `GET /api/admin/apprenants` - Liste des apprenants
- `GET /api/admin/formateurs` - Liste des formateurs
- `GET /api/admin/niveaux` - Liste des niveaux
- `GET /api/admin/niveaux/{id}/apprenants` - Apprenants d'un niveau
- `PUT /api/admin/apprenants/{id}/changer-niveau` - Changer le niveau d'un apprenant

### Formateur
- `GET /api/formateur/calendrier` - Calendrier du formateur
- `GET /api/formateur/modules` - Modules du formateur
- `GET /api/formateur/niveaux` - Niveaux du formateur
- `GET /api/formateur/profile` - Profil du formateur

### Assistant
- `GET /api/assistant/profile` - Profil de l'assistant
- `GET /api/assistant/apprenants` - Liste des apprenants
- `GET /api/assistant/formateurs` - Liste des formateurs

## ✅ Statut

L'API est complète et prête à être utilisée par l'application mobile Flutter.


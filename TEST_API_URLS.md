# URLs pour Tester l'API

## 🔗 URLs de Test

### 1. Test de Base (GET) - Vérifier que l'API fonctionne

```
https://www.adis-ci.net/api/test
```

**Méthode** : GET  
**Résultat attendu** : JSON avec `success: true`

**Test dans le navigateur** :
- Ouvrez : https://www.adis-ci.net/api/test
- Vous devriez voir un JSON avec les informations de l'API

### 2. Login (POST) - Connexion

```
https://www.adis-ci.net/api/login
```

**Méthode** : POST  
**Headers requis** :
- `Content-Type: application/json`
- `Accept: application/json`

**Body (JSON)** :
```json
{
  "email": "adis.mbox@gmail.com",
  "password": "@Adis_2025@"
}
```

**Test avec curl** :
```bash
curl -X POST https://www.adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"adis.mbox@gmail.com","password":"@Adis_2025@"}' \
  -v
```

**Test avec Postman** :
1. Méthode : POST
2. URL : `https://www.adis-ci.net/api/login`
3. Headers :
   - `Content-Type: application/json`
   - `Accept: application/json`
4. Body (raw JSON) :
   ```json
   {
     "email": "adis.mbox@gmail.com",
     "password": "@Adis_2025@"
   }
   ```

### 3. Supports (GET) - Liste des supports

```
https://www.adis-ci.net/api/supports
```

**Méthode** : GET  
**Résultat attendu** : Liste des supports disponibles

### 4. Register (POST) - Inscription

```
https://www.adis-ci.net/api/register
```

**Méthode** : POST  
**Body (JSON)** :
```json
{
  "prenom": "Test",
  "nom": "User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "sexe": "Homme",
  "type_compte": "apprenant"
}
```

## 🧪 Test Rapide dans le Navigateur

### Test 1 : Vérifier que l'API répond

Ouvrez dans votre navigateur :
```
https://www.adis-ci.net/api/test
```

**Résultat attendu** :
```json
{
  "success": true,
  "message": "API ADIS fonctionne correctement",
  "version": "1.0",
  "endpoints": {
    "login": "POST /api/login",
    "register": "POST /api/register",
    "supports": "GET /api/supports",
    "test": "GET /api/test"
  }
}
```

### Test 2 : Tester le Login (nécessite un outil comme Postman ou curl)

Le login ne peut pas être testé directement dans le navigateur car c'est une requête POST.

## 📱 Depuis l'Application Mobile

L'application mobile utilise automatiquement :
- **Base URL** : `https://www.adis-ci.net/api`
- **Login** : `https://www.adis-ci.net/api/login`

## ⚠️ Erreurs Possibles

### Erreur 405 (Method Not Allowed)
- **Cause** : Utilisation de GET au lieu de POST
- **Solution** : Utiliser POST pour `/api/login`

### Erreur 500 (Server Error)
- **Cause** : Problème serveur (base de données, configuration, etc.)
- **Solution** : Vérifier les logs : `storage/logs/laravel.log`

### Erreur 401 (Unauthorized)
- **Cause** : Identifiants invalides
- **Solution** : Vérifier email et mot de passe

### Erreur 403 (Forbidden)
- **Cause** : Compte désactivé, email non vérifié, ou formateur non validé
- **Solution** : Vérifier le statut du compte dans la base de données

## 🔍 Vérification des Logs

Sur le serveur, pour voir les requêtes API :

```bash
cd /var/www/html
tail -f storage/logs/laravel.log | grep "API"
```









# Explication : Erreur 405 sur /api/login

## 🔍 Pourquoi l'Erreur 405 ?

L'erreur **405 Method Not Allowed** est **normale et attendue** quand vous accédez à `/api/login` via un navigateur.

### Raison

- La route `/api/login` est définie en **POST** uniquement
- Les navigateurs font des requêtes **GET** par défaut
- Laravel retourne 405 car GET n'est pas autorisé pour cette route

## ✅ Solution

### Option 1 : Utiliser la Route de Test (GET)

Pour tester que l'API fonctionne dans le navigateur :
```
https://www.adis-ci.net/api/test
```

### Option 2 : Utiliser Postman ou curl (POST)

Pour tester le login, vous devez faire une requête **POST** :

**Avec curl** :
```bash
curl -X POST https://www.adis-ci.net/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"adis.mbox@gmail.com","password":"@Adis_2025@"}'
```

**Avec Postman** :
1. Méthode : **POST**
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

### Option 3 : Depuis l'Application Mobile

L'application mobile fait automatiquement des requêtes POST, donc elle fonctionnera correctement.

## 📝 Routes Disponibles

### Routes GET (accessibles dans le navigateur)
- `GET /api` - Informations sur l'API
- `GET /api/test` - Test de l'API
- `GET /api/supports` - Liste des supports

### Routes POST (nécessitent Postman/curl/app mobile)
- `POST /api/login` - Connexion
- `POST /api/register` - Inscription

## 🎯 Résumé

- ✅ **405 sur GET /api/login** = Normal, utilisez POST
- ✅ **Testez avec GET /api/test** = Devrait fonctionner
- ✅ **Login depuis l'app mobile** = Fonctionne (POST automatique)

L'erreur 405 n'est **pas un bug**, c'est le comportement attendu pour protéger l'API.


# Fix: URLs HTTPS dans les réponses API

## Problème
Les URLs générées dans les réponses API utilisaient `http://` au lieu de `https://`, même si l'application est accessible en HTTPS.

## Solutions appliquées

### 1. Fonction helper `https_url()`
Ajout d'une fonction helper dans `routes/api.php` qui force toujours HTTPS :
```php
function https_url($path = null, $parameters = [], $secure = true) {
    $url = url($path, $parameters, $secure);
    return str_replace('http://', 'https://', $url);
}
```

### 2. Utilisation dans les routes
Toutes les routes GET (`/api/login`, `/api/register`, `/api/`) utilisent maintenant `https_url()` au lieu de `url()`.

### 3. AppServiceProvider
Modification de `AppServiceProvider` pour forcer HTTPS en production :
```php
if (config('app.env') === 'production') {
    \Illuminate\Support\Facades\URL::forceScheme('https');
}
```

### 4. Middleware TrustProxies
Création du middleware `TrustProxies` pour détecter correctement HTTPS derrière Traefik :
- Trust tous les proxies (`$proxies = '*'`)
- Détecte les headers `X-Forwarded-Proto` pour identifier HTTPS

## Vérification dans Dokploy

### 1. Vérifier APP_URL
Assurez-vous que `APP_URL` est configuré avec HTTPS dans les variables d'environnement Dokploy :
```
APP_URL=https://www.adis-ci.net
```

### 2. Après déploiement
Videz le cache de configuration :
```bash
cd /var/www/html
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### 3. Test
Testez les routes GET :
- `https://www.adis-ci.net/api/register` → Doit retourner des URLs en HTTPS
- `https://www.adis-ci.net/api/login` → Doit retourner des URLs en HTTPS
- `https://www.adis-ci.net/api/` → Doit retourner des URLs en HTTPS

## Résultat attendu

Les réponses JSON doivent maintenant contenir des URLs en HTTPS :
```json
{
  "success": false,
  "error": "Méthode non autorisée",
  "url": "https://www.adis-ci.net/api/register",
  "example": {
    "url": "https://www.adis-ci.net/api/register"
  }
}
```

Au lieu de :
```json
{
  "url": "http://www.adis-ci.net/api/register"
}
```


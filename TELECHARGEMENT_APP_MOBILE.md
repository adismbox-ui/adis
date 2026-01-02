# 📱 Téléchargement de l'Application Mobile

## ✅ Fonctionnalité implémentée

Un bouton "Télécharger l'app" a été ajouté sur la page d'accueil du site web (`/`). Ce bouton permet aux visiteurs de télécharger l'application mobile ADIS.

## 🎨 Design

Le bouton est visible dans la section des actions principales de la page d'accueil, à côté des boutons :
- S'inscrire
- Se connecter
- Faire un don

Le bouton a un style distinct avec :
- Icône de téléphone mobile
- Animation au survol
- Couleur dégradée verte (cohérente avec le thème ADIS)

## 📂 Structure des fichiers

### Fichiers créés/modifiés :

1. **Contrôleur** : `app/Http/Controllers/MobileAppController.php`
   - Gère le téléchargement de l'APK
   - Redirige vers les stores (Google Play, App Store)

2. **Vue** : `resources/views/download-app.blade.php`
   - Page dédiée au téléchargement
   - Interface moderne et responsive

3. **Routes** : `routes/web.php`
   - `/download-app` : Page de téléchargement
   - `/download-app/apk` : Téléchargement direct de l'APK
   - `/download-app/store` : Redirection vers les stores

4. **Page d'accueil** : `resources/views/welcome.blade.php`
   - Bouton ajouté dans la section des actions

## 📥 Comment ajouter le fichier APK

### Option 1 : Dans le dossier public (recommandé)

1. Créer le dossier `public/app/` s'il n'existe pas :
   ```bash
   mkdir -p public/app
   ```

2. Placer votre fichier APK dans ce dossier :
   ```
   public/app/adis-mobile.apk
   ```

### Option 2 : Dans le storage

1. Placer le fichier APK dans :
   ```
   storage/app/public/adis-mobile.apk
   ```

2. Créer le lien symbolique si nécessaire :
   ```bash
   php artisan storage:link
   ```

## 🔗 Configuration des stores

Pour rediriger vers Google Play Store ou App Store, modifiez les URLs dans `MobileAppController.php` :

```php
// Ligne 40 : Google Play Store
$playStoreUrl = 'https://play.google.com/store/apps/details?id=com.example.adis_mobile';

// Ligne 44 : App Store
$appStoreUrl = 'https://apps.apple.com/app/adis-mobile';
```

Remplacez ces URLs par les liens réels de votre application sur les stores.

## 🎯 Utilisation

### Pour les visiteurs :

1. Visiter la page d'accueil : `https://www.adis-ci.net/`
2. Cliquer sur le bouton "Télécharger l'app"
3. Sur la page de téléchargement :
   - Cliquer sur "Télécharger l'APK" pour télécharger directement
   - Cliquer sur "Disponible sur Google Play" pour être redirigé vers le store

### Détection automatique :

- **Android** : Redirige automatiquement vers Google Play Store
- **iOS** : Redirige automatiquement vers App Store
- **Autres** : Affiche la page de téléchargement avec les deux options

## 🚀 Déploiement

1. **Placer le fichier APK** dans `public/app/adis-mobile.apk`
2. **Tester localement** : `http://localhost/download-app`
3. **Déployer** sur le serveur
4. **Vérifier** que le fichier APK est accessible

## 📝 Notes importantes

- Le fichier APK doit être nommé exactement `adis-mobile.apk`
- Assurez-vous que le fichier a les permissions de lecture appropriées
- Pour la production, considérez l'utilisation d'un CDN pour servir le fichier APK
- Mettez à jour les liens des stores une fois l'application publiée

## 🔒 Sécurité

- Le téléchargement est public (pas d'authentification requise)
- Assurez-vous que seul le fichier APK officiel est disponible
- Vérifiez régulièrement que le fichier n'a pas été modifié

## 🐛 Dépannage

### Le téléchargement ne fonctionne pas :

1. Vérifier que le fichier existe : `public/app/adis-mobile.apk`
2. Vérifier les permissions du fichier
3. Vérifier les logs Laravel : `storage/logs/laravel.log`
4. Tester l'URL directement : `https://www.adis-ci.net/app/adis-mobile.apk`

### Le bouton n'apparaît pas :

1. Vider le cache : `php artisan view:clear`
2. Vérifier que les modifications sont bien déployées
3. Vider le cache du navigateur


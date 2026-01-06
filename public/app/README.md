# Application Mobile ADIS

## Installation

Pour activer le téléchargement de l'application mobile depuis le site web :

1. Placez le fichier APK de l'application dans ce dossier avec le nom `adis.apk`
2. Le fichier sera automatiquement téléchargé lorsque les utilisateurs cliquent sur le bouton "Télécharger l'app" sur la page d'accueil

## Structure attendue

```
public/
  app/
    adis.apk  ← Placez votre fichier APK ici
```

## Alternative : Google Play Store

Si votre application est publiée sur Google Play Store, modifiez la variable `$playStoreUrl` dans `routes/web.php` pour pointer vers votre application.



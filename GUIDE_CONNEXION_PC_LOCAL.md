# GUIDE DE CONNEXION ENTRE DEUX PC EN LOCAL

## 📋 Prérequis
- Les deux PC doivent être connectés au même réseau WiFi ou Ethernet
- Windows Defender/Firewall doit autoriser la connexion

## 🚀 Méthode 1 : Serveur de fichiers simple

### Sur le PC principal (192.168.1.12) :

1. **Double-cliquez sur `start_file_server.bat`** ou
2. **Ouvrez PowerShell et exécutez :**
   ```powershell
   .\start_file_server.ps1
   ```

3. **Le serveur démarre sur le port 8000**

### Sur l'autre PC :

1. **Ouvrez votre navigateur**
2. **Tapez :** `http://192.168.1.12:8000`
3. **Vous verrez la liste des fichiers de votre projet**

## 🔧 Méthode 2 : Partage de dossiers Windows

### Sur le PC principal :

1. **Clic droit sur le dossier `adis`**
2. **Propriétés → Partage → Partager**
3. **Ajoutez `Tout le monde` avec permissions de lecture/écriture**
4. **Notez le chemin de partage**

### Sur l'autre PC :

1. **Ouvrez l'Explorateur**
2. **Dans la barre d'adresse :** `\\192.168.1.12\adis`
3. **Ou utilisez le nom du PC :** `\\ROG-PC\adis`

## 🌐 Méthode 3 : Serveur Laravel (Recommandée pour le développement)

### Sur le PC principal :

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Sur l'autre PC :

1. **Ouvrez le navigateur**
2. **Allez sur :** `http://192.168.1.12:8000`

## 📱 Méthode 4 : Application mobile/tablette

### Utilisez une app comme :
- **ES File Explorer** (Android)
- **FileBrowser** (iOS)
- **Total Commander** avec plugin FTP

## 🔒 Sécurité

### Autoriser le pare-feu :
1. **Panneau de configuration → Système et sécurité → Pare-feu Windows**
2. **Autoriser une application via le pare-feu**
3. **Ajoutez Python ou votre serveur**

### Vérifier la connexion :
```bash
ping 192.168.1.12
```

## 🚨 Dépannage

### Si ça ne marche pas :

1. **Vérifiez que les deux PC sont sur le même réseau**
2. **Désactivez temporairement le pare-feu**
3. **Vérifiez l'adresse IP avec `ipconfig`**
4. **Redémarrez le routeur si nécessaire**

### Commandes utiles :
```bash
# Voir les connexions actives
netstat -an | findstr :8000

# Tester la connectivité
telnet 192.168.1.12 8000

# Voir les partages
net share
```

## 📁 Structure des fichiers partagés

Votre projet `adis` contient :
- **Code source Laravel** (`app/`, `resources/`, `routes/`)
- **Base de données** (`database/`)
- **Fichiers publics** (`public/`)
- **Configuration** (`config/`, `.env`)

## 🎯 Utilisation recommandée

- **Développement :** Méthode 3 (Laravel)
- **Partage de fichiers :** Méthode 1 ou 2
- **Accès mobile :** Méthode 4

## 📞 Support

En cas de problème, vérifiez :
1. La connectivité réseau
2. Les paramètres de pare-feu
3. Les permissions de partage
4. Les logs d'erreur

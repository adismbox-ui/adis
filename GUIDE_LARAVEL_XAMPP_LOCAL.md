# GUIDE LARAVEL + XAMPP EN LOCAL

## 🎯 Objectif
Permettre à un autre PC d'accéder à votre projet Laravel hébergé sur XAMPP via le réseau local.

## ✅ État actuel
- **PC Serveur :** 192.168.1.12
- **Apache :** Port 80 (déjà configuré)
- **Pare-feu :** Apache déjà autorisé
- **Projet :** Laravel dans votre dossier actuel

## 🚀 Étapes suivantes

### **Étape 3 : Vérifier la configuration XAMPP**

1. **Ouvrez XAMPP Control Panel**
2. **Démarrez Apache et MySQL**
3. **Vérifiez que les services sont actifs (vert)**

### **Étape 4 : Configurer le DocumentRoot**

#### Option A : DocumentRoot principal
Dans `C:\xampp\apache\conf\httpd.conf`, trouvez :
```apache
DocumentRoot "C:/xampp/htdocs"
<Directory "C:/xampp/htdocs">
```

Remplacez par :
```apache
DocumentRoot "C:/Users/ROG/Documents/adis/public"
<Directory "C:/Users/ROG/Documents/adis/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

#### Option B : Virtual Host (Recommandée)
Dans `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, ajoutez :
```apache
<VirtualHost *:80>
    DocumentRoot "C:/Users/ROG/Documents/adis/public"
    ServerName adis.local
    ServerAlias 192.168.1.12
    <Directory "C:/Users/ROG/Documents/adis/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### **Étape 5 : Redémarrer Apache**
1. **Dans XAMPP Control Panel, cliquez sur "Stop" pour Apache**
2. **Puis cliquez sur "Start"**
3. **Vérifiez que le statut devient vert**

### **Étape 6 : Tester l'accès**

#### Sur votre PC serveur :
```
http://localhost
http://adis.local (si virtual host)
```

#### Depuis l'autre PC :
```
http://192.168.1.12
http://192.168.1.12/public (si DocumentRoot principal)
```

## 🔧 Configuration avancée

### **Fichier .htaccess**
Vérifiez que votre fichier `public/.htaccess` contient :
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### **Permissions des dossiers**
```bash
# Donner les bonnes permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 🚨 Dépannage

### **Erreur 403 Forbidden**
- Vérifiez les permissions du dossier
- Vérifiez la configuration Apache
- Vérifiez que `mod_rewrite` est activé

### **Erreur 500 Internal Server Error**
- Vérifiez les logs Apache dans `C:\xampp\apache\logs\error.log`
- Vérifiez la configuration Laravel
- Vérifiez que les extensions PHP sont activées

### **Page blanche**
- Activez l'affichage des erreurs dans `php.ini`
- Vérifiez que `mod_rewrite` est activé
- Vérifiez la configuration des virtual hosts

## 📱 Test depuis l'autre PC

### **1. Vérifiez la connectivité**
```bash
ping 192.168.1.12
```

### **2. Testez l'accès web**
- Ouvrez le navigateur
- Tapez : `http://192.168.1.12`
- Vous devriez voir votre application Laravel

### **3. Testez les fonctionnalités**
- Navigation entre les pages
- Connexion à la base de données
- Upload de fichiers (si applicable)

## 🎯 Utilisation recommandée

- **Développement :** Utilisez le virtual host `adis.local`
- **Test réseau :** Utilisez l'IP `192.168.1.12`
- **Production :** Configurez un vrai nom de domaine

## 📞 Support

En cas de problème :
1. Vérifiez les logs Apache
2. Vérifiez la configuration XAMPP
3. Testez la connectivité réseau
4. Vérifiez les permissions des dossiers

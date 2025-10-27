# 📋 Fonctionnalité Devis - ADIS

## 🎯 Description

Cette fonctionnalité permet aux utilisateurs de demander un devis détaillé lors de la soumission d'un don. Si l'utilisateur coche "Oui" pour recevoir un devis, un email personnalisé sera automatiquement envoyé à l'adresse email spécifiée.

## ✨ Fonctionnalités

### 🔘 Section Devis dans le formulaire
- **Choix radio** : L'utilisateur peut choisir s'il souhaite recevoir un devis ou non
- **Champ email** : Si "Oui" est sélectionné, un champ email apparaît automatiquement
- **Validation** : Le champ email est obligatoire si le devis est demandé

### 📧 Email automatique
- **Template personnalisé** : Email HTML avec le design ADIS
- **Informations détaillées** : Détails du don, du projet et impact
- **Notifications admin** : L'administrateur est notifié de chaque devis envoyé
- **Gestion d'erreurs** : Notifications en cas d'échec d'envoi

### 🎨 Interface utilisateur
- **Design responsive** : S'adapte à tous les écrans
- **Animations** : Transitions fluides pour l'affichage des champs
- **Validation en temps réel** : Feedback immédiat à l'utilisateur

## 🚀 Installation et configuration

### 1. Fichiers modifiés
- `resources/views/projets/don.blade.php` - Formulaire de don avec section devis
- `app/Http/Controllers/DonController.php` - Logique de traitement et envoi
- `app/Mail/DevisNotification.php` - Classe Mailable pour l'email
- `resources/views/emails/devis-notification.blade.php` - Template email

### 2. Configuration email
Assurez-vous que la configuration email de Laravel est correctement configurée dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=adis.mbox@gmail.com
MAIL_FROM_NAME="ADIS"
```

### 3. Base de données
Aucune modification de la base de données n'est nécessaire. La fonctionnalité utilise les champs existants du formulaire.

## 📱 Utilisation

### Pour l'utilisateur
1. Remplir le formulaire de don
2. Dans la section "Devis", choisir "Oui, je souhaite recevoir un devis"
3. Saisir l'adresse email pour recevoir le devis
4. Soumettre le formulaire
5. Recevoir le devis par email dans les 24h

### Pour l'administrateur
- **Notifications** : Recevoir des notifications pour chaque devis envoyé
- **Suivi** : Voir l'historique des devis dans le tableau de bord
- **Gestion d'erreurs** : Être alerté en cas de problème d'envoi

## 🧪 Tests

### Test de la fonctionnalité
Exécutez le script de test pour vérifier que tout fonctionne :

```bash
php test_devis.php
```

### Test en conditions réelles
1. Aller sur la page de don : `/projets/don`
2. Remplir le formulaire avec des données de test
3. Cocher "Oui" pour le devis
4. Saisir une adresse email valide
5. Soumettre le formulaire
6. Vérifier la réception de l'email

## 🔧 Personnalisation

### Modifier le template email
Le template se trouve dans `resources/views/emails/devis-notification.blade.php`

### Modifier le style du formulaire
Les styles CSS sont dans la section `<style>` du fichier `don.blade.php`

### Modifier la logique d'envoi
La logique se trouve dans la méthode `envoyerDevis()` du `DonController`

## 📊 Notifications

### Types de notifications créées
- **`devis_envoye`** : Devis envoyé avec succès
- **`erreur_devis`** : Erreur lors de l'envoi du devis

### Données des notifications
Chaque notification contient :
- ID du don
- Nom du donateur
- Email du devis
- Montant du don
- Projet concerné
- URL d'action

## 🚨 Gestion d'erreurs

### Erreurs possibles
- **Problème de configuration email** : Vérifier les paramètres SMTP
- **Adresse email invalide** : Validation automatique du format
- **Problème de serveur** : Logs d'erreur dans `storage/logs/laravel.log`

### Logs
Toutes les erreurs sont loggées avec :
- Message d'erreur détaillé
- Stack trace
- Contexte de l'erreur

## 🔄 Maintenance

### Vérifications régulières
- Tester l'envoi d'emails
- Vérifier les logs d'erreur
- Contrôler les notifications admin
- Valider la configuration email

### Mises à jour
- Maintenir la compatibilité avec les nouvelles versions de Laravel
- Tester après chaque mise à jour
- Sauvegarder les configurations personnalisées

## 📞 Support

Pour toute question ou problème :
- **Email** : adis.mbox@gmail.com
- **Téléphone** : +225 0704830462
- **Site web** : www.adis.org

---

**Version** : 1.0  
**Date** : {{ date('d/m/Y') }}  
**Développeur** : ADIS Team 
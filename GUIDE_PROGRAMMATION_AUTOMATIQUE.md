# 🚀 Guide de la Programmation Automatique des Questionnaires

## 📋 Vue d'ensemble

Le système permet de programmer l'envoi automatique de questionnaires à une date et heure spécifiques. Quand l'heure arrive, les questionnaires s'affichent automatiquement sur la page `http://127.0.0.1:8000/questionnaire_test`.

## 🎯 Fonctionnalités

### ✅ Programmation Automatique
- **Création** : Programmer un questionnaire avec date et heure d'envoi
- **Envoi automatique** : Le questionnaire est envoyé à l'heure programmée
- **Affichage automatique** : Les questionnaires apparaissent sur la page des apprenants

### ✅ Contrôle d'accès
- **Modules payés** : Seuls les apprenants ayant payé le module voient le questionnaire
- **Niveau correspondant** : Seuls les apprenants du bon niveau voient le questionnaire
- **Date d'envoi respectée** : Les questionnaires n'apparaissent qu'après l'envoi

## 📅 Comment ça fonctionne

### 1. Création d'un questionnaire programmé

1. **Accéder à** : `http://127.0.0.1:8000/questionnaires/create`
2. **Remplir les informations** :
   - Titre et description
   - Module concerné
   - Niveau
   - Session de formation
3. **Programmation automatique** :
   - Choisir la session de formation
   - Définir la date et heure d'envoi
4. **Créer le questionnaire**

### 2. Envoi automatique

Le système vérifie toutes les 5 minutes s'il y a des questionnaires à envoyer :
- **Date d'envoi atteinte** : Le questionnaire est marqué comme envoyé
- **Notifications** : Les apprenants concernés reçoivent une notification
- **Base de données** : Le statut `envoye` passe à `true`

### 3. Affichage pour les apprenants

Sur `http://127.0.0.1:8000/questionnaire_test` :
- **Filtrage automatique** : Seuls les questionnaires envoyés et à la bonne date sont affichés
- **Contrôle d'accès** : Seuls les apprenants ayant payé le module voient le questionnaire
- **Interface moderne** : Affichage avec animations et design responsive

## 🛠️ Configuration

### 1. Configuration du Cron Job

Pour l'envoi automatique, configurez un cron job :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (vérification toutes les 5 minutes)
*/5 * * * * cd /path/to/your/project && php artisan content:send-scheduled >> storage/logs/cron.log 2>&1
```

### 2. Test manuel

Pour tester le système :

```bash
# Test complet du système
php test_complete_system.php

# Test en temps réel (vérification toutes les 30 secondes)
php test_real_time.php

# Exécution manuelle de l'envoi
php run_automatic_send.php
```

## 📝 Utilisation étape par étape

### Étape 1 : Créer un questionnaire programmé

1. **Connectez-vous** en tant qu'admin/formateur
2. **Allez sur** : `http://127.0.0.1:8000/questionnaires/create`
3. **Remplissez** :
   - Titre : "Questionnaire de test"
   - Description : "Test de programmation automatique"
   - Module : Choisissez un module
   - Niveau : Choisissez un niveau
   - Session : Choisissez une session
4. **Programmation automatique** :
   - Date et heure d'envoi : Définissez une date future (ex: dans 5 minutes)
5. **Ajoutez des questions** et créez le questionnaire

### Étape 2 : Attendre l'envoi automatique

Le système vérifie automatiquement toutes les 5 minutes. Vous pouvez :

- **Attendre** que le cron job s'exécute
- **Tester manuellement** avec `php run_automatic_send.php`
- **Surveiller** avec `php test_real_time.php`

### Étape 3 : Vérifier l'affichage

1. **Connectez-vous** en tant qu'apprenant
2. **Allez sur** : `http://127.0.0.1:8000/questionnaire_test`
3. **Vérifiez** que le questionnaire apparaît

## 🔧 Scripts de test

### `test_complete_system.php`
Test complet du système avec création de données de test.

### `test_real_time.php`
Surveillance en temps réel avec vérification toutes les 30 secondes.

### `run_automatic_send.php`
Exécution manuelle de l'envoi automatique.

### `setup_cron.php`
Aide à la configuration du cron job.

## 📊 Logs et surveillance

### Logs automatiques
Les logs sont sauvegardés dans `storage/logs/cron.log`

### Surveillance en temps réel
```bash
# Voir les logs en temps réel
tail -f storage/logs/cron.log

# Test en temps réel
php test_real_time.php
```

## 🚨 Dépannage

### Problème : Le questionnaire n'apparaît pas

1. **Vérifiez** que la date d'envoi est atteinte
2. **Vérifiez** que `envoye = true` en base de données
3. **Vérifiez** que l'apprenant a payé le module
4. **Vérifiez** que l'apprenant est du bon niveau

### Problème : L'envoi automatique ne fonctionne pas

1. **Vérifiez** que le cron job est configuré
2. **Testez** manuellement avec `php run_automatic_send.php`
3. **Vérifiez** les logs dans `storage/logs/cron.log`

### Problème : Erreurs dans les logs

1. **Vérifiez** les permissions des fichiers
2. **Vérifiez** la configuration de la base de données
3. **Vérifiez** que tous les modèles existent

## ✅ Vérification du bon fonctionnement

1. **Créez** un questionnaire programmé pour dans 5 minutes
2. **Attendez** que l'heure arrive
3. **Vérifiez** que `envoye = true` en base de données
4. **Connectez-vous** en tant qu'apprenant
5. **Vérifiez** que le questionnaire apparaît sur `/questionnaire_test`

## 🎉 Résultat attendu

Quand tout fonctionne correctement :
- ✅ Les questionnaires sont programmés avec succès
- ✅ L'envoi automatique fonctionne à l'heure programmée
- ✅ Les questionnaires apparaissent automatiquement pour les apprenants
- ✅ Seuls les apprenants autorisés voient les questionnaires
- ✅ L'interface est moderne et responsive 
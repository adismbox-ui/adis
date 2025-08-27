# 🎯 Guide d'Utilisation - Programmation Automatique des Questionnaires

## 🚀 Comment ça fonctionne

Quand vous créez un questionnaire avec une date et heure d'envoi, il s'affiche automatiquement sur `/questionnaire_test` quand cette heure arrive.

## 📋 Étapes pour créer un questionnaire programmé

### Étape 1 : Créer le questionnaire
1. **Allez sur** : `http://127.0.0.1:8000/questionnaires/create`
2. **Remplissez** les informations :
   - Titre du questionnaire
   - Description
   - Module concerné
   - Niveau
   - Session de formation

### Étape 2 : Programmer l'envoi automatique
Dans la section **"Programmation automatique"** :
1. **Choisissez** la session de formation
2. **Définissez** la date et heure d'envoi (ex: dans 5 minutes)
3. **Cliquez** sur "Créer le questionnaire"

### Étape 3 : Le questionnaire s'affiche automatiquement
- Le questionnaire reste invisible jusqu'à l'heure programmée
- À l'heure exacte, il devient visible sur `/questionnaire_test`
- Les apprenants peuvent alors le voir et y répondre

## 🧪 Test en temps réel

### Test 1 : Vérifier l'état actuel
```bash
php test_automatic_display.php
```

### Test 2 : Surveiller en temps réel
```bash
php monitor_questionnaire_display.php
```

Ce script vous montre :
- ✅ Les questionnaires qui viennent d'être envoyés
- 📋 Les questionnaires disponibles pour les apprenants
- ⏳ Les questionnaires programmés (pas encore envoyés)

## 📊 Exemple concret

### Création d'un questionnaire
1. **Allez sur** : `http://127.0.0.1:8000/questionnaires/create`
2. **Remplissez** :
   - Titre : "Test Programmation Automatique"
   - Description : "Questionnaire de test"
   - Module : Langue Arabe
   - Niveau : Niveau 1A
   - Session : Session Test
3. **Programmation automatique** :
   - Date d'envoi : Dans 2 minutes
4. **Ajoutez** quelques questions
5. **Créez** le questionnaire

### Résultat attendu
- **Immédiatement** : Le questionnaire n'apparaît pas sur `/questionnaire_test`
- **Dans 2 minutes** : Le questionnaire apparaît automatiquement
- **Les apprenants** peuvent le voir et y répondre

## 🔧 Scripts de surveillance

### 1. Vérification rapide
```bash
php check_all_questionnaires.php
```

### 2. Surveillance en temps réel
```bash
php monitor_questionnaire_display.php
```

### 3. Test complet
```bash
php test_automatic_display.php
```

## 🎯 Conditions d'affichage

Un questionnaire s'affiche sur `/questionnaire_test` seulement si :

1. ✅ **Date d'envoi atteinte** : `date_envoi <= maintenant`
2. ✅ **Questionnaire envoyé** : `envoye = true`
3. ✅ **Module payé** : L'apprenant a payé le module
4. ✅ **Niveau correspondant** : L'apprenant est du bon niveau

## 🚨 Dépannage

### Problème : Le questionnaire n'apparaît pas

1. **Vérifiez** que la date d'envoi est atteinte
2. **Vérifiez** que `envoye = true`
3. **Vérifiez** que l'apprenant a payé le module
4. **Vérifiez** que l'apprenant est du bon niveau

### Commande de diagnostic
```bash
php check_all_questionnaires.php
```

### Correction automatique
```bash
php fix_questionnaire_display.php
```

## 📱 Interface utilisateur

### Pour les formateurs
- **Création** : `/questionnaires/create`
- **Gestion** : `/questionnaires` (liste des questionnaires)
- **Programmation** : Définir date/heure dans le formulaire

### Pour les apprenants
- **Affichage** : `/questionnaire_test`
- **Réponse** : Cliquer sur "Répondre" pour chaque questionnaire

## ⏰ Configuration automatique

Pour que l'envoi soit vraiment automatique, configurez le cron job :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (vérification toutes les 5 minutes)
*/5 * * * * cd /path/to/your/project && php artisan content:send-scheduled >> storage/logs/cron.log 2>&1
```

## 🎉 Résultat final

Quand tout fonctionne correctement :

1. **Vous créez** un questionnaire avec une date d'envoi
2. **Le système attend** jusqu'à cette heure
3. **Le questionnaire apparaît** automatiquement sur `/questionnaire_test`
4. **Les apprenants** peuvent le voir et y répondre
5. **Tout est automatique** ! 🚀

## 📞 Support

Si vous avez des problèmes :
1. **Exécutez** les scripts de diagnostic
2. **Vérifiez** les logs : `storage/logs/laravel.log`
3. **Testez** avec un questionnaire simple
4. **Contactez** le support technique 
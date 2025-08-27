# Guide d'Activation de la Surveillance Automatique

## 🎯 Objectif
Activer la surveillance automatique pour que les questionnaires s'affichent automatiquement à l'heure programmée.

## ✅ Vérification préalable
Le script de surveillance fonctionne correctement ! Test effectué :
- ✅ 1 questionnaire en retard trouvé et corrigé
- ✅ 2 questionnaires maintenant visibles pour l'apprenant

## 🚀 Méthodes d'activation

### Méthode 1 : Script Batch (Recommandé)
1. **Double-cliquez** sur le fichier `surveillance_automatique.bat`
2. **Une fenêtre noire s'ouvre** avec le titre "Surveillance Automatique des Questionnaires"
3. **Laissez cette fenêtre ouverte**
4. **Le système vérifiera automatiquement toutes les minutes**

### Méthode 2 : PowerShell (Alternative)
1. **Clic droit** sur `surveillance_automatique.ps1`
2. **Sélectionnez** "Exécuter avec PowerShell"
3. **Autorisez l'exécution** si demandé
4. **Laissez la fenêtre ouverte**

### Méthode 3 : Manuel (Test ponctuel)
1. **Ouvrez un terminal** dans le dossier du projet
2. **Tapez** : `php test_surveillance.php`
3. **Répétez** quand nécessaire

## 📋 Instructions détaillées

### Pour activer la surveillance automatique :

1. **Allez dans le dossier** : `C:\Users\ROG\Documents\adis`
2. **Trouvez le fichier** : `surveillance_automatique.bat`
3. **Double-cliquez** dessus
4. **Une fenêtre s'ouvre** avec ce message :
   ```
   ========================================
   SURVEILLANCE AUTOMATIQUE DES QUESTIONNAIRES
   ========================================
   
   Le systeme verifie toutes les minutes si des questionnaires
   doivent etre envoyes automatiquement.
   
   Pour arreter : Appuyez sur Ctrl+C
   ```
5. **Laissez cette fenêtre ouverte** - c'est votre surveillance automatique !

### Pour arrêter la surveillance :
- **Appuyez sur Ctrl+C** dans la fenêtre
- **Ou fermez la fenêtre**

## 🧪 Test de fonctionnement

### Test immédiat :
1. **Lancez** : `php test_surveillance.php`
2. **Vous devriez voir** :
   ```
   Questionnaires en retard : X
   ✅ Questionnaire ID X marqué comme envoyé
   Questionnaires visibles pour l'apprenant : X
   ```

### Test sur le site web :
1. **Allez sur** : `http://127.0.0.1:8000/questionnaire_test`
2. **Connectez-vous** en tant qu'apprenant
3. **Vous devriez voir** les questionnaires disponibles

## 🔧 Dépannage

### Si la fenêtre se ferme immédiatement :
1. **Ouvrez un terminal** dans le dossier
2. **Tapez** : `surveillance_automatique.bat`
3. **Lisez les messages d'erreur**

### Si PHP n'est pas reconnu :
1. **Vérifiez** que PHP est installé
2. **Vérifiez** que PHP est dans le PATH
3. **Testez** : `php --version`

### Si rien ne s'affiche sur le site :
1. **Lancez** : `php test_surveillance.php`
2. **Vérifiez** que des questionnaires sont visibles
3. **Vérifiez** que l'apprenant a payé les modules

## 📊 Monitoring

### Pour voir les logs en temps réel :
1. **Gardez la fenêtre de surveillance ouverte**
2. **Observez les messages** qui apparaissent toutes les minutes
3. **Exemple de messages** :
   ```
   [2025-08-04 15:30:00] Verification en cours...
   [2025-08-04 15:30:00] ✅ Aucun questionnaire en retard
   [2025-08-04 15:30:00] Surveillance terminée
   ```

### Pour vérifier l'état manuellement :
- **Lancez** : `php test_surveillance.php`
- **Ou allez sur** : `http://127.0.0.1:8000/questionnaire_test`

## ✅ Checklist de vérification

- [ ] Le fichier `surveillance_automatique.bat` existe
- [ ] Double-clic sur le fichier ouvre une fenêtre
- [ ] La fenêtre reste ouverte et affiche des messages
- [ ] `php test_surveillance.php` fonctionne
- [ ] Les questionnaires s'affichent sur `/questionnaire_test`

## 🆘 En cas de problème

### Problème : "Le fichier ne s'ouvre pas"
**Solution** : Utilisez la méthode PowerShell ou manuelle

### Problème : "PHP n'est pas reconnu"
**Solution** : Installez PHP ou ajoutez-le au PATH

### Problème : "Aucun questionnaire visible"
**Solution** : 
1. Lancez `php test_surveillance.php`
2. Vérifiez que l'apprenant a payé les modules
3. Vérifiez que les questionnaires ont `envoye = true`

---

**La surveillance automatique est maintenant configurée ! Double-cliquez sur `surveillance_automatique.bat` pour l'activer.** 
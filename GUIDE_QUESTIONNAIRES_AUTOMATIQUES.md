# Guide des Questionnaires Automatiques

## 🎯 Objectif
Quand vous créez un questionnaire sur `/questionnaires/create` avec une date d'envoi, il doit automatiquement apparaître sur `/questionnaire_test` à l'heure programmée.

## 🔧 Comment ça fonctionne

### 1. Création du questionnaire
- Allez sur `http://127.0.0.1:8000/questionnaires/create`
- Remplissez les informations du questionnaire
- **Important** : Dans la section "Programmation automatique", sélectionnez une date et heure d'envoi
- Le questionnaire est créé avec `envoye = false`

### 2. Envoi automatique
- À l'heure programmée, le système doit passer `envoye = true`
- Cela se fait via la commande : `php artisan content:send-scheduled`

### 3. Affichage pour les apprenants
- Sur `/questionnaire_test`, seuls les questionnaires avec `envoye = true` ET `date_envoi <= maintenant` s'affichent
- L'apprenant doit avoir payé le module et être du bon niveau

## 🚨 Problèmes courants et solutions

### Problème 1 : "Rien ne s'affiche sur questionnaire_test"

**Causes possibles :**
- Le questionnaire a `envoye = false` même après l'heure d'envoi
- L'apprenant n'a pas payé le module
- L'apprenant n'est pas du bon niveau
- La date d'envoi est dans le futur

**Solution :**
```bash
php fix_automatic_questionnaires.php
```

### Problème 2 : "Le cron ne fonctionne pas"

**Solution :**
1. Vérifiez que le cron est configuré :
```bash
*/5 * * * * cd /chemin/vers/projet && php artisan content:send-scheduled
```

2. Ou lancez manuellement :
```bash
php artisan content:send-scheduled
```

### Problème 3 : "Erreur dans les logs"

**Vérifiez :**
- `storage/logs/laravel.log`
- `storage/logs/cron.log` (si configuré)

## 🛠️ Scripts de diagnostic

### Diagnostic complet
```bash
php fix_automatic_questionnaires.php
```
- Analyse tous les questionnaires
- Identifie les problèmes
- Corrige automatiquement
- Crée un questionnaire de test si nécessaire

### Correction rapide
```bash
php auto_fix_questionnaires.php
```
- Corrige uniquement les questionnaires en retard
- Plus rapide pour l'exécution automatique

### Test manuel de l'envoi
```bash
php artisan content:send-scheduled
```
- Lance manuellement l'envoi automatique

## 📊 Vérification de l'état

### Dans la base de données
```sql
-- Questionnaires en retard
SELECT * FROM questionnaires 
WHERE date_envoi <= NOW() 
AND envoye = false;

-- Questionnaires envoyés
SELECT * FROM questionnaires 
WHERE envoye = true 
AND date_envoi <= NOW();
```

### Via Laravel Tinker
```php
// Questionnaires en retard
Questionnaire::where('date_envoi', '<=', now())->where('envoye', false)->get();

// Questionnaires disponibles pour un apprenant
$apprenant = Apprenant::first();
$moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id');
Questionnaire::whereIn('module_id', $moduleIds)
    ->where('envoye', true)
    ->where('date_envoi', '<=', now())
    ->get();
```

## 🔄 Configuration automatique

### Option 1 : Cron (recommandé)
Ajoutez à votre crontab :
```bash
*/5 * * * * cd /chemin/vers/projet && php artisan content:send-scheduled >> storage/logs/cron.log 2>&1
```

### Option 2 : Script PHP
Créez un script qui s'exécute régulièrement :
```bash
php auto_fix_questionnaires.php
```

## 🧪 Test du système

### 1. Créer un questionnaire de test
- Allez sur `/questionnaires/create`
- Programmez l'envoi dans 2 minutes
- Attendez l'heure
- Vérifiez sur `/questionnaire_test`

### 2. Forcer l'envoi
```bash
php artisan content:send-scheduled
```

### 3. Vérifier l'affichage
- Connectez-vous en tant qu'apprenant
- Allez sur `/questionnaire_test`
- Le questionnaire doit apparaître

## 📝 Logs et monitoring

### Logs à surveiller
- `storage/logs/laravel.log` : Erreurs générales
- `storage/logs/cron.log` : Logs du cron (si configuré)

### Commandes de monitoring
```bash
# Voir les derniers logs
tail -f storage/logs/laravel.log

# Vérifier l'état des questionnaires
php fix_automatic_questionnaires.php
```

## 🆘 Dépannage

### Si rien ne fonctionne
1. Vérifiez les logs Laravel
2. Lancez le diagnostic complet : `php fix_automatic_questionnaires.php`
3. Vérifiez que l'apprenant a payé le module
4. Vérifiez que le module correspond au niveau de l'apprenant

### Si le cron ne fonctionne pas
1. Vérifiez que le cron est bien configuré
2. Testez manuellement : `php artisan content:send-scheduled`
3. Vérifiez les permissions du dossier

### Si l'affichage ne fonctionne pas
1. Vérifiez que `envoye = true`
2. Vérifiez que `date_envoi <= maintenant`
3. Vérifiez les paiements de l'apprenant
4. Vérifiez le niveau de l'apprenant

## ✅ Checklist de vérification

- [ ] Le questionnaire a une `date_envoi` valide
- [ ] Le questionnaire a `envoye = true` après l'heure d'envoi
- [ ] L'apprenant a payé le module (`statut = 'valide'`)
- [ ] Le module correspond au niveau de l'apprenant
- [ ] Le cron ou script automatique fonctionne
- [ ] Pas d'erreurs dans les logs

---

**En cas de problème persistant, utilisez toujours `php fix_automatic_questionnaires.php` pour un diagnostic complet et une correction automatique.** 
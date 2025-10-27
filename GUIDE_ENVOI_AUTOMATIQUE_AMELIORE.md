# 🚀 Guide d'Envoi Automatique Amélioré des Questionnaires

## 🎯 Objectif
Système d'envoi automatique des questionnaires qui fonctionne de manière fiable :
- **Date passée** → Envoi immédiat
- **Date future** → Envoi automatique à l'heure programmée
- **En retard** → Envoi automatique même si retardé

## ⚡ Fonctionnement

### 1. Création du Questionnaire
Quand vous créez un questionnaire sur `http://127.0.0.1:8000/questionnaires/create` :

- **Si la date d'envoi est dans le passé** → Envoi immédiat
- **Si la date d'envoi est dans le futur** → Programmation automatique

### 2. Envoi Automatique
Le système surveille en continu et envoie automatiquement :
- À l'heure exacte programmée
- Même si c'est en retard (jusqu'à plusieurs heures/days)

### 3. Affichage pour les Apprenants
Sur `http://127.0.0.1:8000/questionnaire_test` :
- Seuls les questionnaires avec `envoye = true` s'affichent
- L'apprenant doit avoir payé le module
- L'apprenant doit être du bon niveau

## 🛠️ Activation du Système

### Option 1 : Surveillance Continue (Recommandée)
```bash
# Lancer la surveillance en continu
php surveillance_questionnaires_automatique.php
```

### Option 2 : Fichier Batch Windows
```bash
# Double-cliquer sur le fichier
surveillance_questionnaires.bat
```

### Option 3 : Commande Artisan
```bash
# Vérification manuelle
php artisan content:send-scheduled
```

## 📊 États des Questionnaires

| État | Description | Affichage |
|------|-------------|-----------|
| **Programmé** | `envoye = false` + `date_envoi > maintenant` | ❌ Pas visible |
| **À l'heure** | `envoye = true` + `date_envoi <= maintenant` | ✅ Visible |
| **En retard** | `envoye = false` + `date_envoi < maintenant` | ❌ Pas visible (sera envoyé automatiquement) |

## 🧪 Test du Système

### Test Rapide
```bash
# Créer un questionnaire de test
php test_envoi_automatique.php
```

### Vérification Manuelle
```bash
# Vérifier l'état des questionnaires
php artisan content:send-scheduled
```

## 🔧 Scripts Utiles

### 1. Surveillance Continue
```bash
php surveillance_questionnaires_automatique.php
```
- Vérifie toutes les 30 secondes
- Envoie automatiquement les questionnaires prêts
- Affiche les logs en temps réel

### 2. Test Complet
```bash
php test_envoi_automatique.php
```
- Crée un questionnaire de test
- Vérifie l'état actuel
- Identifie les questionnaires en retard

### 3. Vérification Manuelle
```bash
php artisan content:send-scheduled
```
- Envoie tous les questionnaires prêts
- Affiche les détails de chaque envoi

## 🚨 Dépannage

### Problème : "Le questionnaire n'apparaît pas"

**Vérifications :**
1. ✅ La date d'envoi est atteinte (`date_envoi <= maintenant`)
2. ✅ Le questionnaire est envoyé (`envoye = true`)
3. ✅ L'apprenant a payé le module
4. ✅ L'apprenant est du bon niveau

**Solution :**
```bash
# Lancer la surveillance pour envoyer automatiquement
php surveillance_questionnaires_automatique.php
```

### Problème : "Le questionnaire reste en retard"

**Solution :**
```bash
# Forcer l'envoi des questionnaires en retard
php artisan content:send-scheduled
```

### Problème : "Erreur dans les logs"

**Vérifications :**
- `storage/logs/laravel.log`
- Configuration email dans `.env`
- Connexion à la base de données

## 📱 Interface Utilisateur

### Pour les Formateurs
- **Création** : `http://127.0.0.1:8000/questionnaires/create`
- **Gestion** : `http://127.0.0.1:8000/questionnaires`
- **Programmation** : Définir date/heure dans le formulaire

### Pour les Apprenants
- **Affichage** : `http://127.0.0.1:8000/questionnaire_test`
- **Réponse** : Cliquer sur "Répondre" pour chaque questionnaire

## ⏰ Configuration Avancée

### Cron Job (Serveur Linux)
```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (vérification toutes les 5 minutes)
*/5 * * * * cd /path/to/your/project && php artisan content:send-scheduled >> storage/logs/cron.log 2>&1
```

### Surveillance Continue (Recommandé)
```bash
# Lancer en arrière-plan
nohup php surveillance_questionnaires_automatique.php > surveillance.log 2>&1 &
```

## 🎉 Résultat Final

Quand tout fonctionne correctement :

1. **Création** : Le formateur crée un questionnaire avec une date d'envoi
2. **Programmation** : Le système programme l'envoi automatique
3. **Envoi** : À l'heure programmée, le questionnaire est envoyé automatiquement
4. **Affichage** : Les apprenants voient le questionnaire sur `/questionnaire_test`
5. **Réponse** : Les apprenants peuvent répondre au questionnaire

## 📋 Logs et Monitoring

### Logs de Surveillance
```bash
# Voir les logs en temps réel
tail -f surveillance.log
```

### Logs Laravel
```bash
# Voir les logs d'erreur
tail -f storage/logs/laravel.log
```

### Monitoring en Temps Réel
```bash
# Voir l'état des questionnaires
php artisan content:send-scheduled
```

## 🔄 Maintenance

### Nettoyage des Questionnaires de Test
```sql
-- Supprimer les questionnaires de test
DELETE FROM questionnaires WHERE titre LIKE 'Test Envoi Automatique%';
```

### Vérification de l'État
```bash
# Vérifier tous les questionnaires
php test_envoi_automatique.php
```

---

## ✅ Checklist de Vérification

- [ ] La surveillance est lancée : `php surveillance_questionnaires_automatique.php`
- [ ] Les emails sont configurés dans `.env`
- [ ] La base de données est accessible
- [ ] Les modules et niveaux existent
- [ ] Les apprenants ont payé leurs modules
- [ ] Les notifications fonctionnent

**🎯 Le système est maintenant prêt pour l'envoi automatique fiable des questionnaires !** 
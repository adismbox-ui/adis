# 🚀 Guide d'Envoi Automatique Amélioré des Documents

## 🎯 Objectif
Système d'envoi automatique des documents qui fonctionne de manière fiable :
- **Date passée** → Envoi immédiat
- **Date future** → Envoi automatique à l'heure programmée
- **En retard** → Envoi automatique même si retardé

## ⚡ Fonctionnement

### 1. Création du Document
Quand vous créez un document sur `http://127.0.0.1:8000/admin/documents/create` :

- **Si la date d'envoi est dans le passé** → Envoi immédiat
- **Si la date d'envoi est dans le futur** → Programmation automatique

### 2. Envoi Automatique
Le système surveille en continu et envoie automatiquement :
- À l'heure exacte programmée
- Même si c'est en retard (jusqu'à plusieurs heures/days)

### 3. Affichage pour les Apprenants
Sur la page des documents :
- Seuls les documents avec `envoye = true` s'affichent
- L'apprenant doit avoir payé le module
- L'apprenant doit être du bon niveau

## 🛠️ Activation du Système

### Option 1 : Surveillance Continue (Recommandée)
```bash
# Lancer la surveillance en continu
php surveillance_documents_automatique.php
```

### Option 2 : Fichier Batch Windows
```bash
# Double-cliquer sur le fichier
surveillance_documents.bat
```

### Option 3 : Commande Artisan
```bash
# Vérification manuelle
php artisan content:send-scheduled
```

## 📊 États des Documents

| État | Description | Affichage |
|------|-------------|-----------|
| **Programmé** | `envoye = false` + `date_envoi > maintenant` | ❌ Pas visible |
| **À l'heure** | `envoye = true` + `date_envoi <= maintenant` | ✅ Visible |
| **En retard** | `envoye = false` + `date_envoi < maintenant` | ❌ Pas visible (sera envoyé automatiquement) |

## 🧪 Test du Système

### Test Rapide
```bash
# Créer un document de test
php test_envoi_documents_automatique.php
```

### Vérification Manuelle
```bash
# Vérifier l'état des documents
php verifier_documents.php
```

## 🔧 Scripts Utiles

### 1. Surveillance Continue
```bash
php surveillance_documents_automatique.php
```
- Vérifie toutes les 30 secondes
- Envoie automatiquement les documents prêts
- Affiche les logs en temps réel

### 2. Test Complet
```bash
php test_envoi_documents_automatique.php
```
- Crée un document de test
- Vérifie l'état actuel
- Identifie les documents en retard

### 3. Vérification Manuelle
```bash
php artisan content:send-scheduled
```
- Envoie tous les documents prêts
- Affiche les détails de chaque envoi

## 🚨 Dépannage

### Problème : "Le document n'apparaît pas"

**Vérifications :**
1. ✅ La date d'envoi est atteinte (`date_envoi <= maintenant`)
2. ✅ Le document est envoyé (`envoye = true`)
3. ✅ L'apprenant a payé le module
4. ✅ L'apprenant est du bon niveau

**Solution :**
```bash
# Lancer la surveillance pour envoyer automatiquement
php surveillance_documents_automatique.php
```

### Problème : "Le document reste en retard"

**Solution :**
```bash
# Forcer l'envoi des documents en retard
php artisan content:send-scheduled
```

### Problème : "Erreur dans les logs"

**Vérifications :**
- `storage/logs/laravel.log`
- Configuration email dans `.env`
- Connexion à la base de données

## 📱 Interface Utilisateur

### Pour les Admins
- **Création** : `http://127.0.0.1:8000/admin/documents/create`
- **Gestion** : `http://127.0.0.1:8000/admin/documents`
- **Programmation** : Définir date/heure dans le formulaire

### Pour les Apprenants
- **Affichage** : Page des documents
- **Téléchargement** : Cliquer sur le document pour le télécharger

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
nohup php surveillance_documents_automatique.php > surveillance_documents.log 2>&1 &
```

## 🎉 Résultat Final

Quand tout fonctionne correctement :

1. **Création** : L'admin crée un document avec une date d'envoi
2. **Programmation** : Le système programme l'envoi automatique
3. **Envoi** : À l'heure programmée, le document est envoyé automatiquement
4. **Affichage** : Les apprenants voient le document
5. **Téléchargement** : Les apprenants peuvent télécharger le document

## 📋 Logs et Monitoring

### Logs de Surveillance
```bash
# Voir les logs en temps réel
tail -f surveillance_documents.log
```

### Logs Laravel
```bash
# Voir les logs d'erreur
tail -f storage/logs/laravel.log
```

### Monitoring en Temps Réel
```bash
# Voir l'état des documents
php artisan content:send-scheduled
```

## 🔄 Maintenance

### Nettoyage des Documents de Test
```sql
-- Supprimer les documents de test
DELETE FROM documents WHERE titre LIKE 'Test Envoi Automatique Document%';
```

### Vérification de l'État
```bash
# Vérifier tous les documents
php test_envoi_documents_automatique.php
```

---

## ✅ Checklist de Vérification

- [ ] La surveillance est lancée : `php surveillance_documents_automatique.php`
- [ ] Les emails sont configurés dans `.env`
- [ ] La base de données est accessible
- [ ] Les modules et niveaux existent
- [ ] Les apprenants ont payé leurs modules
- [ ] Les notifications fonctionnent

**🎯 Le système est maintenant prêt pour l'envoi automatique fiable des documents !** 
# Guide des Nouvelles Colonnes - Date d'Envoi et Status

## 🎯 Objectif
Ajout de deux nouvelles colonnes dans la liste des questionnaires pour afficher la date d'envoi programmée et le status de chaque questionnaire.

## ✅ Nouvelles Colonnes Ajoutées

### **1. Colonne "Date d'envoi"**
- **Position** : 8ème colonne (après "Niveau")
- **Affichage** : Date et heure au format français (dd/mm/yyyy HH:mm)
- **Exemple** : `04/08/2025 15:30`
- **Si non programmé** : Badge gris "Non programmé"

### **2. Colonne "Status"**
- **Position** : 9ème colonne (après "Date d'envoi")
- **Badges colorés** selon l'état :

#### 🟢 **Envoyé** (Vert)
- **Condition** : `envoye = true`
- **Icône** : ✅ Check-circle
- **Signification** : Le questionnaire a été envoyé aux apprenants

#### 🟡 **En retard** (Jaune)
- **Condition** : `date_envoi <= maintenant` ET `envoye = false`
- **Icône** : ⏰ Clock
- **Signification** : La date d'envoi est passée mais le questionnaire n'a pas été envoyé

#### 🔵 **Programmé** (Bleu)
- **Condition** : `date_envoi > maintenant` ET `envoye = false`
- **Icône** : ⏳ Hourglass-half
- **Signification** : Le questionnaire est programmé pour une date future

#### ⚫ **Non défini** (Gris)
- **Condition** : `date_envoi = null`
- **Signification** : Aucune date d'envoi n'a été définie

## 🧪 Test des Nouvelles Colonnes

### **Pour voir les nouvelles colonnes :**
1. **Allez sur** : `http://127.0.0.1:8000/questionnaires`
2. **Vous devriez voir** :
   ```
   # | Titre | Type | Semaine | Temps | Module | Niveau | Date d'envoi | Status | Questions | Actions
   ```

### **Test de la recherche :**
1. **Dans la barre de recherche**, tapez :
   - `04/08` → Trouve les questionnaires du 4 août
   - `envoyé` → Trouve les questionnaires envoyés
   - `programmé` → Trouve les questionnaires programmés
   - `retard` → Trouve les questionnaires en retard

### **Exemples d'affichage :**
```
ID | Titre                    | Date d'envoi    | Status
1  | Questionnaire Test       | 04/08/2025 15:30| 🟢 Envoyé
2  | Quiz Hebdomadaire        | 05/08/2025 14:00| 🔵 Programmé
3  | Évaluation Finale        | 03/08/2025 10:00| 🟡 En retard
4  | Test Module 1            | Non programmé   | ⚫ Non défini
```

## 🔧 Fonctionnalités Techniques

### **Formatage des dates :**
```php
{{ \Carbon\Carbon::parse($questionnaire->date_envoi)->format('d/m/Y H:i') }}
```

### **Logique des status :**
```php
@if($questionnaire->envoye)
    <span class="badge bg-success">Envoyé</span>
@elseif($questionnaire->date_envoi && $questionnaire->date_envoi <= \Carbon\Carbon::now())
    <span class="badge bg-warning">En retard</span>
@elseif($questionnaire->date_envoi)
    <span class="badge bg-info">Programmé</span>
@else
    <span class="badge bg-secondary">Non défini</span>
@endif
```

### **Recherche étendue :**
```javascript
const dateEnvoi = row.cells[7].textContent.toLowerCase();
const status = row.cells[8].textContent.toLowerCase();
row.style.display = (titre.includes(filter) || type.includes(filter) || 
                     module.includes(filter) || dateEnvoi.includes(filter) || 
                     status.includes(filter)) ? '' : 'none';
```

## 📊 Statistiques Disponibles

### **Types de status :**
- **Envoyés** : Questionnaires déjà envoyés aux apprenants
- **En retard** : Questionnaires dont la date d'envoi est passée mais non envoyés
- **Programmés** : Questionnaires programmés pour une date future
- **Non programmés** : Questionnaires sans date d'envoi définie

### **Avantages :**
- ✅ **Visibilité** : Voir d'un coup d'œil l'état de tous les questionnaires
- ✅ **Gestion** : Identifier rapidement les questionnaires en retard
- ✅ **Planification** : Voir les questionnaires programmés
- ✅ **Recherche** : Filtrer par date ou status

## 🎨 Styles des Badges

### **Badge "Envoyé" :**
```html
<span class="badge bg-success text-white">
    <i class="fas fa-check-circle me-1"></i> Envoyé
</span>
```

### **Badge "En retard" :**
```html
<span class="badge bg-warning text-dark">
    <i class="fas fa-clock me-1"></i> En retard
</span>
```

### **Badge "Programmé" :**
```html
<span class="badge bg-info text-white">
    <i class="fas fa-hourglass-half me-1"></i> Programmé
</span>
```

### **Badge "Non défini" :**
```html
<span class="badge bg-secondary">Non défini</span>
```

## 🔄 Intégration avec le Système

### **Avec le bouton OK :**
1. **L'utilisateur** confirme la date avec le bouton OK
2. **La colonne "Date d'envoi"** affiche la date confirmée
3. **La colonne "Status"** affiche "Programmé"
4. **À l'heure programmée**, le status devient "Envoyé"

### **Avec la surveillance automatique :**
1. **Le système** vérifie toutes les minutes
2. **Les questionnaires en retard** sont automatiquement marqués "Envoyé"
3. **La colonne "Status"** se met à jour automatiquement

## 📋 Checklist de Vérification

- [ ] Les colonnes "Date d'envoi" et "Status" apparaissent dans le tableau
- [ ] Les dates sont formatées correctement (dd/mm/yyyy HH:mm)
- [ ] Les badges colorés s'affichent selon le status
- [ ] La recherche fonctionne avec les nouvelles colonnes
- [ ] Les questionnaires en retard sont détectés
- [ ] Les questionnaires programmés sont visibles

## 🆘 Dépannage

### **Problème : "Les colonnes n'apparaissent pas"**
**Solution** : Vérifiez que la page est bien rechargée

### **Problème : "Les dates ne s'affichent pas"**
**Solution** : Vérifiez que les questionnaires ont une `date_envoi` définie

### **Problème : "Les status sont incorrects"**
**Solution** : Lancez `php test_colonnes_date.php` pour corriger les questionnaires en retard

### **Problème : "La recherche ne fonctionne pas"**
**Solution** : Vérifiez que JavaScript est activé dans le navigateur

## 📊 Monitoring

### **Pour vérifier l'état des questionnaires :**
1. **Lancez** : `php test_colonnes_date.php`
2. **Vérifiez** les statistiques des status
3. **Corrigez** automatiquement les questionnaires en retard

### **Pour tester la page :**
1. **Allez sur** : `http://127.0.0.1:8000/questionnaires`
2. **Observez** les nouvelles colonnes
3. **Testez** la recherche avec différents termes

---

**Les nouvelles colonnes sont maintenant fonctionnelles ! Testez-les sur la page des questionnaires.** 🚀 
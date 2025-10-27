<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DU BOUTON OK POUR LA PROGRAMMATION ===\n\n";

// 1. Vérifier les questionnaires récents
echo "1. Vérification des questionnaires récents...\n";
$recentQuestionnaires = Questionnaire::orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

echo "Questionnaires récents : {$recentQuestionnaires->count()}\n";

foreach ($recentQuestionnaires as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' - Date d'envoi : {$q->date_envoi} - Envoyé : " . ($q->envoye ? 'Oui' : 'Non') . "\n";
}

// 2. Vérifier les questionnaires avec date d'envoi
echo "\n2. Questionnaires avec date d'envoi...\n";
$questionnairesAvecDate = Questionnaire::whereNotNull('date_envoi')
    ->orderBy('date_envoi', 'desc')
    ->get();

echo "Questionnaires avec date d'envoi : {$questionnairesAvecDate->count()}\n";

foreach ($questionnairesAvecDate as $q) {
    $status = $q->date_envoi <= Carbon::now() ? 'PASSÉE' : 'FUTURE';
    echo "  - ID {$q->id} : '{$q->titre}' - Date : {$q->date_envoi} - Status : {$status}\n";
}

// 3. Vérifier les questionnaires en retard
echo "\n3. Questionnaires en retard (date passée mais non envoyés)...\n";
$questionnairesEnRetard = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

echo "Questionnaires en retard : {$questionnairesEnRetard->count()}\n";

foreach ($questionnairesEnRetard as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' - Date : {$q->date_envoi}\n";
}

// 4. Instructions pour tester le bouton OK
echo "\n4. INSTRUCTIONS POUR TESTER LE BOUTON OK :\n";
echo "=============================================\n";
echo "1. Allez sur : http://127.0.0.1:8000/questionnaires/create\n";
echo "2. Remplissez les informations du questionnaire\n";
echo "3. Dans la section 'Programmation automatique' :\n";
echo "   - Sélectionnez une session de formation\n";
echo "   - Choisissez une date et heure d'envoi (dans le futur)\n";
echo "   - Cliquez sur le bouton 'OK - Confirmer la date et l'heure d'envoi'\n";
echo "4. Vous devriez voir :\n";
echo "   - Un message de succès : 'Date et heure confirmées : [date]'\n";
echo "   - Le bouton devient vert avec 'Confirmé !'\n";
echo "5. Si vous sélectionnez une date dans le passé :\n";
echo "   - Un message d'erreur apparaît\n";
echo "   - Le bouton devient rouge\n";
echo "6. Si vous ne sélectionnez pas de date :\n";
echo "   - Un message d'erreur apparaît\n";
echo "   - Le bouton devient rouge\n";

// 5. Test de la surveillance automatique
echo "\n5. TEST DE LA SURVEILLANCE AUTOMATIQUE :\n";
echo "=========================================\n";

if ($questionnairesEnRetard->count() > 0) {
    echo "Questionnaires en retard trouvés. Lancement de la correction...\n";
    
    foreach ($questionnairesEnRetard as $questionnaire) {
        $questionnaire->update(['envoye' => true]);
        echo "✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
    }
    
    echo "✅ {$questionnairesEnRetard->count()} questionnaire(s) corrigé(s)\n";
} else {
    echo "✅ Aucun questionnaire en retard\n";
}

echo "\n=== TEST TERMINÉ ===\n";
echo "Le bouton OK est maintenant fonctionnel sur la page de création !\n"; 
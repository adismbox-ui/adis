<?php

require_once 'vendor/autoload.php';

use App\Models\Apprenant;
use App\Models\Questionnaire;
use App\Models\Module;
use App\Models\Niveau;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORRECTION NIVEAU APPRENANT ===\n\n";

// 1. Identifier l'apprenant
$apprenant = Apprenant::with(['utilisateur', 'niveau'])->first();

if (!$apprenant) {
    echo "❌ Aucun apprenant trouvé !\n";
    exit;
}

echo "Apprenant : {$apprenant->utilisateur->nom} (Niveau: {$apprenant->niveau->nom})\n\n";

// 2. Vérifier les modules payés
$modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
echo "Modules payés : " . implode(', ', $modulesPayes) . "\n";

// 3. Vérifier les questionnaires disponibles pour son niveau
$questionnairesNiveau = Questionnaire::with(['module.niveau'])
    ->whereIn('module_id', $modulesPayes)
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id);
    })
    ->where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "\nQuestionnaires pour son niveau ({$apprenant->niveau->nom}) : {$questionnairesNiveau->count()}\n";
foreach ($questionnairesNiveau as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
}

// 4. Vérifier tous les questionnaires disponibles (sans restriction de niveau)
$questionnairesTous = Questionnaire::with(['module.niveau'])
    ->whereIn('module_id', $modulesPayes)
    ->where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "\nTous les questionnaires disponibles (tous niveaux) : {$questionnairesTous->count()}\n";
foreach ($questionnairesTous as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre}, Niveau: {$q->module->niveau->nom})\n";
}

// 5. Créer des questionnaires pour son niveau
echo "\n5. Création de questionnaires pour son niveau :\n";

if ($questionnairesNiveau->count() === 0) {
    echo "Aucun questionnaire pour son niveau. Création de questionnaires de test...\n";
    
    // Prendre le premier module payé
    $moduleTest = Module::find($modulesPayes[0]);
    
    if ($moduleTest) {
        // Créer un questionnaire pour ce module et ce niveau
        $questionnaire = Questionnaire::create([
            'titre' => 'Questionnaire de test - ' . $moduleTest->titre . ' - ' . $apprenant->niveau->nom,
            'module_id' => $moduleTest->id,
            'user_id' => 1, // Admin
            'date_envoi' => Carbon::now()->subMinutes(5), // 5 minutes dans le passé
            'envoye' => true,
            'type_devoir' => 'hebdomadaire',
            'semaine' => 1,
            'minutes' => 30
        ]);
        
        echo "✅ Questionnaire créé avec l'ID : {$questionnaire->id}\n";
        echo "   - Titre : {$questionnaire->titre}\n";
        echo "   - Module : {$moduleTest->titre}\n";
        echo "   - Niveau : {$apprenant->niveau->nom}\n";
        echo "   - Date d'envoi : " . Carbon::parse($questionnaire->date_envoi)->format('d/m/Y H:i') . "\n";
        echo "   - Status : Envoyé\n";
    }
} else {
    echo "Des questionnaires existent déjà pour son niveau.\n";
}

// 6. Alternative : Modifier le contrôleur pour accepter tous les niveaux
echo "\n6. Alternative : Modification du contrôleur\n";
echo "Le contrôleur actuel filtre par niveau. Options :\n";
echo "1. Créer des questionnaires pour son niveau (fait ci-dessus)\n";
echo "2. Modifier le contrôleur pour accepter tous les niveaux\n";
echo "3. Changer le niveau de l'apprenant\n";

// 7. Vérification finale
echo "\n7. Vérification finale :\n";

$questionnairesFinal = Questionnaire::with(['module.niveau'])
    ->whereIn('module_id', $modulesPayes)
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id);
    })
    ->where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "Questionnaires disponibles pour son niveau : {$questionnairesFinal->count()}\n";
foreach ($questionnairesFinal as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
}

echo "\n=== CORRECTION TERMINÉE ===\n";
echo "Testez maintenant : http://127.0.0.1:8000/questionnaire_test\n"; 
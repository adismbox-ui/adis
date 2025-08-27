<?php

require_once 'vendor/autoload.php';

use App\Models\Questionnaire;
use App\Models\Module;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ENVOI IMMÉDIAT ===\n\n";

// 1. Créer un questionnaire avec une date d'envoi dans le passé
echo "1. Création d'un questionnaire de test avec date passée :\n";

$module = Module::first();
if (!$module) {
    echo "❌ Aucun module trouvé !\n";
    exit;
}

// Créer un questionnaire avec une date d'envoi 2 minutes dans le passé
$dateEnvoi = Carbon::now()->subMinutes(2);

$questionnaire = Questionnaire::create([
    'titre' => 'Test Envoi Immédiat - ' . Carbon::now()->format('H:i:s'),
    'module_id' => $module->id,
    'user_id' => 1, // Admin
    'date_envoi' => $dateEnvoi,
    'envoye' => false, // Pas encore envoyé
    'type_devoir' => 'hebdomadaire',
    'semaine' => 1,
    'minutes' => 30
]);

echo "✅ Questionnaire créé :\n";
echo "  - ID : {$questionnaire->id}\n";
echo "  - Titre : {$questionnaire->titre}\n";
echo "  - Date d'envoi : " . $dateEnvoi->format('d/m/Y H:i:s') . "\n";
echo "  - Statut : Non envoyé\n";

// 2. Vérifier l'état avant envoi
echo "\n2. État avant envoi automatique :\n";
$questionnaireAvant = Questionnaire::find($questionnaire->id);
echo "  - Envoyé : " . ($questionnaireAvant->envoye ? 'Oui' : 'Non') . "\n";
echo "  - Date passée : " . ($dateEnvoi->isPast() ? 'Oui' : 'Non') . "\n";

// 3. Simuler l'envoi automatique
echo "\n3. Simulation de l'envoi automatique :\n";

$questionnairesEnRetard = Questionnaire::where('envoye', false)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "  Questionnaires en retard : {$questionnairesEnRetard->count()}\n";

foreach ($questionnairesEnRetard as $q) {
    $q->update(['envoye' => true]);
    $dateEnvoiQ = Carbon::parse($q->date_envoi);
    echo "  ✅ ID {$q->id} : '{$q->titre}' envoyé (retard de " . $dateEnvoiQ->diffForHumans() . ")\n";
}

// 4. Vérifier l'état après envoi
echo "\n4. État après envoi automatique :\n";
$questionnaireApres = Questionnaire::find($questionnaire->id);
echo "  - Envoyé : " . ($questionnaireApres->envoye ? 'Oui' : 'Non') . "\n";

// 5. Vérifier s'il apparaît sur la page de test
echo "\n5. Vérification pour la page de test :\n";

$apprenant = \App\Models\Apprenant::with(['utilisateur', 'niveau'])->first();
if ($apprenant) {
    $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    
    $questionnairesDisponibles = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $modulesPayes)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "  Apprenant : {$apprenant->utilisateur->nom}\n";
    echo "  Modules payés : " . implode(', ', $modulesPayes) . "\n";
    echo "  Questionnaires disponibles : {$questionnairesDisponibles->count()}\n";
    
    // Vérifier si notre questionnaire de test est disponible
    $questionnaireTestDisponible = $questionnairesDisponibles->where('id', $questionnaire->id)->first();
    if ($questionnaireTestDisponible) {
        echo "  ✅ Le questionnaire de test est disponible sur la page de test !\n";
    } else {
        echo "  ❌ Le questionnaire de test n'est pas disponible (vérifiez le niveau/module)\n";
    }
}

echo "\n=== TEST TERMINÉ ===\n";
echo "Vérifiez maintenant : http://127.0.0.1:8000/questionnaire_test\n"; 
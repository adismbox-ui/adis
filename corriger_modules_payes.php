<?php

require_once 'vendor/autoload.php';

use App\Models\Apprenant;
use App\Models\Inscription;
use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORRECTION DES MODULES PAYÉS ===\n\n";

// 1. Identifier l'apprenant
$apprenant = Apprenant::with(['utilisateur', 'niveau'])->first();

if (!$apprenant) {
    echo "❌ Aucun apprenant trouvé !\n";
    exit;
}

echo "Apprenant : {$apprenant->utilisateur->nom} (Niveau: {$apprenant->niveau->nom})\n\n";

// 2. Vérifier les inscriptions actuelles
echo "1. Inscriptions actuelles :\n";
$inscriptions = Inscription::where('apprenant_id', $apprenant->id)->get();

foreach ($inscriptions as $inscription) {
    $status = $inscription->statut === 'valide' ? '✅ Valide' : '❌ Non valide';
    echo "  - Module ID {$inscription->module_id} : {$status}\n";
}

// 3. Marquer les modules comme valides
echo "\n2. Marquage des modules comme valides :\n";

$modulesCorriges = 0;
foreach ($inscriptions as $inscription) {
    if ($inscription->statut !== 'valide') {
        $inscription->update(['statut' => 'valide']);
        echo "✅ Module ID {$inscription->module_id} marqué comme valide\n";
        $modulesCorriges++;
    }
}

if ($modulesCorriges === 0) {
    echo "ℹ️  Tous les modules sont déjà payés\n";
}

// 4. Vérifier les questionnaires disponibles après correction
echo "\n3. Vérification des questionnaires disponibles :\n";

$modulesPayes = Inscription::where('apprenant_id', $apprenant->id)
    ->where('statut', 'valide')
    ->with(['module.niveau'])
    ->get();

echo "Modules payés après correction :\n";
foreach ($modulesPayes as $inscription) {
    echo "  - {$inscription->module->titre} (Niveau: {$inscription->module->niveau->nom})\n";
}

$modulesApprenant = $modulesPayes->pluck('module_id')->unique();

// Questionnaires disponibles pour cet apprenant
$questionnairesDisponibles = Questionnaire::where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->whereIn('module_id', $modulesApprenant)
    ->with(['module.niveau'])
    ->get();

echo "\nQuestionnaires disponibles pour cet apprenant :\n";
if ($questionnairesDisponibles->count() > 0) {
    foreach ($questionnairesDisponibles as $q) {
        echo "  - ID {$q->id} : '{$q->titre}'\n";
        echo "    Module: {$q->module->titre}\n";
        echo "    Niveau: {$q->module->niveau->nom}\n";
        echo "    Questions: {$q->questions->count()}\n";
        echo "    ---\n";
    }
} else {
    echo "  ❌ Aucun questionnaire disponible !\n";
    
    // Créer un questionnaire de test pour le niveau de l'apprenant
    echo "\n4. Création d'un questionnaire de test :\n";
    
    $moduleTest = $modulesPayes->first()->module;
    
    $questionnaire = Questionnaire::create([
        'titre' => 'Questionnaire de test - ' . $moduleTest->titre,
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
    echo "   - Date d'envoi : " . Carbon::parse($questionnaire->date_envoi)->format('d/m/Y H:i') . "\n";
    echo "   - Status : Envoyé\n";
}

// 5. Instructions pour tester
echo "\n5. INSTRUCTIONS POUR TESTER :\n";
echo "===============================\n";
echo "1. Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
echo "2. Connectez-vous avec l'apprenant : {$apprenant->utilisateur->nom}\n";
echo "3. Vous devriez maintenant voir les questionnaires disponibles\n";
echo "4. Si aucun questionnaire n'apparaît, vérifiez que :\n";
echo "   - L'apprenant est bien connecté\n";
echo "   - Les modules sont marqués comme valides\n";
echo "   - Les questionnaires sont envoyés et à la bonne date\n";

echo "\n=== CORRECTION TERMINÉE ===\n";
echo "Les modules sont maintenant marqués comme valides !\n"; 
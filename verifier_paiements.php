<?php

require_once 'vendor/autoload.php';

use App\Models\Apprenant;
use App\Models\Paiement;
use App\Models\Inscription;
use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VÉRIFICATION PAIEMENTS ET INSCRIPTIONS ===\n\n";

// 1. Identifier l'apprenant
$apprenant = Apprenant::with(['utilisateur', 'niveau'])->first();

if (!$apprenant) {
    echo "❌ Aucun apprenant trouvé !\n";
    exit;
}

echo "Apprenant : {$apprenant->utilisateur->nom} (Niveau: {$apprenant->niveau->nom})\n\n";

// 2. Vérifier les paiements
echo "1. Paiements de l'apprenant :\n";
$paiements = Paiement::where('apprenant_id', $apprenant->id)->get();

if ($paiements->count() > 0) {
    foreach ($paiements as $paiement) {
        $status = $paiement->statut === 'valide' ? '✅ Valide' : '❌ Non valide';
        echo "  - Module ID {$paiement->module_id} : {$status} (Montant: {$paiement->montant})\n";
    }
} else {
    echo "  ❌ Aucun paiement trouvé !\n";
}

// 3. Vérifier les inscriptions
echo "\n2. Inscriptions de l'apprenant :\n";
$inscriptions = Inscription::where('apprenant_id', $apprenant->id)->get();

foreach ($inscriptions as $inscription) {
    $status = $inscription->statut === 'valide' ? '✅ Valide' : '❌ Non valide';
    echo "  - Module ID {$inscription->module_id} : {$status}\n";
}

// 4. Simuler la logique du contrôleur
echo "\n3. Simulation de la logique du contrôleur :\n";

// Logique actuelle du contrôleur (utilise paiements)
$modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
echo "Modules payés (via paiements) : " . implode(', ', $modulesPayes) . "\n";

// Logique alternative (utilise inscriptions)
$modulesInscrits = $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray();
echo "Modules inscrits (via inscriptions) : " . implode(', ', $modulesInscrits) . "\n";

// 5. Vérifier les questionnaires disponibles avec chaque logique
echo "\n4. Questionnaires disponibles :\n";

// Avec la logique actuelle (paiements)
if (!empty($modulesPayes)) {
    $questionnairesPaiements = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $modulesPayes)
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "Avec paiements : {$questionnairesPaiements->count()} questionnaire(s)\n";
    foreach ($questionnairesPaiements as $q) {
        echo "  - ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
    }
} else {
    echo "Avec paiements : 0 questionnaire (aucun module payé)\n";
}

// Avec la logique alternative (inscriptions)
if (!empty($modulesInscrits)) {
    $questionnairesInscriptions = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $modulesInscrits)
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "\nAvec inscriptions : {$questionnairesInscriptions->count()} questionnaire(s)\n";
    foreach ($questionnairesInscriptions as $q) {
        echo "  - ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
    }
} else {
    echo "\nAvec inscriptions : 0 questionnaire (aucun module inscrit)\n";
}

// 6. Créer des paiements de test si nécessaire
echo "\n5. Création de paiements de test :\n";

if ($paiements->count() === 0) {
    echo "Aucun paiement trouvé. Création de paiements de test...\n";
    
    $inscriptionsValides = Inscription::where('apprenant_id', $apprenant->id)
        ->where('statut', 'valide')
        ->get();
    
    foreach ($inscriptionsValides as $inscription) {
        // Vérifier si un paiement existe déjà pour ce module
        $paiementExistant = Paiement::where('apprenant_id', $apprenant->id)
            ->where('module_id', $inscription->module_id)
            ->first();
        
        if (!$paiementExistant) {
            $paiement = Paiement::create([
                'apprenant_id' => $apprenant->id,
                'module_id' => $inscription->module_id,
                'montant' => 100.00,
                'date_paiement' => Carbon::now(),
                'statut' => 'valide',
                'methode' => 'test',
                'reference' => 'TEST-' . time(),
                'notes' => 'Paiement de test créé automatiquement'
            ]);
            
            echo "✅ Paiement créé pour le module ID {$inscription->module_id}\n";
        } else {
            echo "ℹ️  Paiement existant pour le module ID {$inscription->module_id}\n";
        }
    }
} else {
    echo "Des paiements existent déjà.\n";
}

// 7. Vérification finale
echo "\n6. Vérification finale après correction :\n";

$modulesPayesFinal = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
echo "Modules payés : " . implode(', ', $modulesPayesFinal) . "\n";

$questionnairesFinal = Questionnaire::with(['module.niveau'])
    ->whereIn('module_id', $modulesPayesFinal)
    ->where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "Questionnaires disponibles : {$questionnairesFinal->count()}\n";
foreach ($questionnairesFinal as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
}

echo "\n=== VÉRIFICATION TERMINÉE ===\n";
echo "Testez maintenant : http://127.0.0.1:8000/questionnaire_test\n"; 
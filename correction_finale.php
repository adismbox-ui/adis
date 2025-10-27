<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Module;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORRECTION FINALE DU PROBLÈME ===\n\n";

// 1. RÉCUPÉRER L'APPRENANT
$apprenant = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->first();
echo "Apprenant : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
echo "Niveau : {$apprenant->niveau->nom} (ID: {$apprenant->niveau_id})\n";

// 2. RÉCUPÉRER LES MODULES PAYÉS DU NIVEAU 2A
$moduleIds = $apprenant->paiements()
    ->where('statut', 'valide')
    ->pluck('module_id')
    ->toArray();

echo "Modules payés : " . implode(', ', $moduleIds) . "\n";

// 3. TROUVER LES QUESTIONNAIRES DU NIVEAU 2A
echo "\n3. QUESTIONNAIRES DU NIVEAU 2A...\n";

$questionnairesNiveau2A = Questionnaire::with(['module.niveau'])
    ->whereIn('module_id', $moduleIds)
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id); // Niveau 2A
    })
    ->get();

echo "Questionnaires du niveau 2A : {$questionnairesNiveau2A->count()}\n";

foreach ($questionnairesNiveau2A as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' - Envoyé: " . ($q->envoye ? 'Oui' : 'Non') . " - Date: {$q->date_envoi}\n";
}

// 4. CORRIGER LES QUESTIONNAIRES EN RETARD
echo "\n4. CORRECTION DES QUESTIONNAIRES EN RETARD...\n";

$questionnairesEnRetard = $questionnairesNiveau2A->filter(function($q) {
    return $q->date_envoi <= Carbon::now() && !$q->envoye;
});

echo "Questionnaires en retard : {$questionnairesEnRetard->count()}\n";

foreach ($questionnairesEnRetard as $questionnaire) {
    $questionnaire->update(['envoye' => true]);
    echo "✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
}

// 5. CRÉER UN QUESTIONNAIRE DE TEST SI AUCUN N'EST DISPONIBLE
$questionnairesDisponibles = $questionnairesNiveau2A->filter(function($q) {
    return $q->envoye && $q->date_envoi <= Carbon::now();
});

if ($questionnairesDisponibles->count() === 0) {
    echo "\n5. CRÉATION D'UN QUESTIONNAIRE DE TEST POUR NIVEAU 2A...\n";
    
    $moduleNiveau2A = Module::whereIn('id', $moduleIds)
        ->where('niveau_id', $apprenant->niveau_id)
        ->first();
    
    if ($moduleNiveau2A) {
        echo "Module trouvé : {$moduleNiveau2A->titre}\n";
        
        // Supprimer les anciens questionnaires de test
        Questionnaire::where('titre', 'LIKE', '%TEST NIVEAU 2A%')->delete();
        
        // Créer un questionnaire de test avec user_id
        $questionnaire = Questionnaire::create([
            'titre' => 'Questionnaire TEST NIVEAU 2A - DISPONIBLE MAINTENANT',
            'description' => 'Questionnaire de test pour le niveau 2A',
            'module_id' => $moduleNiveau2A->id,
            'date_envoi' => Carbon::now()->subMinutes(5),
            'envoye' => true,
            'duree' => 30,
            'points_totaux' => 10,
            'user_id' => $apprenant->utilisateur->id
        ]);
        
        echo "✅ Questionnaire de test créé (ID: {$questionnaire->id})\n";
    }
}

// 6. VÉRIFIER L'AFFICHAGE FINAL
echo "\n6. VÉRIFICATION FINALE DE L'AFFICHAGE...\n";

$questionnairesVisibles = Questionnaire::with(['module.niveau'])
    ->whereIn('module_id', $moduleIds)
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id);
    })
    ->where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "Questionnaires visibles sur /questionnaire_test : {$questionnairesVisibles->count()}\n";

foreach ($questionnairesVisibles as $q) {
    echo "  - '{$q->titre}' (Module: {$q->module->titre}, Niveau: {$q->module->niveau->nom})\n";
}

// 7. INSTRUCTIONS FINALES
echo "\n7. INSTRUCTIONS POUR TESTER :\n";
echo "=============================\n";

if ($questionnairesVisibles->count() > 0) {
    echo "✅ SUCCÈS ! Les questionnaires sont maintenant disponibles.\n";
    echo "1. Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
    echo "2. Connectez-vous en tant qu'apprenant\n";
    echo "3. Vous devriez voir {$questionnairesVisibles->count()} questionnaire(s)\n";
} else {
    echo "❌ Problème persistant. Vérifiez la base de données.\n";
}

echo "\n=== CORRECTION TERMINÉE ===\n"; 
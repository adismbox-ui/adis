<?php

require_once 'vendor/autoload.php';

use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DE LA SURVEILLANCE ===\n";
echo "Date et heure : " . date('Y-m-d H:i:s') . "\n\n";

// 1. Vérifier les questionnaires en retard
$questionnairesEnRetard = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

echo "Questionnaires en retard : {$questionnairesEnRetard->count()}\n";

if ($questionnairesEnRetard->count() > 0) {
    foreach ($questionnairesEnRetard as $questionnaire) {
        $questionnaire->update(['envoye' => true]);
        echo "✅ Questionnaire ID {$questionnaire->id} ('{$questionnaire->titre}') marqué comme envoyé\n";
    }
    echo "✅ {$questionnairesEnRetard->count()} questionnaire(s) corrigé(s)\n";
} else {
    echo "✅ Aucun questionnaire en retard\n";
}

// 2. Vérifier l'état général
$totalQuestionnaires = Questionnaire::count();
$questionnairesEnvoyes = Questionnaire::where('envoye', true)->count();
$questionnairesNonEnvoyes = Questionnaire::where('envoye', false)->count();

echo "\nÉtat général :\n";
echo "- Total questionnaires : {$totalQuestionnaires}\n";
echo "- Questionnaires envoyés : {$questionnairesEnvoyes}\n";
echo "- Questionnaires non envoyés : {$questionnairesNonEnvoyes}\n";

// 3. Vérifier les questionnaires disponibles pour les apprenants
$apprenants = \App\Models\Apprenant::with(['utilisateur', 'paiements', 'niveau'])->get();

if ($apprenants->count() > 0) {
    $apprenant = $apprenants->first();
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    
    $questionnairesVisibles = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "\nQuestionnaires visibles pour l'apprenant : {$questionnairesVisibles->count()}\n";
    
    foreach ($questionnairesVisibles as $q) {
        echo "  - '{$q->titre}' (Module: {$q->module->titre})\n";
    }
}

echo "\n=== TEST TERMINÉ ===\n"; 
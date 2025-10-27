<?php

require_once "vendor/autoload.php";

use App\Models\Questionnaire;
use App\Models\Apprenant;
use Carbon\Carbon;

$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== TEST IMMÉDIAT DE L'AFFICHAGE ===\n\n";

// Forcer l'envoi
$questionnairesEnRetard = Questionnaire::where("date_envoi", "<=", Carbon::now())
    ->where("envoye", false)
    ->get();

foreach ($questionnairesEnRetard as $questionnaire) {
    $questionnaire->update(["envoye" => true]);
    echo "✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
}

// Vérifier l'affichage
$apprenant = Apprenant::with(["utilisateur", "paiements", "niveau"])->first();
if ($apprenant) {
    $moduleIds = $apprenant->paiements()->where("statut", "valide")->pluck("module_id")->toArray();
    
    $questionnairesVisibles = Questionnaire::with(["module.niveau"])
        ->whereIn("module_id", $moduleIds)
        ->whereHas("module", function($q) use ($apprenant) {
            $q->where("niveau_id", $apprenant->niveau_id);
        })
        ->where("envoye", true)
        ->where("date_envoi", "<=", Carbon::now())
        ->get();
    
    echo "\nQuestionnaires visibles sur /questionnaire_test : {$questionnairesVisibles->count()}\n";
    
    foreach ($questionnairesVisibles as $q) {
        echo "  - {$q->titre} (Module: {$q->module->titre})\n";
    }
    
    if ($questionnairesVisibles->count() > 0) {
        echo "\n✅ SUCCÈS ! Les questionnaires s'affichent correctement.\n";
        echo "Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
    } else {
        echo "\n❌ Aucun questionnaire visible. Vérifiez les paiements et niveaux.\n";
    }
}

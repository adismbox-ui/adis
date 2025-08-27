<?php

require_once 'vendor/autoload.php';

use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Correction automatique des questionnaires en retard
$questionnairesEnRetard = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

if ($questionnairesEnRetard->count() > 0) {
    foreach ($questionnairesEnRetard as $questionnaire) {
        $questionnaire->update(['envoye' => true]);
        echo "✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
    }
    echo "Correction terminée : {$questionnairesEnRetard->count()} questionnaire(s) corrigé(s)\n";
} else {
    echo "✅ Aucun questionnaire en retard à corriger\n";
} 
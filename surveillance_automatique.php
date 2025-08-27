<?php

require_once "vendor/autoload.php";

use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "[" . date("Y-m-d H:i:s") . "] Début de la surveillance...\n";

// Correction automatique
$questionnairesEnRetard = Questionnaire::where("date_envoi", "<=", Carbon::now())
    ->where("envoye", false)
    ->get();

if ($questionnairesEnRetard->count() > 0) {
    foreach ($questionnairesEnRetard as $questionnaire) {
        $questionnaire->update(["envoye" => true]);
        echo "[" . date("Y-m-d H:i:s") . "] ✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
    }
    echo "[" . date("Y-m-d H:i:s") . "] {$questionnairesEnRetard->count()} questionnaire(s) corrigé(s)\n";
} else {
    echo "[" . date("Y-m-d H:i:s") . "] ✅ Aucun questionnaire en retard\n";
}

echo "[" . date("Y-m-d H:i:s") . "] Surveillance terminée\n";

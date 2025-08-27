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

echo "=== SOLUTION DÉFINITIVE POUR LES QUESTIONNAIRES AUTOMATIQUES ===\n\n";

// 1. FORCER L'ENVOI DE TOUS LES QUESTIONNAIRES EN RETARD
echo "1. FORÇAGE DE L'ENVOI DE TOUS LES QUESTIONNAIRES...\n";

$questionnairesEnRetard = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

echo "Questionnaires en retard trouvés : {$questionnairesEnRetard->count()}\n";

foreach ($questionnairesEnRetard as $questionnaire) {
    $questionnaire->update(['envoye' => true]);
    echo "✅ Questionnaire ID {$questionnaire->id} ('{$questionnaire->titre}') marqué comme envoyé\n";
}

// 2. VÉRIFIER LES QUESTIONNAIRES EXISTANTS
echo "\n2. VÉRIFICATION DES QUESTIONNAIRES EXISTANTS...\n";

$questionnairesEnvoyes = Questionnaire::where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "Questionnaires envoyés et disponibles : {$questionnairesEnvoyes->count()}\n";

foreach ($questionnairesEnvoyes as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
}

// 3. VÉRIFIER L'AFFICHAGE POUR LES APPRENANTS
echo "\n3. VÉRIFICATION DE L'AFFICHAGE POUR LES APPRENANTS...\n";

$apprenants = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->get();

if ($apprenants->count() > 0) {
    $apprenant = $apprenants->first();
    echo "Apprenant test : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    echo "Niveau : {$apprenant->niveau->nom}\n";
    
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "Modules payés : " . implode(', ', $moduleIds) . "\n";
    
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
}

// 4. CRÉER LE SYSTÈME DE SURVEILLANCE AUTOMATIQUE
echo "\n4. CRÉATION DU SYSTÈME DE SURVEILLANCE AUTOMATIQUE...\n";

$surveillanceScript = '<?php

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
';

file_put_contents('surveillance_automatique.php', $surveillanceScript);
echo "✅ Script de surveillance créé : surveillance_automatique.php\n";

// 5. CRÉER LE FICHIER BATCH POUR WINDOWS
$batchScript = '@echo off
title Surveillance Automatique des Questionnaires
echo ========================================
echo SURVEILLANCE AUTOMATIQUE DES QUESTIONNAIRES
echo ========================================
echo.
echo Le système vérifie toutes les minutes si des questionnaires
echo doivent être envoyés automatiquement.
echo.
echo Pour arrêter : Fermez cette fenêtre
echo.
cd /d "' . getcwd() . '"
:loop
php surveillance_automatique.php
echo.
echo Attente de 60 secondes...
timeout /t 60 /nobreak > nul
echo.
goto loop
';

file_put_contents('surveillance_automatique.bat', $batchScript);
echo "✅ Script batch créé : surveillance_automatique.bat\n";

// 6. CRÉER UN SCRIPT DE TEST IMMÉDIAT
$testScript = '<?php

require_once "vendor/autoload.php";

use App\Models\Questionnaire;
use App\Models\Apprenant;
use Carbon\Carbon;

$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== TEST IMMÉDIAT DE L\'AFFICHAGE ===\n\n";

// Forcer l\'envoi
$questionnairesEnRetard = Questionnaire::where("date_envoi", "<=", Carbon::now())
    ->where("envoye", false)
    ->get();

foreach ($questionnairesEnRetard as $questionnaire) {
    $questionnaire->update(["envoye" => true]);
    echo "✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
}

// Vérifier l\'affichage
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
        echo "\n✅ SUCCÈS ! Les questionnaires s\'affichent correctement.\n";
        echo "Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
    } else {
        echo "\n❌ Aucun questionnaire visible. Vérifiez les paiements et niveaux.\n";
    }
}
';

file_put_contents('test_immediat.php', $testScript);
echo "✅ Script de test immédiat créé : test_immediat.php\n";

// 7. INSTRUCTIONS FINALES
echo "\n5. INSTRUCTIONS POUR L'AUTOMATISATION DÉFINITIVE :\n";
echo "==================================================\n";
echo "🎯 SOLUTION 1 (Recommandée) - Surveillance continue :\n";
echo "1. Double-cliquez sur : surveillance_automatique.bat\n";
echo "2. Laissez la fenêtre ouverte\n";
echo "3. Le système vérifiera automatiquement toutes les minutes\n\n";

echo "🎯 SOLUTION 2 - Test immédiat :\n";
echo "1. Lancez : php test_immediat.php\n";
echo "2. Cela forcera l'envoi et vérifiera l'affichage\n\n";

echo "🎯 SOLUTION 3 - Manuel :\n";
echo "1. Lancez : php surveillance_automatique.php\n";
echo "2. Répétez quand nécessaire\n\n";

echo "TEST IMMÉDIAT :\n";
echo "1. Lancez : php test_immediat.php\n";
echo "2. Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
echo "3. Connectez-vous en tant qu'apprenant\n";
echo "4. Vous devriez voir les questionnaires\n";

echo "\n=== SOLUTION DÉFINITIVE TERMINÉE ===\n";
echo "Le système est maintenant configuré pour fonctionner automatiquement !\n"; 
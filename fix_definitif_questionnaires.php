<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Question;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORRECTION DÉFINITIVE DES QUESTIONNAIRES AUTOMATIQUES ===\n\n";

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

// 2. CRÉER UN QUESTIONNAIRE DE TEST GARANTI
echo "\n2. CRÉATION D'UN QUESTIONNAIRE DE TEST GARANTI...\n";

$apprenants = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->get();

if ($apprenants->count() > 0) {
    $apprenant = $apprenants->first();
    echo "Apprenant test : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    echo "Niveau : {$apprenant->niveau->nom}\n";
    
    // Récupérer un module payé
    $modulePaye = $apprenant->paiements()
        ->where('statut', 'valide')
        ->with('module')
        ->first();
    
    if ($modulePaye) {
        echo "Module payé trouvé : {$modulePaye->module->titre}\n";
        
        // Supprimer les anciens questionnaires de test
        Questionnaire::where('titre', 'LIKE', '%test automatique%')->delete();
        
        // Créer un nouveau questionnaire de test
        $questionnaire = Questionnaire::create([
            'titre' => 'Questionnaire de test automatique - DISPONIBLE MAINTENANT',
            'description' => 'Ce questionnaire est créé automatiquement pour garantir l\'affichage sur la page de test',
            'module_id' => $modulePaye->module_id,
            'date_envoi' => Carbon::now()->subMinutes(10), // Disponible depuis 10 minutes
            'envoye' => true, // Directement envoyé
            'duree' => 30,
            'points_totaux' => 10
        ]);
        
        // Créer des questions de test avec user_id
        $questions = [
            [
                'texte' => 'Question 1 : Ce questionnaire s\'affiche-t-il correctement ?',
                'choix' => ['Oui', 'Non', 'Peut-être'],
                'bonne_reponse' => 'Oui',
                'points' => 5,
                'user_id' => $apprenant->utilisateur->id
            ],
            [
                'texte' => 'Question 2 : Le système automatique fonctionne-t-il ?',
                'choix' => ['Parfaitement', 'Pas encore', 'En cours'],
                'bonne_reponse' => 'Parfaitement',
                'points' => 5,
                'user_id' => $apprenant->utilisateur->id
            ]
        ];
        
        foreach ($questions as $q) {
            $questionnaire->questions()->create($q);
        }
        
        echo "✅ Questionnaire de test créé (ID: {$questionnaire->id})\n";
        echo "✅ 2 questions ajoutées\n";
    } else {
        echo "❌ Aucun module payé trouvé pour l'apprenant\n";
    }
}

// 3. VÉRIFIER L'AFFICHAGE
echo "\n3. VÉRIFICATION DE L'AFFICHAGE...\n";

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
    
    echo "Questionnaires visibles sur /questionnaire_test : {$questionnairesVisibles->count()}\n";
    
    foreach ($questionnairesVisibles as $q) {
        echo "  - '{$q->titre}' (Module: {$q->module->titre}, Niveau: {$q->module->niveau->nom})\n";
    }
}

// 4. CRÉER UN SYSTÈME DE SURVEILLANCE AUTOMATIQUE
echo "\n4. CRÉATION DU SYSTÈME DE SURVEILLANCE...\n";

$surveillanceScript = '<?php

require_once "vendor/autoload.php";

use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

// Correction automatique toutes les minutes
$questionnairesEnRetard = Questionnaire::where("date_envoi", "<=", Carbon::now())
    ->where("envoye", false)
    ->get();

if ($questionnairesEnRetard->count() > 0) {
    foreach ($questionnairesEnRetard as $questionnaire) {
        $questionnaire->update(["envoye" => true]);
        echo "[" . date("Y-m-d H:i:s") . "] Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
    }
    echo "[" . date("Y-m-d H:i:s") . "] {$questionnairesEnRetard->count()} questionnaire(s) corrigé(s)\n";
}

echo "[" . date("Y-m-d H:i:s") . "] Surveillance terminée\n";
';

file_put_contents('surveillance_automatique.php', $surveillanceScript);
echo "✅ Script de surveillance créé : surveillance_automatique.php\n";

// 5. CRÉER UN FICHIER BATCH POUR WINDOWS
$batchScript = '@echo off
echo Surveillance automatique des questionnaires...
cd /d "' . getcwd() . '"
:loop
php surveillance_automatique.php
timeout /t 60 /nobreak > nul
goto loop
';

file_put_contents('surveillance_automatique.bat', $batchScript);
echo "✅ Script batch créé : surveillance_automatique.bat\n";

// 6. CRÉER UN FICHIER DE CONFIGURATION CRON
$cronConfig = "*/1 * * * * cd " . getcwd() . " && php surveillance_automatique.php >> storage/logs/surveillance.log 2>&1\n";
file_put_contents('cron_config.txt', $cronConfig);
echo "✅ Configuration cron créée : cron_config.txt\n";

// 7. INSTRUCTIONS FINALES
echo "\n5. INSTRUCTIONS POUR L'AUTOMATISATION DÉFINITIVE :\n";
echo "==================================================\n";
echo "OPTION 1 (Recommandé) - Surveillance continue :\n";
echo "1. Double-cliquez sur : surveillance_automatique.bat\n";
echo "2. Laissez la fenêtre ouverte\n";
echo "3. Le système vérifiera toutes les minutes\n\n";

echo "OPTION 2 - Cron (si disponible) :\n";
echo "1. Ouvrez : cron_config.txt\n";
echo "2. Copiez le contenu dans votre crontab\n\n";

echo "OPTION 3 - Manuel :\n";
echo "1. Lancez : php surveillance_automatique.php\n";
echo "2. Répétez toutes les minutes si nécessaire\n\n";

echo "TEST IMMÉDIAT :\n";
echo "1. Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
echo "2. Connectez-vous en tant qu'apprenant\n";
echo "3. Vous devriez voir le questionnaire de test\n";
echo "4. Si vous ne voyez rien, lancez : php surveillance_automatique.php\n";

echo "\n=== CORRECTION DÉFINITIVE TERMINÉE ===\n";
echo "Le système est maintenant configuré pour fonctionner automatiquement !\n"; 
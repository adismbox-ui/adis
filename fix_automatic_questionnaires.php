<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Niveau;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC ET CORRECTION AUTOMATIQUE DES QUESTIONNAIRES ===\n\n";

// 1. Vérifier l'état actuel des questionnaires
echo "1. État actuel des questionnaires...\n";
$questionnaires = Questionnaire::with(['module.niveau'])->get();

echo "Total questionnaires : {$questionnaires->count()}\n";

$envoyes = $questionnaires->where('envoye', true)->count();
$nonEnvoyes = $questionnaires->where('envoye', false)->count();
$avecDateEnvoi = $questionnaires->whereNotNull('date_envoi')->count();
$sansDateEnvoi = $questionnaires->whereNull('date_envoi')->count();

echo "Questionnaires envoyés : {$envoyes}\n";
echo "Questionnaires non envoyés : {$nonEnvoyes}\n";
echo "Avec date d'envoi : {$avecDateEnvoi}\n";
echo "Sans date d'envoi : {$sansDateEnvoi}\n\n";

// 2. Identifier les problèmes
echo "2. Identification des problèmes...\n";

// Questionnaires avec date d'envoi passée mais non envoyés
$questionnairesEnRetard = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

echo "Questionnaires en retard (date passée mais non envoyés) : {$questionnairesEnRetard->count()}\n";

foreach ($questionnairesEnRetard as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' - Date: {$q->date_envoi} - Envoyé: " . ($q->envoye ? 'Oui' : 'Non') . "\n";
}

// Questionnaires sans date d'envoi
$questionnairesSansDate = Questionnaire::whereNull('date_envoi')->get();
echo "\nQuestionnaires sans date d'envoi : {$questionnairesSansDate->count()}\n";

foreach ($questionnairesSansDate as $q) {
    echo "  - ID {$q->id} : '{$q->titre}' - Envoyé: " . ($q->envoye ? 'Oui' : 'Non') . "\n";
}

// 3. Correction automatique
echo "\n3. CORRECTION AUTOMATIQUE...\n";

// A. Marquer comme envoyés les questionnaires en retard
if ($questionnairesEnRetard->count() > 0) {
    echo "A. Marquage des questionnaires en retard comme envoyés...\n";
    
    foreach ($questionnairesEnRetard as $questionnaire) {
        $questionnaire->update(['envoye' => true]);
        echo "  ✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
    }
}

// B. Créer un questionnaire de test si aucun n'est disponible
echo "\nB. Vérification de la disponibilité des questionnaires...\n";

$apprenants = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->get();

if ($apprenants->count() > 0) {
    $apprenant = $apprenants->first();
    echo "Apprenant test : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    echo "Niveau : {$apprenant->niveau->nom}\n";
    
    // Vérifier les modules payés
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "Modules payés : " . implode(', ', $moduleIds) . "\n";
    
    // Vérifier les questionnaires disponibles
    $questionnairesDisponibles = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "Questionnaires disponibles pour l'apprenant : {$questionnairesDisponibles->count()}\n";
    
    if ($questionnairesDisponibles->count() === 0) {
        echo "❌ Aucun questionnaire disponible pour l'apprenant\n";
        
        // Créer un questionnaire de test
        $module = Module::whereIn('id', $moduleIds)->first();
        if ($module) {
            echo "Création d'un questionnaire de test...\n";
            
            $questionnaire = Questionnaire::create([
                'titre' => 'Questionnaire de test automatique',
                'description' => 'Questionnaire créé automatiquement pour tester l\'affichage',
                'module_id' => $module->id,
                'date_envoi' => Carbon::now()->subMinutes(5), // Disponible depuis 5 minutes
                'envoye' => true,
                'duree' => 30,
                'points_totaux' => 10
            ]);
            
            // Créer une question de test
            $questionnaire->questions()->create([
                'texte' => 'Question de test automatique',
                'choix' => ['Option A', 'Option B', 'Option C'],
                'bonne_reponse' => 'Option A',
                'points' => 10
            ]);
            
            echo "✅ Questionnaire de test créé (ID: {$questionnaire->id})\n";
        } else {
            echo "❌ Aucun module valide trouvé pour créer un questionnaire de test\n";
        }
    } else {
        echo "✅ Des questionnaires sont disponibles pour l'apprenant\n";
    }
}

// 4. Vérification finale
echo "\n4. VÉRIFICATION FINALE...\n";

$questionnairesEnvoyes = Questionnaire::where('envoye', true)->count();
$questionnairesNonEnvoyes = Questionnaire::where('envoye', false)->count();

echo "Questionnaires envoyés : {$questionnairesEnvoyes}\n";
echo "Questionnaires non envoyés : {$questionnairesNonEnvoyes}\n";

// 5. Test de la page questionnaire_test
echo "\n5. TEST DE LA PAGE QUESTIONNAIRE_TEST...\n";

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

// 6. Instructions pour tester
echo "\n6. INSTRUCTIONS POUR TESTER :\n";
echo "================================\n";
echo "1. Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
echo "2. Connectez-vous en tant qu'apprenant\n";
echo "3. Vérifiez que les questionnaires s'affichent maintenant\n";
echo "4. Si aucun questionnaire n'apparaît, vérifiez :\n";
echo "   - Que l'apprenant a payé des modules\n";
echo "   - Que les modules correspondent à son niveau\n";
echo "   - Que les questionnaires ont bien envoye = true\n";

// 7. Configuration du cron (optionnel)
echo "\n7. CONFIGURATION DU CRON (optionnel) :\n";
echo "Pour automatiser l'envoi, ajoutez cette ligne à votre crontab :\n";
echo "*/5 * * * * cd " . getcwd() . " && php artisan content:send-scheduled >> storage/logs/cron.log 2>&1\n";
echo "Cela vérifiera toutes les 5 minutes les questionnaires à envoyer.\n";

echo "\n=== CORRECTION TERMINÉE ===\n"; 
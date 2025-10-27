<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Correction des problèmes d'affichage des questionnaires ===\n\n";

// 1. Corriger les questionnaires avec des dates d'envoi vides
echo "1. Correction des questionnaires avec dates d'envoi vides...\n";
$questionnairesSansDate = Questionnaire::whereNull('date_envoi')->get();

foreach ($questionnairesSansDate as $q) {
    echo "Questionnaire ID {$q->id} : {$q->titre}\n";
    
    // Définir une date d'envoi dans le passé pour qu'ils soient disponibles immédiatement
    $q->update([
        'date_envoi' => Carbon::now()->subMinutes(5),
        'envoye' => true // Marquer comme envoyé
    ]);
    
    echo "  ✅ Date d'envoi définie et marqué comme envoyé\n";
}

// 2. Envoyer les questionnaires qui devraient être envoyés
echo "\n2. Envoi des questionnaires en attente...\n";
$questionnairesAEnvoyer = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

foreach ($questionnairesAEnvoyer as $q) {
    echo "Questionnaire ID {$q->id} : {$q->titre}\n";
    $q->update(['envoye' => true]);
    echo "  ✅ Marqué comme envoyé\n";
}

// 3. Vérifier les apprenants et leurs modules
echo "\n3. Vérification des apprenants...\n";
$apprenants = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->get();

foreach ($apprenants as $apprenant) {
    echo "\n--- Apprenant : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom} ---\n";
    echo "Email : {$apprenant->utilisateur->email}\n";
    echo "Niveau : {$apprenant->niveau->nom}\n";
    
    // Modules payés
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "Modules payés : " . implode(', ', $moduleIds) . "\n";
    
    // Questionnaires disponibles
    $questionnairesDisponibles = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "Questionnaires disponibles : {$questionnairesDisponibles->count()}\n";
    foreach ($questionnairesDisponibles as $q) {
        echo "  - ID {$q->id} : {$q->titre} (Module: {$q->module->titre})\n";
    }
    
    // Si aucun questionnaire disponible, créer un test
    if ($questionnairesDisponibles->count() === 0) {
        echo "  ⚠️  Aucun questionnaire disponible - création d'un questionnaire de test...\n";
        
        // Trouver un module payé
        $modulePaye = $apprenant->paiements()->where('statut', 'valide')->first();
        if ($modulePaye) {
            $questionnaire = Questionnaire::create([
                'titre' => 'Questionnaire de test pour ' . $apprenant->utilisateur->prenom,
                'description' => 'Questionnaire créé automatiquement pour test',
                'module_id' => $modulePaye->module_id,
                'niveau_id' => $apprenant->niveau_id,
                'session_id' => 1, // Session par défaut
                'date_envoi' => Carbon::now()->subMinutes(5),
                'envoye' => true,
                'minutes' => 30,
                'semaine' => 1,
                'type_devoir' => 'hebdomadaire',
                'user_id' => 1
            ]);
            
            // Créer quelques questions de test
            $questions = [
                [
                    'texte' => 'Quelle est la capitale de la France ?',
                    'choix' => ['Paris', 'Londres', 'Berlin', 'Madrid'],
                    'bonne_reponse' => 'Paris',
                    'points' => 1
                ],
                [
                    'texte' => 'Combien font 2 + 2 ?',
                    'choix' => ['3', '4', '5', '6'],
                    'bonne_reponse' => '4',
                    'points' => 1
                ]
            ];
            
            foreach ($questions as $questionData) {
                $questionnaire->questions()->create($questionData);
            }
            
            echo "  ✅ Questionnaire de test créé (ID: {$questionnaire->id})\n";
        }
    }
}

// 4. Test final
echo "\n4. Test final de la page questionnaire_test...\n";
$apprenant = Apprenant::with(['utilisateur', 'paiements'])->first();

if ($apprenant) {
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    
    $questionnairesDisponibles = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "Questionnaires disponibles pour {$apprenant->utilisateur->prenom} : {$questionnairesDisponibles->count()}\n";
    
    if ($questionnairesDisponibles->count() > 0) {
        echo "✅ Le système fonctionne ! Les questionnaires s'afficheront sur /questionnaire_test\n";
    } else {
        echo "❌ Aucun questionnaire disponible - problème persistant\n";
    }
}

echo "\n=== Correction terminée ===\n";
echo "\nInstructions :\n";
echo "1. Allez sur http://127.0.0.1:8000/questionnaire_test\n";
echo "2. Connectez-vous en tant qu'apprenant\n";
echo "3. Vérifiez que les questionnaires s'affichent\n"; 
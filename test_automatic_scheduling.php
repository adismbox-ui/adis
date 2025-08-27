<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\User;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test du système de programmation automatique ===\n\n";

// 1. Vérifier les questionnaires programmés
echo "1. Questionnaires programmés :\n";
$questionnaires = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

foreach ($questionnaires as $q) {
    echo "- {$q->titre} (Date d'envoi: {$q->date_envoi}, Envoyé: " . ($q->envoye ? 'Oui' : 'Non') . ")\n";
}

// 2. Simuler l'envoi automatique
echo "\n2. Simulation de l'envoi automatique...\n";
foreach ($questionnaires as $questionnaire) {
    echo "Envoi du questionnaire: {$questionnaire->titre}\n";
    
    // Marquer comme envoyé
    $questionnaire->update(['envoye' => true]);
    
    // Récupérer les apprenants concernés
    $apprenants = Apprenant::with('utilisateur')
        ->whereHas('paiements', function($q) use ($questionnaire) {
            $q->where('module_id', $questionnaire->module_id)
              ->where('statut', 'valide');
        })
        ->whereHas('inscriptions.module', function($q) use ($questionnaire) {
            $q->where('niveau_id', $questionnaire->niveau_id);
        })
        ->get();
    
    echo "  -> {$apprenants->count()} apprenant(s) concerné(s)\n";
    
    foreach ($apprenants as $apprenant) {
        echo "    - {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom} ({$apprenant->utilisateur->email})\n";
    }
}

// 3. Vérifier les questionnaires maintenant disponibles
echo "\n3. Questionnaires maintenant disponibles pour les apprenants :\n";
$questionnairesDisponibles = Questionnaire::with(['module.niveau'])
    ->where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

foreach ($questionnairesDisponibles as $q) {
    echo "- {$q->titre} (Module: {$q->module->titre}, Niveau: {$q->module->niveau->nom})\n";
}

// 4. Test avec un apprenant spécifique
echo "\n4. Test avec un apprenant :\n";
$apprenant = Apprenant::with(['utilisateur', 'paiements'])->first();
if ($apprenant) {
    echo "Apprenant: {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "Modules payés: " . implode(', ', $moduleIds) . "\n";
    
    $questionnairesPourApprenant = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "Questionnaires disponibles pour cet apprenant: {$questionnairesPourApprenant->count()}\n";
    foreach ($questionnairesPourApprenant as $q) {
        echo "  - {$q->titre}\n";
    }
}

echo "\n=== Test terminé ===\n"; 
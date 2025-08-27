<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test en temps réel du système de programmation automatique ===\n\n";
echo "Ce script vérifie toutes les 30 secondes s'il y a des questionnaires à envoyer.\n";
echo "Appuyez sur Ctrl+C pour arrêter.\n\n";

$iteration = 1;

while (true) {
    echo "\n--- Itération {$iteration} ---\n";
    echo "⏰ " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";
    
    // 1. Vérifier les questionnaires programmés
    $questionnairesProgrammes = Questionnaire::where('date_envoi', '<=', Carbon::now())
        ->where('envoye', false)
        ->get();
    
    echo "📋 Questionnaires programmés : {$questionnairesProgrammes->count()}\n";
    
    foreach ($questionnairesProgrammes as $q) {
        echo "  - {$q->titre} (Date: {$q->date_envoi})\n";
    }
    
    // 2. Vérifier les questionnaires envoyés
    $questionnairesEnvoyes = Questionnaire::where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "✅ Questionnaires envoyés : {$questionnairesEnvoyes->count()}\n";
    
    // 3. Vérifier les questionnaires disponibles pour les apprenants
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
        
        echo "👤 Questionnaires disponibles pour {$apprenant->utilisateur->prenom} : {$questionnairesDisponibles->count()}\n";
        
        foreach ($questionnairesDisponibles as $q) {
            echo "  - {$q->titre} (Module: {$q->module->titre})\n";
        }
    }
    
    // 4. Si il y a des questionnaires à envoyer, les envoyer
    if ($questionnairesProgrammes->count() > 0) {
        echo "\n🚀 Envoi automatique en cours...\n";
        $command = new \App\Console\Commands\SendScheduledContent();
        $command->handle();
        echo "✅ Envoi terminé\n";
    } else {
        echo "\nℹ️  Aucun questionnaire à envoyer pour le moment\n";
    }
    
    echo "\n⏳ Attente de 30 secondes...\n";
    sleep(30);
    $iteration++;
} 
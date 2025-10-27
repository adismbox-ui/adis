<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Surveillance en temps réel de l'affichage des questionnaires ===\n\n";
echo "Ce script vérifie toutes les 10 secondes si de nouveaux questionnaires apparaissent.\n";
echo "Appuyez sur Ctrl+C pour arrêter.\n\n";

$iteration = 1;
$derniersQuestionnaires = [];

while (true) {
    echo "\n--- Vérification {$iteration} ---\n";
    echo "⏰ " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";
    
    // 1. Vérifier les questionnaires qui doivent être envoyés
    $questionnairesAEnvoyer = Questionnaire::where('date_envoi', '<=', Carbon::now())
        ->where('envoye', false)
        ->get();
    
    if ($questionnairesAEnvoyer->count() > 0) {
        echo "🚀 Envoi de {$questionnairesAEnvoyer->count()} questionnaire(s)...\n";
        foreach ($questionnairesAEnvoyer as $q) {
            $q->update(['envoye' => true]);
            echo "  ✅ Questionnaire '{$q->titre}' envoyé !\n";
        }
    }
    
    // 2. Vérifier les questionnaires disponibles pour l'apprenant
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
        
        echo "📋 Questionnaires disponibles pour {$apprenant->utilisateur->prenom} : {$questionnairesDisponibles->count()}\n";
        
        // Vérifier s'il y a de nouveaux questionnaires
        $nouveauxQuestionnaires = [];
        foreach ($questionnairesDisponibles as $q) {
            if (!in_array($q->id, $derniersQuestionnaires)) {
                $nouveauxQuestionnaires[] = $q;
            }
        }
        
        if (count($nouveauxQuestionnaires) > 0) {
            echo "🎉 NOUVEAUX QUESTIONNAIRES DISPONIBLES !\n";
            foreach ($nouveauxQuestionnaires as $q) {
                echo "  ✨ {$q->titre} (Module: {$q->module->titre})\n";
            }
            echo "\n🌐 Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
        }
        
        // Mettre à jour la liste des questionnaires connus
        $derniersQuestionnaires = $questionnairesDisponibles->pluck('id')->toArray();
        
        // Afficher tous les questionnaires disponibles
        foreach ($questionnairesDisponibles as $q) {
            $dateEnvoi = Carbon::parse($q->date_envoi);
            $minutesDepuisEnvoi = $dateEnvoi->diffInMinutes(Carbon::now());
            echo "  - ID {$q->id} : {$q->titre} (Envoyé il y a {$minutesDepuisEnvoi} min)\n";
        }
    }
    
    // 3. Vérifier les questionnaires programmés (pas encore envoyés)
    $questionnairesProgrammes = Questionnaire::where('envoye', false)
        ->where('date_envoi', '>', Carbon::now())
        ->get();
    
    if ($questionnairesProgrammes->count() > 0) {
        echo "\n⏳ Questionnaires programmés : {$questionnairesProgrammes->count()}\n";
        foreach ($questionnairesProgrammes as $q) {
            $dateEnvoi = Carbon::parse($q->date_envoi);
            $minutesRestantes = $dateEnvoi->diffInMinutes(Carbon::now());
            echo "  - ID {$q->id} : {$q->titre} (Envoi dans {$minutesRestantes} min)\n";
        }
    }
    
    echo "\n⏳ Attente de 10 secondes...\n";
    sleep(10);
    $iteration++;
} 
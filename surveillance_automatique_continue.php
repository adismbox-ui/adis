<?php

require_once 'vendor/autoload.php';

use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SURVEILLANCE AUTOMATIQUE CONTINUE ===\n";
echo "Appuyez sur Ctrl+C pour arrêter\n\n";

$iteration = 1;

while (true) {
    echo "=== Itération {$iteration} - " . Carbon::now()->format('d/m/Y H:i:s') . " ===\n";
    
    try {
        // 1. Vérifier les questionnaires en retard
        $questionnairesEnRetard = Questionnaire::where('envoye', false)
            ->where('date_envoi', '<=', Carbon::now())
            ->get();
        
        if ($questionnairesEnRetard->count() > 0) {
            echo "📋 {$questionnairesEnRetard->count()} questionnaire(s) à envoyer :\n";
            
            foreach ($questionnairesEnRetard as $q) {
                $q->update(['envoye' => true]);
                $dateEnvoi = Carbon::parse($q->date_envoi);
                echo "  ✅ ID {$q->id} : '{$q->titre}' envoyé (retard de " . $dateEnvoi->diffForHumans() . ")\n";
            }
            
            echo "🎉 Envoi automatique terminé !\n";
        } else {
            echo "✅ Aucun questionnaire à envoyer\n";
        }
        
        // 2. Afficher le statut général
        $totalQuestionnaires = Questionnaire::count();
        $questionnairesEnvoyes = Questionnaire::where('envoye', true)->count();
        $questionnairesProgrammes = Questionnaire::where('envoye', false)
            ->where('date_envoi', '>', Carbon::now())
            ->count();
        
        echo "📊 Statut : {$questionnairesEnvoyes}/{$totalQuestionnaires} envoyés, {$questionnairesProgrammes} programmés\n";
        
        // 3. Afficher les prochains envois
        $prochainsEnvois = Questionnaire::where('envoye', false)
            ->where('date_envoi', '>', Carbon::now())
            ->orderBy('date_envoi')
            ->limit(3)
            ->get();
        
        if ($prochainsEnvois->count() > 0) {
            echo "⏰ Prochains envois :\n";
            foreach ($prochainsEnvois as $q) {
                $dateEnvoi = Carbon::parse($q->date_envoi);
                echo "  - ID {$q->id} : '{$q->titre}' dans " . $dateEnvoi->diffForHumans() . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur : " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Attendre 60 secondes avant la prochaine vérification
    sleep(60);
    $iteration++;
} 
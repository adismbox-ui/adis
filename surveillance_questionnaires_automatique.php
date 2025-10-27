<?php
/**
 * Script de Surveillance Continue des Questionnaires
 * 
 * Ce script vérifie toutes les 30 secondes si des questionnaires doivent être envoyés
 * et les envoie automatiquement quand leur date d'envoi est atteinte.
 * 
 * Usage : php surveillance_questionnaires_automatique.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Démarrage de la surveillance automatique des questionnaires...\n";
echo "⏰ Heure de démarrage : " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
echo "📋 Vérification toutes les 30 secondes...\n";
echo "🛑 Pour arrêter : Ctrl+C\n\n";

$iteration = 0;

while (true) {
    $iteration++;
    $now = Carbon::now();
    
    echo "\n[{$now->format('H:i:s')}] Vérification #{$iteration}...\n";
    
    try {
        // Récupérer les questionnaires à envoyer
        $questionnaires = Questionnaire::where('date_envoi', '<=', $now)
            ->where('envoye', false)
            ->with(['session', 'niveau', 'module'])
            ->orderBy('date_envoi', 'asc')
            ->get();
        
        if ($questionnaires->count() > 0) {
            echo "📤 Trouvé {$questionnaires->count()} questionnaire(s) à envoyer\n";
            
            foreach ($questionnaires as $questionnaire) {
                $retard = Carbon::parse($questionnaire->date_envoi)->diffInMinutes($now);
                $statut = Carbon::parse($questionnaire->date_envoi) < $now ? "EN RETARD ({$retard} min)" : "À L'HEURE";
                
                echo "  📋 Questionnaire : {$questionnaire->titre}\n";
                echo "  📅 Date d'envoi : {$questionnaire->date_envoi}\n";
                echo "  ⏰ Statut : {$statut}\n";
                
                try {
                    // Envoyer le questionnaire
                    sendQuestionnaireToApprenants($questionnaire);
                    $questionnaire->update(['envoye' => true]);
                    echo "  ✅ Envoyé avec succès\n";
                    
                } catch (Exception $e) {
                    echo "  ❌ Erreur : " . $e->getMessage() . "\n";
                    Log::error("Erreur envoi questionnaire {$questionnaire->id}: " . $e->getMessage());
                }
            }
        } else {
            echo "ℹ️  Aucun questionnaire à envoyer pour le moment\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur générale : " . $e->getMessage() . "\n";
        Log::error("Erreur surveillance questionnaires: " . $e->getMessage());
    }
    
    // Attendre 30 secondes
    sleep(30);
}

/**
 * Envoie un questionnaire aux apprenants concernés
 */
function sendQuestionnaireToApprenants($questionnaire) {
    // Récupérer les apprenants selon le niveau et/ou module
    $apprenants = getApprenantsForQuestionnaire($questionnaire);
    
    if ($apprenants->count() === 0) {
        echo "  ⚠️  Aucun apprenant trouvé pour ce questionnaire\n";
        return;
    }
    
    echo "  👥 {$apprenants->count()} apprenant(s) concerné(s)\n";
    
    foreach ($apprenants as $apprenant) {
        try {
            // Envoyer l'email de notification
            sendQuestionnaireEmail($apprenant, $questionnaire);
            
            // Créer une notification en base de données
            createNotification($apprenant, 'questionnaire', $questionnaire);
            
        } catch (Exception $e) {
            echo "  ❌ Erreur envoi à {$apprenant->utilisateur->email}: " . $e->getMessage() . "\n";
            Log::error("Erreur envoi questionnaire à apprenant {$apprenant->id}: " . $e->getMessage());
        }
    }
}

/**
 * Récupère les apprenants concernés par le questionnaire
 */
function getApprenantsForQuestionnaire($questionnaire) {
    $query = Apprenant::with('utilisateur');
    
    // Filtrer par niveau si spécifié
    if ($questionnaire->niveau_id) {
        $query->whereHas('inscriptions.module', function($q) use ($questionnaire) {
            $q->where('niveau_id', $questionnaire->niveau_id);
        });
    }
    
    // Filtrer par module si spécifié
    if ($questionnaire->module_id) {
        $query->whereHas('inscriptions', function($q) use ($questionnaire) {
            $q->where('module_id', $questionnaire->module_id);
        });
    }
    
    // Filtrer par session si spécifiée
    if ($questionnaire->session_id) {
        $query->whereHas('inscriptions', function($q) use ($questionnaire) {
            $q->where('session_formation_id', $questionnaire->session_id);
        });
    }
    
    return $query->get();
}

/**
 * Envoie l'email de notification pour un questionnaire
 */
function sendQuestionnaireEmail($apprenant, $questionnaire) {
    $data = [
        'apprenant' => $apprenant,
        'questionnaire' => $questionnaire,
        'url' => route('questionnaire.answer', $questionnaire->id)
    ];
    
    Mail::send('emails.questionnaire-notification', $data, function($message) use ($apprenant, $questionnaire) {
        $message->to($apprenant->utilisateur->email, $apprenant->utilisateur->prenom . ' ' . $apprenant->utilisateur->nom)
                ->subject("Nouveau questionnaire disponible : {$questionnaire->titre}");
    });
}

/**
 * Crée une notification en base de données
 */
function createNotification($apprenant, $type, $content) {
    $message = $type === 'questionnaire' 
        ? "Nouveau questionnaire disponible : {$content->titre}"
        : "Nouveau document disponible : {$content->titre}";
        
    Notification::create([
        'utilisateur_id' => $apprenant->utilisateur_id,
        'titre' => $message,
        'message' => $message,
        'type' => $type,
        'lien' => $type === 'questionnaire' 
            ? route('questionnaire.answer', $content->id)
            : route('documents.show', $content->id),
        'lu' => false
    ]);
} 
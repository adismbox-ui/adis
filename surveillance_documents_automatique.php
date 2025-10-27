<?php
/**
 * Script de Surveillance Continue des Documents
 * 
 * Ce script vérifie toutes les 30 secondes si des documents doivent être envoyés
 * et les envoie automatiquement quand leur date d'envoi est atteinte.
 * 
 * Usage : php surveillance_documents_automatique.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Document;
use App\Models\Apprenant;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Démarrage de la surveillance automatique des documents...\n";
echo "⏰ Heure de démarrage : " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
echo "📋 Vérification toutes les 30 secondes...\n";
echo "🛑 Pour arrêter : Ctrl+C\n\n";

$iteration = 0;

while (true) {
    $iteration++;
    $now = Carbon::now();
    
    echo "\n[{$now->format('H:i:s')}] Vérification #{$iteration}...\n";
    
    try {
        // Récupérer les documents à envoyer
        $documents = Document::where('date_envoi', '<=', $now)
            ->where('envoye', false)
            ->with(['session', 'niveau', 'module'])
            ->orderBy('date_envoi', 'asc')
            ->get();
        
        if ($documents->count() > 0) {
            echo "📤 Trouvé {$documents->count()} document(s) à envoyer\n";
            
            foreach ($documents as $document) {
                $retard = Carbon::parse($document->date_envoi)->diffInMinutes($now);
                $statut = Carbon::parse($document->date_envoi) < $now ? "EN RETARD ({$retard} min)" : "À L'HEURE";
                
                echo "  📄 Document : {$document->titre}\n";
                echo "  📅 Date d'envoi : {$document->date_envoi}\n";
                echo "  ⏰ Statut : {$statut}\n";
                
                try {
                    // Envoyer le document
                    sendDocumentToApprenants($document);
                    $document->update(['envoye' => true]);
                    echo "  ✅ Envoyé avec succès\n";
                    
                } catch (Exception $e) {
                    echo "  ❌ Erreur : " . $e->getMessage() . "\n";
                    Log::error("Erreur envoi document {$document->id}: " . $e->getMessage());
                }
            }
        } else {
            echo "ℹ️  Aucun document à envoyer pour le moment\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur générale : " . $e->getMessage() . "\n";
        Log::error("Erreur surveillance documents: " . $e->getMessage());
    }
    
    // Attendre 30 secondes
    sleep(30);
}

/**
 * Envoie un document aux apprenants concernés
 */
function sendDocumentToApprenants($document) {
    // Récupérer les apprenants selon le niveau et/ou module
    $apprenants = getApprenantsForDocument($document);
    
    if ($apprenants->count() === 0) {
        echo "  ⚠️  Aucun apprenant trouvé pour ce document\n";
        return;
    }
    
    echo "  👥 {$apprenants->count()} apprenant(s) concerné(s)\n";
    
    foreach ($apprenants as $apprenant) {
        try {
            // Envoyer l'email de notification
            sendDocumentEmail($apprenant, $document);
            
            // Créer une notification en base de données
            createNotification($apprenant, 'document', $document);
            
        } catch (Exception $e) {
            echo "  ❌ Erreur envoi à {$apprenant->utilisateur->email}: " . $e->getMessage() . "\n";
            Log::error("Erreur envoi document à apprenant {$apprenant->id}: " . $e->getMessage());
        }
    }
}

/**
 * Récupère les apprenants concernés par le document
 */
function getApprenantsForDocument($document) {
    $query = Apprenant::with('utilisateur');
    
    // Filtrer par niveau si spécifié
    if ($document->niveau_id) {
        $query->whereHas('inscriptions.module', function($q) use ($document) {
            $q->where('niveau_id', $document->niveau_id);
        });
    }
    
    // Filtrer par module si spécifié
    if ($document->module_id) {
        $query->whereHas('inscriptions', function($q) use ($document) {
            $q->where('module_id', $document->module_id);
        });
    }
    
    // Filtrer par session si spécifiée
    if ($document->session_id) {
        $query->whereHas('inscriptions', function($q) use ($document) {
            $q->where('session_formation_id', $document->session_id);
        });
    }
    
    return $query->get();
}

/**
 * Envoie l'email de notification pour un document
 */
function sendDocumentEmail($apprenant, $document) {
    $data = [
        'apprenant' => $apprenant,
        'document' => $document,
        'url' => route('documents.show', $document->id)
    ];
    
    Mail::send('emails.document-notification', $data, function($message) use ($apprenant, $document) {
        $message->to($apprenant->utilisateur->email, $apprenant->utilisateur->prenom . ' ' . $apprenant->utilisateur->nom)
                ->subject("Nouveau document disponible : {$document->titre}");
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
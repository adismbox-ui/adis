<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Questionnaire;
use App\Models\Document;
use App\Models\SessionFormation;
use App\Models\Apprenant;
use App\Models\Inscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendScheduledContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie automatiquement les questionnaires et documents programmés pour le dimanche soir à 13h00';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Début de l\'envoi automatique des contenus programmés...');
        
        $now = Carbon::now(config('app.timezone'));
        $this->info("⏰ Heure actuelle : {$now->format('Y-m-d H:i:s')}");
        
        // DÉSACTIVÉ : Vérification de l'heure (pour les tests)
        // if ($now->dayOfWeek !== Carbon::SUNDAY || $now->hour !== 13) {
        //     $this->warn('Ce n\'est pas le moment d\'envoyer les contenus (dimanche 13h00 requis)');
        //     return;
        // }
        
        $this->info('✅ Envoi automatique activé (vérification d\'heure désactivée pour les tests)...');
        
        // Envoyer les questionnaires programmés
        $this->sendScheduledQuestionnaires();
        
        // Envoyer les documents programmés
        $this->sendScheduledDocuments();
        
        $this->info('🎉 Envoi automatique terminé avec succès !');
    }
    
    /**
     * Envoie les questionnaires programmés
     */
    private function sendScheduledQuestionnaires()
    {
        $this->info('📋 Envoi des questionnaires programmés...');
        
        $questionnaires = Questionnaire::where('date_envoi', '<=', Carbon::now(config('app.timezone')))
            ->where('envoye', false)
            ->with(['session', 'niveau', 'module'])
            ->orderBy('date_envoi', 'asc') // Envoyer d'abord les plus anciens
            ->get();
            
        $this->info("🔍 Trouvé {$questionnaires->count()} questionnaire(s) à envoyer");
        
        if ($questionnaires->count() === 0) {
            $this->info("ℹ️  Aucun questionnaire à envoyer pour le moment");
            return;
        }
        
        foreach ($questionnaires as $questionnaire) {
            try {
                $now = Carbon::now();
                $retard = Carbon::parse($questionnaire->date_envoi)->diffInMinutes($now);
                $statut = Carbon::parse($questionnaire->date_envoi) < $now ? "EN RETARD ({$retard} min)" : "À L'HEURE";
                
                $this->info("📤 Envoi du questionnaire : {$questionnaire->titre}");
                $this->info("   📅 Date d'envoi programmée : {$questionnaire->date_envoi}");
                $this->info("   ⏰ Statut : {$statut}");
                $this->info("   📚 Module : {$questionnaire->module->titre}");
                $this->info("   🎓 Niveau : {$questionnaire->module->niveau->nom}");
                
                $this->sendQuestionnaireToApprenants($questionnaire);
                $questionnaire->update(['envoye' => true]);
                $this->info("✅ Questionnaire '{$questionnaire->titre}' envoyé avec succès");
                
            } catch (\Exception $e) {
                $this->error("❌ Erreur lors de l'envoi du questionnaire '{$questionnaire->titre}': " . $e->getMessage());
                Log::error("Erreur envoi questionnaire {$questionnaire->id}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Envoie les documents programmés
     */
    private function sendScheduledDocuments()
    {
        $this->info('📄 Envoi des documents programmés...');
        
        $documents = Document::where('date_envoi', '<=', Carbon::now(config('app.timezone')))
            ->where('envoye', false)
            ->with(['session', 'niveau', 'module'])
            ->orderBy('date_envoi', 'asc') // Envoyer d'abord les plus anciens
            ->get();
            
        $this->info("🔍 Trouvé {$documents->count()} document(s) à envoyer");
        
        if ($documents->count() === 0) {
            $this->info("ℹ️  Aucun document à envoyer pour le moment");
            return;
        }
        
        foreach ($documents as $document) {
            try {
                $now = Carbon::now();
                $retard = Carbon::parse($document->date_envoi)->diffInMinutes($now);
                $statut = Carbon::parse($document->date_envoi) < $now ? "EN RETARD ({$retard} min)" : "À L'HEURE";
                
                $this->info("📤 Envoi du document : {$document->titre}");
                $this->info("   📅 Date d'envoi programmée : {$document->date_envoi}");
                $this->info("   ⏰ Statut : {$statut}");
                $this->info("   📚 Module : " . ($document->module ? $document->module->titre : 'Général'));
                $this->info("   🎓 Niveau : " . ($document->niveau ? $document->niveau->nom : 'Non spécifié'));
                
                $this->sendDocumentToApprenants($document);
                $document->update(['envoye' => true]);
                $this->info("✅ Document '{$document->titre}' envoyé avec succès");
                
            } catch (\Exception $e) {
                $this->error("❌ Erreur lors de l'envoi du document '{$document->titre}': " . $e->getMessage());
                Log::error("Erreur envoi document {$document->id}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Envoie un questionnaire aux apprenants concernés
     */
    private function sendQuestionnaireToApprenants($questionnaire)
    {
        // Récupérer les apprenants selon le niveau et/ou module
        $apprenants = $this->getApprenantsForContent($questionnaire);
        
        $this->info("   👥 {$apprenants->count()} apprenant(s) concerné(s)");
        
        if ($apprenants->count() === 0) {
            $this->warn("   ⚠️  Aucun apprenant trouvé pour ce questionnaire");
            return;
        }
        
        foreach ($apprenants as $apprenant) {
            try {
                $this->info("   📧 Envoi à : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom} ({$apprenant->utilisateur->email})");
                
                // Envoyer l'email de notification
                $this->sendQuestionnaireEmail($apprenant, $questionnaire);
                
                // Créer une notification en base de données
                $this->createNotification($apprenant, 'questionnaire', $questionnaire);
                
                $this->info("   ✅ Notification envoyée avec succès");
                
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur envoi à {$apprenant->utilisateur->email}: " . $e->getMessage());
                Log::error("Erreur envoi questionnaire à apprenant {$apprenant->id}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Envoie un document aux apprenants concernés
     */
    private function sendDocumentToApprenants($document)
    {
        // Récupérer les apprenants selon le niveau et/ou module
        $apprenants = $this->getApprenantsForContent($document);
        
        $this->info("   👥 {$apprenants->count()} apprenant(s) concerné(s)");
        
        if ($apprenants->count() === 0) {
            $this->warn("   ⚠️  Aucun apprenant trouvé pour ce document");
            return;
        }
        
        foreach ($apprenants as $apprenant) {
            try {
                $this->info("   📧 Envoi à : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom} ({$apprenant->utilisateur->email})");
                
                // Envoyer l'email de notification
                $this->sendDocumentEmail($apprenant, $document);
                
                // Créer une notification en base de données
                $this->createNotification($apprenant, 'document', $document);
                
                $this->info("   ✅ Notification envoyée avec succès");
                
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur envoi à {$apprenant->utilisateur->email}: " . $e->getMessage());
                Log::error("Erreur envoi document à apprenant {$apprenant->id}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Récupère les apprenants concernés par le contenu
     */
    private function getApprenantsForContent($content)
    {
        $query = Apprenant::with('utilisateur');
        
        // Filtrer par niveau si spécifié
        if ($content->niveau_id) {
            $query->whereHas('inscriptions.module', function($q) use ($content) {
                $q->where('niveau_id', $content->niveau_id);
            });
        }
        
        // Filtrer par module si spécifié
        if ($content->module_id) {
            $query->whereHas('inscriptions', function($q) use ($content) {
                $q->where('module_id', $content->module_id);
            });
        }
        
        // Filtrer par session si spécifiée
        if ($content->session_id) {
            $query->whereHas('inscriptions', function($q) use ($content) {
                $q->where('session_formation_id', $content->session_id);
            });
        }
        
        return $query->get();
    }
    
    /**
     * Envoie l'email de notification pour un questionnaire
     */
    private function sendQuestionnaireEmail($apprenant, $questionnaire)
    {
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
     * Envoie l'email de notification pour un document
     */
    private function sendDocumentEmail($apprenant, $document)
    {
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
    private function createNotification($apprenant, $type, $content)
    {
        $message = $type === 'questionnaire' 
            ? "Nouveau questionnaire disponible : {$content->titre}"
            : "Nouveau document disponible : {$content->titre}";
            
        \App\Models\Notification::create([
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
} 
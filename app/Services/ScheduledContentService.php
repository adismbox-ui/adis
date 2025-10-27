<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\Document;
use App\Models\Apprenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ScheduledContentService
{
    public function sendScheduledQuestionnaires()
    {
        $questionnaires = Questionnaire::where('date_envoi', '<=', Carbon::now())
            ->where('envoye', false)
            ->with(['session', 'niveau', 'module'])
            ->orderBy('date_envoi', 'asc')
            ->get();
        $envoyes = [];
        foreach ($questionnaires as $questionnaire) {
            try {
                $this->sendQuestionnaireToApprenants($questionnaire);
                $questionnaire->update(['envoye' => true]);
                $envoyes[] = $questionnaire->titre;
            } catch (\Exception $e) {
                Log::error("Erreur envoi questionnaire {$questionnaire->id}: " . $e->getMessage());
            }
        }
        return $envoyes;
    }

    public function sendScheduledDocuments()
    {
        $documents = Document::where('date_envoi', '<=', Carbon::now())
            ->where('envoye', false)
            ->with(['session', 'niveau', 'module'])
            ->orderBy('date_envoi', 'asc')
            ->get();
        $envoyes = [];
        foreach ($documents as $document) {
            try {
                $this->sendDocumentToApprenants($document);
                $document->update(['envoye' => true]);
                $envoyes[] = $document->titre;
            } catch (\Exception $e) {
                Log::error("Erreur envoi document {$document->id}: " . $e->getMessage());
            }
        }
        return $envoyes;
    }

    private function sendQuestionnaireToApprenants($questionnaire)
    {
        $apprenants = $this->getApprenantsForContent($questionnaire);
        foreach ($apprenants as $apprenant) {
            try {
                $this->sendQuestionnaireEmail($apprenant, $questionnaire);
                $this->createNotification($apprenant, 'questionnaire', $questionnaire);
            } catch (\Exception $e) {
                Log::error("Erreur envoi questionnaire à apprenant {$apprenant->id}: " . $e->getMessage());
            }
        }
    }

    private function sendDocumentToApprenants($document)
    {
        $apprenants = $this->getApprenantsForContent($document);
        foreach ($apprenants as $apprenant) {
            try {
                $this->sendDocumentEmail($apprenant, $document);
                $this->createNotification($apprenant, 'document', $document);
            } catch (\Exception $e) {
                Log::error("Erreur envoi document à apprenant {$apprenant->id}: " . $e->getMessage());
            }
        }
    }

    private function getApprenantsForContent($content)
    {
        $query = Apprenant::with('utilisateur');
        if ($content->niveau_id) {
            $query->whereHas('inscriptions.module', function($q) use ($content) {
                $q->where('niveau_id', $content->niveau_id);
            });
        }
        if ($content->module_id) {
            $query->whereHas('inscriptions', function($q) use ($content) {
                $q->where('module_id', $content->module_id);
            });
        }
        if ($content->session_id) {
            $query->whereHas('inscriptions', function($q) use ($content) {
                $q->where('session_formation_id', $content->session_id);
            });
        }
        return $query->get();
    }

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

    private function createNotification($apprenant, $type, $content)
    {
        // À adapter selon votre modèle Notification
        // Notification::create([...]);
    }
}
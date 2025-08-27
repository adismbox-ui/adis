<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Apprenant;
use App\Models\Questionnaire;
use App\Models\Document;
use Carbon\Carbon;

class ApprenantNotificationController extends Controller
{
    /**
     * Afficher les notifications de l'apprenant
     */
    public function index()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        
        if (!$apprenant) {
            return redirect()->back()->with('error', 'Apprenant non trouvé');
        }

        // Récupérer les notifications de l'apprenant
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Créer des notifications automatiques basées sur les questionnaires et documents
        $this->createAutomaticNotifications($apprenant);

        // Récupérer toutes les notifications (y compris les nouvelles)
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notification_test', compact('notifications', 'apprenant'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Créer des notifications automatiques pour l'apprenant
     */
    private function createAutomaticNotifications($apprenant)
    {
        $user = $apprenant->utilisateur;
        $now = Carbon::now();

        // 1. Notifications pour les nouveaux questionnaires
        $recentQuestionnaires = Questionnaire::where('envoye', true)
            ->where('date_envoi', '<=', $now)
            ->where('date_envoi', '>=', $now->subDays(7)) // Questionnaires des 7 derniers jours
            ->whereHas('module', function($q) use ($apprenant) {
                $q->where('niveau_id', $apprenant->niveau_id);
            })
            ->whereIn('module_id', $apprenant->paiements()->where('statut', 'valide')->pluck('module_id'))
            ->get();

        foreach ($recentQuestionnaires as $questionnaire) {
            // Vérifier si une notification existe déjà
            $existingNotification = Notification::where('user_id', $user->id)
                ->where('type', 'questionnaire')
                ->where('data->questionnaire_id', $questionnaire->id)
                ->first();

            if (!$existingNotification) {
                Notification::createNotification([
                    'type' => 'questionnaire',
                    'title' => 'Nouveau questionnaire disponible',
                    'message' => "Un nouveau questionnaire '{$questionnaire->titre}' est disponible pour le module {$questionnaire->module->titre}",
                    'icon' => 'fas fa-question-circle',
                    'color' => 'success',
                    'user_id' => $user->id,
                    'data' => [
                        'questionnaire_id' => $questionnaire->id,
                        'module_id' => $questionnaire->module_id,
                        'niveau_id' => $questionnaire->niveau_id
                    ],
                    'action_url' => "/apprenants/questionnaires/{$questionnaire->id}/repondre"
                ]);
            }
        }

        // 2. Notifications pour les nouveaux documents
        $recentDocuments = Document::where('created_at', '>=', $now->subDays(7))
            ->whereHas('module', function($q) use ($apprenant) {
                $q->where('niveau_id', $apprenant->niveau_id);
            })
            ->whereIn('module_id', $apprenant->paiements()->where('statut', 'valide')->pluck('module_id'))
            ->get();

        foreach ($recentDocuments as $document) {
            // Vérifier si une notification existe déjà
            $existingNotification = Notification::where('user_id', $user->id)
                ->where('type', 'document')
                ->where('data->document_id', $document->id)
                ->first();

            if (!$existingNotification) {
                Notification::createNotification([
                    'type' => 'document',
                    'title' => 'Nouveau document disponible',
                    'message' => "Un nouveau document '{$document->titre}' est disponible pour le module {$document->module->titre}",
                    'icon' => 'fas fa-file-alt',
                    'color' => 'info',
                    'user_id' => $user->id,
                    'data' => [
                        'document_id' => $document->id,
                        'module_id' => $document->module_id,
                        'niveau_id' => $document->niveau_id
                    ],
                    'action_url' => "/documents/{$document->id}"
                ]);
            }
        }

        // 3. Notifications pour les questionnaires programmés (qui vont bientôt être disponibles)
        $upcomingQuestionnaires = Questionnaire::where('envoye', false)
            ->where('date_envoi', '>', $now)
            ->where('date_envoi', '<=', $now->addHours(24)) // Dans les 24 prochaines heures
            ->whereHas('module', function($q) use ($apprenant) {
                $q->where('niveau_id', $apprenant->niveau_id);
            })
            ->whereIn('module_id', $apprenant->paiements()->where('statut', 'valide')->pluck('module_id'))
            ->get();

        foreach ($upcomingQuestionnaires as $questionnaire) {
            $hoursUntilAvailable = $questionnaire->date_envoi->diffInHours($now);
            
            // Vérifier si une notification existe déjà
            $existingNotification = Notification::where('user_id', $user->id)
                ->where('type', 'questionnaire_upcoming')
                ->where('data->questionnaire_id', $questionnaire->id)
                ->first();

            if (!$existingNotification) {
                Notification::createNotification([
                    'type' => 'questionnaire_upcoming',
                    'title' => 'Questionnaire bientôt disponible',
                    'message' => "Le questionnaire '{$questionnaire->titre}' sera disponible dans {$hoursUntilAvailable} heure(s)",
                    'icon' => 'fas fa-clock',
                    'color' => 'warning',
                    'user_id' => $user->id,
                    'data' => [
                        'questionnaire_id' => $questionnaire->id,
                        'module_id' => $questionnaire->module_id,
                        'niveau_id' => $questionnaire->niveau_id,
                        'available_at' => $questionnaire->date_envoi
                    ],
                    'action_url' => "/questionnaire_test"
                ]);
            }
        }
    }

    /**
     * Obtenir les notifications via AJAX
     */
    public function getNotificationsAjax()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
} 
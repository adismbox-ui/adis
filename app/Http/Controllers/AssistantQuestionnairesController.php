<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class AssistantQuestionnairesController extends Controller
{
    public function index()
    {
        // Assurer l'envoi immédiat des éléments programmés arrivés à échéance
        try {
            Artisan::call('content:send-scheduled');
        } catch (\Exception $e) {
            // Ne pas bloquer l'affichage si l'exécution échoue
            \Log::error('content:send-scheduled depuis assistant.questionnaires.index a échoué: ' . $e->getMessage());
        }
        $questionnaires = \App\Models\Questionnaire::orderBy('created_at', 'desc')->get();
        return view('assistants.questionnaires.index', compact('questionnaires'));
    }

    public function create()
    {
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $modules = \App\Models\Module::with('niveau')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('assistants.questionnaires.create', compact('niveaux', 'modules', 'sessions'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_devoir' => 'required|in:hebdomadaire,mensuel,final',
            'semaine' => 'required|integer|min:1|max:12',
            'minutes' => 'required|integer|min:1|max:180',
            'module_id' => 'required|exists:modules,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'session_id' => 'required|exists:sessions_formation,id',
            'date_envoi' => 'required|string',
            'questions' => 'required|array|min:1',
            'questions.*.texte' => 'required|string',
            'questions.*.choix' => 'required|string',
            'questions.*.bonne_reponse' => 'required|string',
            'questions.*.points' => 'required|integer|min:1',
        ]);
        
        $user = auth()->user();
        
        // Vérifier l'unicité : un questionnaire par semaine et par module pour chaque utilisateur
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $existe = \App\Models\Questionnaire::where('user_id', $user ? $user->id : null)
            ->where('module_id', $request->module_id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->exists();
        if ($existe) {
            return back()->withErrors(['Vous avez déjà créé un questionnaire pour ce module cette semaine.'])->withInput();
        }
        
        // Utiliser la date d'envoi fournie par l'utilisateur
        // Normaliser à la timezone de l'app pour éviter les décalages
        $dateEnvoi = Carbon::parse($request->date_envoi, config('app.timezone'));
        
        $questionnaire = \App\Models\Questionnaire::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'type_devoir' => $request->type_devoir,
            'semaine' => $request->semaine,
            'minutes' => $request->minutes,
            'module_id' => $request->module_id,
            'niveau_id' => $request->niveau_id,
            'session_id' => $request->session_id,
            'date_envoi' => $dateEnvoi,
            'envoye' => false,
            'user_id' => $user ? $user->id : null,
        ]);
        
        // Créer les questions associées
        foreach ($request->questions as $questionData) {
            // Convertir les choix de string (séparés par ;) vers array
            $choix = array_map('trim', explode(';', $questionData['choix']));
            
            \App\Models\Question::create([
                'questionnaire_id' => $questionnaire->id,
                'texte' => $questionData['texte'],
                'choix' => $choix,
                'bonne_reponse' => $questionData['bonne_reponse'],
                'points' => $questionData['points'],
            ]);
        }
        
        // Envoi immédiat si la date/heure choisie est arrivée ou dépassée
        if ($dateEnvoi->lessThanOrEqualTo(Carbon::now(config('app.timezone')))) {
            try {
                // Récupérer les apprenants concernés (même logique que la commande d'envoi)
                $apprenantsQuery = \App\Models\Apprenant::with('utilisateur');
                if ($questionnaire->niveau_id) {
                    $apprenantsQuery->whereHas('inscriptions.module', function($q) use ($questionnaire) {
                        $q->where('niveau_id', $questionnaire->niveau_id);
                    });
                }
                if ($questionnaire->module_id) {
                    $apprenantsQuery->whereHas('inscriptions', function($q) use ($questionnaire) {
                        $q->where('module_id', $questionnaire->module_id);
                    });
                }
                if ($questionnaire->session_id) {
                    $apprenantsQuery->whereHas('inscriptions', function($q) use ($questionnaire) {
                        $q->where('session_formation_id', $questionnaire->session_id);
                    });
                }
                $apprenants = $apprenantsQuery->get();

                foreach ($apprenants as $apprenant) {
                    // Envoi email
                    \Mail::send('emails.questionnaire-notification', [
                        'apprenant' => $apprenant,
                        'questionnaire' => $questionnaire,
                        'url' => route('questionnaire.answer', $questionnaire->id)
                    ], function($message) use ($apprenant, $questionnaire) {
                        $message->to($apprenant->utilisateur->email, $apprenant->utilisateur->prenom . ' ' . $apprenant->utilisateur->nom)
                                ->subject("Nouveau questionnaire disponible : {$questionnaire->titre}");
                    });

                    // Notification DB
                    \App\Models\Notification::create([
                        'utilisateur_id' => $apprenant->utilisateur_id,
                        'titre' => "Nouveau questionnaire disponible : {$questionnaire->titre}",
                        'message' => "Nouveau questionnaire disponible : {$questionnaire->titre}",
                        'type' => 'questionnaire',
                        'lien' => route('questionnaire.answer', $questionnaire->id),
                        'lu' => false
                    ]);
                }

                // Marquer comme envoyé
                $questionnaire->update(['envoye' => true]);
            } catch (\Exception $e) {
                // Optionnel: logger l'erreur, mais ne pas bloquer la création
                \Log::error('Envoi immédiat questionnaire échoué: ' . $e->getMessage());
            }
        }

        // Notifier l'admin avec le nouveau système
        NotificationService::notifyAssistantAction(
            'questionnaire',
            'Nouveau questionnaire créé',
            "Un nouveau questionnaire a été créé : {$questionnaire->titre}",
            [
                'questionnaire_titre' => $questionnaire->titre,
                'questionnaire_id' => $questionnaire->id
            ]
        );
        
        return redirect()->route('assistant.questionnaires')->with('success', 'Questionnaire créé avec succès ! L\'admin a été notifié.');
    }
    
    /**
     * Calcule la date d'envoi automatique basée sur la session et la semaine
     */
    private function calculateEnvoiDate($sessionId, $semaine)
    {
        $session = \App\Models\SessionFormation::find($sessionId);
        if (!$session) {
            throw new \Exception('Session non trouvée');
        }
        
        // Calculer le premier dimanche après la date de début
        $debut = \Carbon\Carbon::parse($session->date_debut);
        $premierDimanche = $this->getNextSunday($debut);
        
        // Ajouter (semaine - 1) * 7 jours pour obtenir le dimanche de la semaine demandée
        $dateEnvoi = $premierDimanche->copy()->addDays(($semaine - 1) * 7);
        
        // Définir l'heure à 13h00 (dimanche soir)
        $dateEnvoi->setTime(13, 0, 0);
        
        return $dateEnvoi;
    }
    
    /**
     * Trouve le prochain dimanche après une date donnée
     */
    private function getNextSunday($date)
    {
        $day = $date->dayOfWeek;
        $daysUntilSunday = (7 - $day) % 7;
        return $date->copy()->addDays($daysUntilSunday);
    }

    public function show($id)
    {
        $questionnaire = \App\Models\Questionnaire::with('questions', 'module.niveau')->findOrFail($id);
        return view('assistants.questionnaires.show', compact('questionnaire'));
    }

    public function edit($id)
    {
        $questionnaire = \App\Models\Questionnaire::findOrFail($id);
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $modules = \App\Models\Module::with('niveau')->get();
        return view('assistants.questionnaires.edit', compact('questionnaire', 'niveaux', 'modules'));
    }

    public function update(Request $request, $id)
    {
        $questionnaire = \App\Models\Questionnaire::findOrFail($id);
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $questionnaire->update([
            'titre' => $request->titre,
            'description' => $request->description,
        ]);
        return redirect()->route('assistant.questionnaires')->with('success', 'Questionnaire mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $questionnaire = \App\Models\Questionnaire::findOrFail($id);
        $questionnaire->delete();
        return redirect()->route('assistant.questionnaires')->with('success', 'Questionnaire supprimé avec succès.');
    }
} 
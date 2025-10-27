<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Carbon\Carbon;
use App\Services\ScheduledContentService;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Affichage côté apprenant : questionnaires liés à ses modules validés ou payés
    public function apprenantIndex()
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        $moduleIds = [];
        if ($apprenant) {
            $modulesInscrits = $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray();
            $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
            $moduleIds = collect($modulesInscrits)->merge($modulesPayes)->unique()->toArray();
        }
        $questionnaires = \App\Models\Questionnaire::with(['questions', 'module'])
            ->whereIn('module_id', $moduleIds)
            ->get();
        return view('apprenants.questionnaires', compact('questionnaires'));
    }

    /**
     * Affiche la page de test des questionnaires pour les apprenants
     */
    public function apprenantTest()
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        $moduleIds = [];
        $inscritModuleIds = [];
        if ($apprenant) {
            // Modules payés uniquement
            $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
            $moduleIds = array_unique($modulesPayes);
            $inscritModuleIds = $moduleIds;
        }
        // Si aucun module payé, pas de questionnaire
        $questionnaires = collect();
        if (!empty($moduleIds) && $apprenant && $apprenant->niveau_id) {
            $questionnaires = \App\Models\Questionnaire::with(['module.niveau'])
                ->whereIn('module_id', $moduleIds)
                ->whereHas('module', function($q) use ($apprenant) {
                    $q->where('niveau_id', $apprenant->niveau_id);
                })
                ->where('envoye', true) // Seulement les questionnaires envoyés
                ->where('date_envoi', '<=', \Carbon\Carbon::now()) // Seulement ceux dont la date d'envoi est atteinte
                ->get();
        }
        $debug = null; // plus de debug
        return view('questionnaire_test', compact('questionnaires', 'inscritModuleIds', 'debug'));
    }

    /**
     * Affiche le formulaire pour répondre à un questionnaire (avec timer).
     */
    public function showForApprenant(Questionnaire $questionnaire)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return redirect()->route('login')->withErrors(['Accès réservé aux apprenants.']);
        }
        
        // Vérifier que l'apprenant a accès à ce questionnaire
        $moduleIds = [];
        $modulesInscrits = $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray();
        $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
        $moduleIds = collect($modulesInscrits)->merge($modulesPayes)->unique()->toArray();
        
        if (!in_array($questionnaire->module_id, $moduleIds)) {
            return redirect()->route('apprenants.questionnaire_test')->withErrors(['Vous n\'avez pas accès à ce questionnaire.']);
        }
        
        // Vérifier si l'apprenant a déjà répondu à ce questionnaire
        $sessionKey = "questionnaire_{$questionnaire->id}_completed_{$apprenant->id}";
        if (session($sessionKey)) {
            return redirect()->route('apprenants.questionnaire_test')->withErrors(['Vous avez déjà répondu à ce questionnaire.']);
        }
        
        $questionnaire->load('questions');
        return view('apprenants.questionnaire-answer', compact('questionnaire'));
    }

    // Soumission des réponses à un questionnaire
    public function repondre(Request $request, \App\Models\Questionnaire $questionnaire)
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        
        if (!$apprenant) {
            return redirect()->route('login')->withErrors(['Accès réservé aux apprenants.']);
        }
        
        // Vérifier si l'apprenant a déjà répondu à ce questionnaire
        $sessionKey = "questionnaire_{$questionnaire->id}_completed_{$apprenant->id}";
        if (session($sessionKey)) {
            return redirect()->route('apprenants.questionnaire_test')->withErrors(['Vous avez déjà répondu à ce questionnaire.']);
        }
        
        $data = $request->validate([
            'reponses' => 'required|array',
        ]);
        
        $questions = $questionnaire->questions;
        $incorrects = [];
        $score = 0;
        $totalQuestions = $questions->count();
        $totalPoints = 0;
        $earnedPoints = 0;
        
        foreach ($questions as $question) {
            $reponse = $data['reponses'][$question->id] ?? null;
            $questionPoints = $question->points ?? 1;
            $totalPoints += $questionPoints;
            
            if ($reponse === $question->bonne_reponse) {
                $score++;
                $earnedPoints += $questionPoints;
            } else {
                $incorrects[] = [
                    'texte' => $question->texte,
                    'bonne_reponse' => $question->bonne_reponse,
                    'votre_reponse' => $reponse,
                    'points' => $questionPoints
                ];
            }
            \App\Models\ReponseQuestionnaire::updateOrCreate([
                'apprenant_id' => $apprenant->id,
                'question_id' => $question->id,
            ], [
                'reponse' => $reponse,
            ]);
        }
        
        // Marquer le questionnaire comme complété
        session([$sessionKey => true]);
        
        $percentage = ($score / $totalQuestions) * 100;
        $pointsPercentage = ($earnedPoints / $totalPoints) * 100;
        
        if (count($incorrects) === 0) {
            return redirect()->route('apprenants.questionnaire_test')->with('success', "🎉 Bravo ! Vous avez obtenu {$score}/{$totalQuestions} ({$percentage}%) - {$earnedPoints}/{$totalPoints} points ({$pointsPercentage}%) - Toutes vos réponses sont correctes !");
        } else {
            return redirect()->route('apprenants.questionnaire_test')->with([
                'error' => "📊 Résultat : {$score}/{$totalQuestions} ({$percentage}%) - {$earnedPoints}/{$totalPoints} points ({$pointsPercentage}%) - Certaines réponses sont incorrectes.",
                'incorrects' => $incorrects
            ]);
        }
    }

    /**
     * Display a listing of the resource (admin/gestion).
     */
    public function index()
    {
        $service = new ScheduledContentService();
        $questionnairesEnvoyes = $service->sendScheduledQuestionnaires();
        $documentsEnvoyes = $service->sendScheduledDocuments();
        $message = null;
        if (count($questionnairesEnvoyes) > 0 || count($documentsEnvoyes) > 0) {
            $message = 'Envois automatiques : ';
            if (count($questionnairesEnvoyes) > 0) {
                $message .= count($questionnairesEnvoyes) . ' questionnaire(s) envoyés : ' . implode(', ', $questionnairesEnvoyes) . '. ';
            }
            if (count($documentsEnvoyes) > 0) {
                $message .= count($documentsEnvoyes) . ' document(s) envoyés : ' . implode(', ', $documentsEnvoyes) . '.';
            }
        }
        $questionnaires = \App\Models\Questionnaire::with(['module.niveau'])->get();
        return view('questionnaires.index', compact('questionnaires'))->with('auto_send_message', $message);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $modules = \App\Models\Module::with('niveau')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('questionnaires.create', compact('niveaux', 'modules', 'sessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'required|exists:modules,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'session_id' => 'required|exists:sessions_formation,id',
            'date_envoi' => 'required|string',
            'minutes' => 'required|integer|min:1|max:180',
            'semaine' => 'required|integer|min:1|max:12',
            'type_devoir' => 'required|in:hebdomadaire,mensuel,final',
            'questions' => 'required|array|min:1',
            'questions.*.texte' => 'required|string',
            'questions.*.choix' => 'required|array|min:2',
            'questions.*.bonne_reponse' => 'required|string',
            'questions.*.points' => 'required|integer|min:1|max:100',
        ]);

        // Validation du nombre minimum de questions selon le type
        $questionCount = count($data['questions']);
        $minQuestions = [
            'hebdomadaire' => 2,
            'mensuel' => 8,
            'final' => 66
        ];

        if ($questionCount < $minQuestions[$data['type_devoir']]) {
            return back()->withErrors([
                'questions' => "Le devoir {$data['type_devoir']} nécessite au minimum {$minQuestions[$data['type_devoir']]} questions. Vous en avez ajouté {$questionCount}."
            ])->withInput();
        }

        // Vérification : un questionnaire par semaine par formateur et par module
        $user = auth()->user();
        if (!$user) {
            return back()->withErrors(['Vous devez être connecté pour créer un questionnaire.']);
        }
        $moduleId = $data['module_id'];
        $now = \Carbon\Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $existe = Questionnaire::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->exists();
        if ($existe) {
            return back()->withErrors(['Vous avez déjà créé un questionnaire pour ce module cette semaine.'])->withInput();
        }

        // Utiliser la date d'envoi fournie par l'utilisateur
        try {
            $dateEnvoi = Carbon::parse($data['date_envoi']);
        } catch (\Exception $e) {
            return back()->withErrors(['La date d\'envoi n\'est pas valide. Veuillez sélectionner une date et heure valides.'])->withInput();
        }
        
        // Vérifier que la date d'envoi n'est pas vide
        if (empty($data['date_envoi'])) {
            return back()->withErrors(['La date d\'envoi est obligatoire. Veuillez sélectionner une date et heure d\'envoi.'])->withInput();
        }
        
        $questionnaire = Questionnaire::create([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'module_id' => $data['module_id'],
            'niveau_id' => $data['niveau_id'],
            'session_id' => $data['session_id'],
            'date_envoi' => $dateEnvoi,
            'envoye' => false,
            'minutes' => $data['minutes'],
            'semaine' => $data['semaine'],
            'type_devoir' => $data['type_devoir'],
            'user_id' => $user->id, // Associer le formateur créateur
        ]);
        
        // Envoyer immédiatement si la date d'envoi est atteinte ou dépassée
        if ($dateEnvoi <= Carbon::now()) {
            $this->sendQuestionnaireImmediately($questionnaire);
            $questionnaire->update(['envoye' => true]);
        }
        // Si la date est dans le futur, le questionnaire sera envoyé automatiquement par le système de surveillance

        if (!empty($data['questions'])) {
            foreach ($data['questions'] as $q) {
                $questionnaire->questions()->create([
                    'texte' => $q['texte'],
                    'choix' => $q['choix'], // Le cast 'array' dans le modèle s'occupera de l'encodage
                    'bonne_reponse' => $q['bonne_reponse'],
                    'points' => $q['points'],
                ]);
            }
        }

        $message = $dateEnvoi <= Carbon::now() 
            ? 'Questionnaire envoyé immédiatement avec succès !'
            : 'Questionnaire programmé avec succès ! Il sera envoyé automatiquement à la date et heure spécifiées.';
            
        return redirect()->route('questionnaires.index')->with('success', $message);
    }
    
    /**
     * Envoie immédiatement un questionnaire aux apprenants
     */
    private function sendQuestionnaireImmediately($questionnaire)
    {
        // Récupérer les apprenants concernés
        $apprenants = $this->getApprenantsForQuestionnaire($questionnaire);
        
        foreach ($apprenants as $apprenant) {
            try {
                // Envoyer l'email de notification
                $this->sendQuestionnaireEmail($apprenant, $questionnaire);
                
                // Créer une notification en base de données
                $this->createNotification($apprenant, 'questionnaire', $questionnaire);
                
            } catch (\Exception $e) {
                \Log::error("Erreur envoi questionnaire à apprenant {$apprenant->id}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Récupère les apprenants concernés par le questionnaire
     */
    private function getApprenantsForQuestionnaire($questionnaire)
    {
        $query = \App\Models\Apprenant::with('utilisateur');
        
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
    private function sendQuestionnaireEmail($apprenant, $questionnaire)
    {
        $data = [
            'apprenant' => $apprenant,
            'questionnaire' => $questionnaire,
            'url' => route('questionnaire.answer', $questionnaire->id)
        ];
        
        \Illuminate\Support\Facades\Mail::send('emails.questionnaire-notification', $data, function($message) use ($apprenant, $questionnaire) {
            $message->to($apprenant->utilisateur->email, $apprenant->utilisateur->prenom . ' ' . $apprenant->utilisateur->nom)
                    ->subject("Nouveau questionnaire disponible : {$questionnaire->titre}");
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

    /**
     * Display the specified resource.
     */
    public function show(Questionnaire $questionnaire)
    {
        $questionnaire->load('questions');
        return view('questionnaires.show', compact('questionnaire'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Questionnaire $questionnaire)
    {
        return view('questionnaires.edit', compact('questionnaire'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Questionnaire $questionnaire)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $questionnaire->update($data);
        return redirect()->route('questionnaires.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Questionnaire $questionnaire)
    {
        $questionnaire->delete();
        return redirect()->route('questionnaires.index');
    }
}

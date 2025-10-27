<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Apprenant;
use App\Models\Formateur;
use App\Models\SessionFormation;
use App\Models\Inscription;
use App\Models\Module;
use App\Services\NotificationService;

class AssistantController extends Controller
{
    /**
     * Affiche le dashboard de l'assistant
     */
    public function dashboard()
    {
        // Vérifier que l'utilisateur est bien un assistant ou un formateur-assistant
        $user = Auth::user();
        $isAssistant = $user->type_compte === 'assistant' || $user->assistant;
        
        if (!$isAssistant) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        // Statistiques générales
        $totalApprenants = Apprenant::count();
        $totalFormateurs = Formateur::count();
        $totalModules = Module::count();
        $totalInscriptions = Inscription::count();
        
        // Inscriptions récentes (dernières 7 jours)
        $inscriptionsRecentes = Inscription::with(['apprenant', 'module'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Sessions de formation à venir
        $sessionsAVenir = SessionFormation::with(['modules', 'formateur'])
            ->where('date_debut', '>=', now())
            ->orderBy('date_debut', 'asc')
            ->limit(5)
            ->get();
        
        // Apprenants récemment inscrits
        $apprenantsRecents = Apprenant::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('assistants.dashboard', compact(
            'totalApprenants',
            'totalFormateurs', 
            'totalModules',
            'totalInscriptions',
            'inscriptionsRecentes',
            'sessionsAVenir',
            'apprenantsRecents'
        ));
    }

    /**
     * Affiche la liste des assistants (pour l'admin)
     */
    public function index()
    {
        $assistants = User::where('type_compte', 'assistant')->get();
        return view('assistants.index', compact('assistants'));
    }

    /**
     * Affiche le formulaire de création d'un assistant
     */
    public function create()
    {
        return view('assistants.create');
    }

    /**
     * Enregistre un nouvel assistant
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
            'type_compte' => 'assistant',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.assistants.index')
            ->with('success', 'Assistant créé avec succès.');
    }

    /**
     * Affiche la liste des apprenants pour l'assistant
     */
    public function apprenants()
    {
        $user = Auth::user();
        $isAssistant = $user->type_compte === 'assistant' || $user->assistant;
        
        if (!$isAssistant) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }
        $apprenants = \App\Models\Apprenant::with('utilisateur')->get();
        return view('assistants.apprenants', compact('apprenants'));
    }

    /**
     * Affiche la liste des formateurs pour l'assistant
     */
    public function formateurs()
    {
        $user = Auth::user();
        $isAssistant = $user->type_compte === 'assistant' || $user->assistant;
        
        if (!$isAssistant) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }
        $formateurs = \App\Models\Formateur::with('utilisateur')->get();
        return view('assistants.formateurs', compact('formateurs'));
    }

    /**
     * Affiche la liste des modules pour l'assistant
     */
    public function modules()
    {
        $user = Auth::user();
        $isAssistant = $user->type_compte === 'assistant' || $user->assistant;
        
        if (!$isAssistant) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }
        $modules = \App\Models\Module::with(['niveau', 'formateur.utilisateur'])->orderBy('created_at', 'desc')->get();
        return view('assistants.modules', compact('modules'));
    }

    /**
     * Affiche la liste des inscriptions pour l'assistant
     */
    public function inscriptions()
    {
        $user = Auth::user();
        $isAssistant = $user->type_compte === 'assistant' || $user->assistant;
        
        if (!$isAssistant) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }
        // Même logique que la page admin: lister les apprenants n'ayant pas encore payé des modules de leur niveau
        $apprenantsNonPayes = \App\Models\Apprenant::with(['utilisateur', 'niveau', 'inscriptions.module', 'paiements'])
            ->whereNotNull('niveau_id')
            ->whereDoesntHave('paiements', function($query) {
                $query->where('statut', 'valide');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('assistants.inscriptions.index', compact('apprenantsNonPayes'));
    }

    /**
     * Affiche la liste des documents pour l'assistant
     */
    public function documents()
    {
        $user = Auth::user();
        $isAssistant = $user->type_compte === 'assistant' || $user->assistant;
        
        if (!$isAssistant) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }
        // Déclencher l'envoi des contenus programmés arrivés à échéance
        try {
            \Artisan::call('content:send-scheduled');
        } catch (\Exception $e) {
            \Log::error('content:send-scheduled depuis assistant.documents a échoué: ' . $e->getMessage());
        }
        $documents = \App\Models\Document::with(['formateur.utilisateur', 'module'])->orderBy('created_at', 'desc')->get();
        return view('assistants.documents', compact('documents'));
    }

    /**
     * Affiche le formulaire de création de document pour l'assistant
     */
    public function createDocument()
    {
        $modules = \App\Models\Module::with('niveau')->orderBy('titre')->get();
        $niveaux = \App\Models\Niveau::orderBy('nom')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('assistants.documents_create', compact('modules', 'niveaux', 'sessions'));
    }

    /**
     * Enregistre un document créé par l'assistant et notifie l'admin
     */
    public function storeDocument(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'fichier' => 'required|file|mimes:pdf',
            'module_id' => 'nullable|exists:modules,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'semaine' => 'required|integer|min:1|max:12',
            'session_id' => 'required|exists:sessions_formation,id',
            'date_envoi' => 'required|date',
        ]);

        // Gestion de l’upload du fichier
        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        // Gestion de l’upload du fichier audio (optionnel)
        if ($request->hasFile('audio')) {
            $data['audio'] = $request->file('audio')->store('audios', 'public');
        }

        // Associer le formateur connecté si disponible
        if (auth()->check() && auth()->user()->type_compte === 'formateur') {
            $formateur = \App\Models\Formateur::where('utilisateur_id', auth()->user()->id)->first();
            $data['formateur_id'] = $formateur ? $formateur->id : null;
        }

        // Marquer comme créé par l'assistant
        $data['created_by_admin'] = false;
        $data['semaine'] = $request->input('semaine');

        // Normaliser la date d'envoi à la timezone applicative
        try {
            $data['date_envoi'] = \Carbon\Carbon::parse($request->input('date_envoi'), config('app.timezone'));
        } catch (\Exception $e) {
            // En cas d'échec de parsing, garder la valeur brute
            \Log::warning('Parsing date_envoi échoué, valeur brute utilisée: ' . $request->input('date_envoi'));
        }

        // Initialiser le statut d'envoi
        $data['envoye'] = false;
        $document = \App\Models\Document::create($data);

        // Envoi immédiat si la date/heure choisie est arrivée ou dépassée
        try {
            $dateEnvoi = \Carbon\Carbon::parse($request->input('date_envoi'));
            if ($dateEnvoi->lessThanOrEqualTo(\Carbon\Carbon::now())) {
                // Récupérer les apprenants concernés
                $apprenantsQuery = \App\Models\Apprenant::with('utilisateur');
                if ($document->niveau_id) {
                    $apprenantsQuery->whereHas('inscriptions.module', function($q) use ($document) {
                        $q->where('niveau_id', $document->niveau_id);
                    });
                }
                if ($document->module_id) {
                    $apprenantsQuery->whereHas('inscriptions', function($q) use ($document) {
                        $q->where('module_id', $document->module_id);
                    });
                }
                if ($document->session_id) {
                    $apprenantsQuery->whereHas('inscriptions', function($q) use ($document) {
                        $q->where('session_formation_id', $document->session_id);
                    });
                }
                $apprenants = $apprenantsQuery->get();

                foreach ($apprenants as $apprenant) {
                    // Email
                    \Mail::send('emails.document-notification', [
                        'apprenant' => $apprenant,
                        'document' => $document,
                        'url' => route('documents.show', $document->id)
                    ], function($message) use ($apprenant, $document) {
                        $message->to($apprenant->utilisateur->email, $apprenant->utilisateur->prenom . ' ' . $apprenant->utilisateur->nom)
                                ->subject("Nouveau document disponible : {$document->titre}");
                    });

                    // Notification DB
                    \App\Models\Notification::create([
                        'utilisateur_id' => $apprenant->utilisateur_id,
                        'titre' => "Nouveau document disponible : {$document->titre}",
                        'message' => "Nouveau document disponible : {$document->titre}",
                        'type' => 'document',
                        'lien' => route('documents.show', $document->id),
                        'lu' => false
                    ]);
                }

                // Marquer comme envoyé
                $document->update(['envoye' => true]);
            }
        } catch (\Exception $e) {
            \Log::error('Envoi immédiat document échoué: ' . $e->getMessage());
        }

        // Notifier l'admin avec le nouveau système
        NotificationService::notifyAssistantAction(
            'document',
            'Nouveau document créé',
            "Un nouveau document a été créé par l'assistant : {$document->titre}",
            [
                'document_title' => $document->titre,
                'document_id' => $document->id,
                'module_id' => $document->module_id,
                'niveau_id' => $document->niveau_id
            ]
        );

        return redirect()->route('assistant.documents')->with('success', 'Document créé et notification envoyée à l\'admin.');
    }
}

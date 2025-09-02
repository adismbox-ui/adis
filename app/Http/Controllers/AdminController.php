<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Formateur;
use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Certificat;
use App\Models\Document;
use App\Models\Niveau;
use App\Models\SessionFormation;
use App\Models\Vacance;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Vérifie si l'utilisateur connecté est admin
     */
    private function checkAdmin()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['Vous devez être connecté pour accéder à cette page.']);
        }
        
        $user = Auth::user();
        if ($user->type_compte !== 'admin') {
            // Redirection selon le type de compte
            switch ($user->type_compte) {
                case 'apprenant':
                    return redirect()->route('apprenants.dashboard');
                case 'formateur':
                    return redirect()->route('formateurs.dashboard');
                case 'assistant':
                    return redirect()->route('assistant.dashboard');
                default:
                    return redirect('/');
            }
        }
        
        return null; // Pas d'erreur
    }

    // Dashboard admin : liste utilisateurs et formateurs à valider
    public function dashboard()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        // Statistiques générales
        $totalUtilisateurs = Utilisateur::count();
        $totalApprenants = Apprenant::count();
        $totalFormateurs = Formateur::count();
        $totalAssistants = Utilisateur::where('type_compte', 'assistant')->count();
        $totalModules = Module::count();
        $totalInscriptions = Inscription::count();
        $totalPaiements = Paiement::count();
        $totalCertificats = Certificat::count();
        $totalDocuments = Document::count();
        
        // Nouvelles statistiques formations
        $totalNiveaux = Niveau::where('actif', true)->count();
        $totalSessions = SessionFormation::where('actif', true)->count();
        $sessionsEnCours = SessionFormation::where('actif', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->count();
        $totalVacances = Vacance::where('actif', true)->count();
        $totalQuestionnaires = Questionnaire::count();

        // Formateurs à valider
        $formateursAValider = Formateur::with('utilisateur')->where('valide', false)->get();
        
        // Dernières inscriptions
        $dernieresInscriptions = Inscription::with(['apprenant.utilisateur', 'module'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Demandes de paiement en attente
        $demandesPaiementEnAttente = Paiement::with(['apprenant.utilisateur', 'module'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();

        // Derniers paiements
        $derniersPaiements = Paiement::with(['apprenant.utilisateur', 'module'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Modules les plus populaires
        $modulesPopulaires = Module::withCount('inscriptions')
            ->orderBy('inscriptions_count', 'desc')
            ->limit(5)
            ->get();

        // Utilisateurs récents
        $utilisateursRecents = Utilisateur::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistiques par type de compte
        $statsParType = [
            'admin' => Utilisateur::where('type_compte', 'admin')->count(),
            'formateur' => Utilisateur::where('type_compte', 'formateur')->count(),
            'apprenant' => Utilisateur::where('type_compte', 'apprenant')->count(),
            'assistant' => Utilisateur::where('type_compte', 'assistant')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalUtilisateurs',
            'totalApprenants', 
            'totalFormateurs',
            'totalAssistants',
            'totalModules',
            'totalNiveaux',
            'totalSessions',
            'sessionsEnCours',
            'totalVacances',
            'totalInscriptions',
            'totalPaiements',
            'totalCertificats',
            'totalDocuments',
            'totalQuestionnaires',
            'formateursAValider',
            'dernieresInscriptions',
            'demandesPaiementEnAttente',
            'derniersPaiements',
            'modulesPopulaires',
            'utilisateursRecents',
            'statsParType'
        ));
    }

    // Affiche le formulaire d'inscription rempli par le formateur
    public function showFormateur($id)
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $formateur = Formateur::with('utilisateur')->findOrFail($id);
        return view('admin.formateur_show', compact('formateur'));
    }

    // Valide le formateur et envoie un email
    public function validerFormateur($id)
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $formateur = Formateur::with('utilisateur')->findOrFail($id);
        $formateur->valide = true;
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        $formateur->validation_token = $token;
        $formateur->save();
        // Envoi d'un email au formateur validé
        $utilisateur = $formateur->utilisateur;
        if ($utilisateur) {
            // Activer le compte utilisateur du formateur à la validation admin
            $utilisateur->actif = true;
            $utilisateur->save();
            $autoLoginUrl = url('/formateur/auto-login/' . $token);
            Mail::raw(
                "Bonjour " . $utilisateur->prenom . ",\n\nVotre compte formateur a été validé par l'administrateur.\nCliquez sur ce lien pour accéder directement à votre espace formateur :\n" . $autoLoginUrl . "\n\nCe lien est à usage unique.\n\nL'équipe ADIS.",
                function ($message) use ($utilisateur) {
                    $message->to($utilisateur->email)
                            ->subject('Votre compte formateur a été validé !');
                }
            );
        }
        return redirect()->route('admin.dashboard')->with('success', 'Formateur validé et email envoyé.');
    }

    // Refuse le formateur
    public function refuserFormateur($id)
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        $formateur = Formateur::with('utilisateur')->findOrFail($id);
        $formateur->valide = false;
        $formateur->save();
        // Optionnel : envoyer un email de refus au formateur
        $utilisateur = $formateur->utilisateur;
        if ($utilisateur) {
            Mail::raw(
                "Bonjour " . $utilisateur->prenom . ",\n\nVotre demande d'inscription en tant que formateur a été refusée.\n\nL'équipe ADIS.",
                function ($message) use ($utilisateur) {
                    $message->to($utilisateur->email)
                        ->subject('Demande Formateur refusée');
                }
            );
        }
        return redirect()->route('admin.dashboard')->with('success', 'Formateur refusé.');
    }

    // Gestion des utilisateurs
    public function utilisateurs()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $utilisateurs = Utilisateur::with(['apprenant', 'formateur'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('utilisateurs.index', compact('utilisateurs'));
    }

    // Gestion des modules
    public function modules()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $modules = Module::with(['formateur.utilisateur'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.modules.index', compact('modules'));
    }

    // Afficher un module (admin)
    public function showModule(Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $module->load('formateur.utilisateur');
        return view('admin.modules.show', compact('module'));
    }

    // Editer un module (admin)
    public function editModule(Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $formateurs = Formateur::with('utilisateur')->where('valide', true)->get();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('admin.modules.edit', compact('module', 'formateurs', 'niveaux'));
    }

    // Mettre à jour un module (admin)
    public function updateModule(Request $request, Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'discipline' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'formateur_id' => 'nullable|exists:formateurs,id',
            'lien' => 'nullable|string|max:255',
            'support' => 'nullable|file|mimes:pdf|max:10240',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a|max:20480',
            'prix' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'certificat' => 'nullable|boolean',
            'niveau_id' => 'nullable|exists:niveaux,id',
        ]);
        if ($request->hasFile('support')) {
            $data['support'] = $request->file('support')->store('supports', 'public');
        }
        if ($request->hasFile('audio')) {
            $data['audio'] = $request->file('audio')->store('audios', 'public');
        }
        if (array_key_exists('niveau_id', $data)) {
            $module->niveau_id = $data['niveau_id'];
        }
        $module->update($data);
        return redirect()->route('admin.modules')->with('success', 'Module modifié avec succès');
    }

    // Supprimer un module (admin)
    public function destroyModule(Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $module->delete();
        return redirect()->route('admin.modules')->with('success', 'Module supprimé avec succès');
    }

    // Gestion des préinscriptions (apprenants qui n'ont pas encore payé)
    public function inscriptions()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        // Récupérer les apprenants qui n'ont pas payé de modules de leur niveau actuel
        $apprenantsNonPayes = \App\Models\Apprenant::with(['utilisateur', 'niveau', 'inscriptions.module', 'paiements'])
            ->whereNotNull('niveau_id') // Seulement les apprenants qui ont un niveau assigné
            ->whereDoesntHave('paiements', function($query) {
                $query->where('statut', 'valide');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.inscriptions.index', compact('apprenantsNonPayes'));
    }

    // Gestion des paiements
    public function paiements(Request $request)
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        $query = Paiement::with(['apprenant.utilisateur', 'module'])
            ->orderBy('created_at', 'desc');

        // Filtre par statut (ex: 'valide' pour effectués)
        $statut = $request->input('statut');
        if (!empty($statut)) {
            $query->where('statut', $statut);
        }

        // Recherche texte sur apprenant (nom, prénom, email) et module (titre)
        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('apprenant.utilisateur', function ($q2) use ($search) {
                    $q2->where('nom', 'like', "%$search%")
                       ->orWhere('prenom', 'like', "%$search%")
                       ->orWhere('email', 'like', "%$search%");
                })
                ->orWhereHas('module', function ($q3) use ($search) {
                    $q3->where('titre', 'like', "%$search%");
                })
                ->orWhere('methode_paiement', 'like', "%$search%");
            });
        }

        $paiements = $query->paginate(20)->appends($request->query());

        return view('admin.paiements.index', compact('paiements', 'statut', 'search'));
    }

    /**
     * Liste des demandes de paiement en attente de validation
     */
    public function demandesPaiementEnAttente()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $paiements = Paiement::with(['apprenant.utilisateur', 'module'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.paiements.en_attente', compact('paiements'));
    }

    /**
     * Valider un paiement
     */
    public function validerPaiement($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $paiement = Paiement::with(['apprenant.utilisateur', 'module'])->findOrFail($id);
        $paiement->statut = 'valide';
        $paiement->save();
        
        // Créer automatiquement l'inscription au module
        $inscription = Inscription::create([
            'apprenant_id' => $paiement->apprenant_id,
            'module_id' => $paiement->module_id,
            'date_inscription' => now(),
            'statut' => 'valide',
        ]);
        
        return redirect()->back()->with('success', 'Paiement validé et inscription créée avec succès.');
    }

    /**
     * Refuser un paiement
     */
    public function refuserPaiement($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $paiement = Paiement::findOrFail($id);
        $paiement->statut = 'refuse';
        $paiement->save();
        
        return redirect()->back()->with('success', 'Paiement refusé avec succès.');
    }

    /**
     * Liste des inscriptions en attente de validation
     */
    public function inscriptionsEnAttente()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscriptions = \App\Models\Inscription::with(['apprenant.utilisateur', 'module'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.inscriptions_en_attente', compact('inscriptions'));
    }

    /**
     * Valider une inscription (paiement)
     */
    public function validerInscription($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription = \App\Models\Inscription::findOrFail($id);
        $inscription->statut = 'valide';
        $inscription->save();
        // (Optionnel) Notifier l'apprenant ici
        return redirect()->back()->with('success', 'Inscription validée avec succès.');
    }

    // Liste des assistants
    public function assistants()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        // Récupérer tous les assistants actifs (utilisateurs avec type_compte = 'assistant' OU utilisateurs ayant un enregistrement actif dans la table assistants)
        $assistantsFromTable = \App\Models\Assistant::with('utilisateur', 'formateur.utilisateur')
            ->where('actif', true) // Seulement les assistants actifs
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($assistant) {
                return $assistant->utilisateur;
            });
            
        // Ajouter aussi les utilisateurs avec type_compte = 'assistant' qui ne sont pas dans la table assistants
        $assistantsDirect = Utilisateur::where('type_compte', 'assistant')
            ->whereNotIn('id', $assistantsFromTable->pluck('id'))
            ->get();
            
        $assistants = $assistantsFromTable->merge($assistantsDirect)->sortByDesc('created_at');
        
        return view('assistants.index', compact('assistants'));
    }

    // Activer/Désactiver un assistant
    public function toggleAssistantActivation($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        // Chercher d'abord dans la table assistants
        $assistantRecord = \App\Models\Assistant::where('utilisateur_id', $id)->first();
        if ($assistantRecord) {
            $assistantRecord->actif = !$assistantRecord->actif;
            $assistantRecord->save();
            $etat = $assistantRecord->actif ? 'activé' : 'désactivé';
            return back()->with('success', "Assistant $etat avec succès.");
        }
        
        // Sinon, chercher dans les utilisateurs avec type_compte = 'assistant'
        $assistant = Utilisateur::where('type_compte', 'assistant')->findOrFail($id);
        $assistant->actif = !$assistant->actif;
        $assistant->save();
        $etat = $assistant->actif ? 'activé' : 'désactivé';
        return back()->with('success', "Assistant $etat avec succès.");
    }

    // Formulaire de création d'assistant
    public function createAssistant()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        return view('assistants.create');
    }

    // Enregistrement d'un assistant
    public function storeAssistant(Request $request)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:Homme,Femme',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:utilisateurs,email',
            'mot_de_passe' => 'required|string|min:6',
        ]);
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        $data['type_compte'] = 'assistant';
        $data['actif'] = true;
        $data['email_verified_at'] = now();
        $utilisateur = Utilisateur::create($data);
        
        // Créer l'enregistrement dans la table assistants
        \App\Models\Assistant::create([
            'utilisateur_id' => $utilisateur->id,
            'formateur_id' => null,
            'actif' => true,
        ]);
        
        return redirect()->route('admin.assistants')->with('success', 'Assistant ajouté avec succès!');
    }

    // Afficher un assistant
    public function showAssistant($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $assistant = Utilisateur::findOrFail($id);
        return view('assistants.show', compact('assistant'));
    }

    // Editer un assistant
    public function editAssistant($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $assistant = Utilisateur::findOrFail($id);
        $formateurs = Formateur::with('utilisateur')->where('valide', true)->get();
        return view('assistants.edit', compact('assistant', 'formateurs'));
    }

    // Mettre à jour un assistant
    public function updateAssistant(Request $request, $id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $assistant = Utilisateur::findOrFail($id);
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email,' . $assistant->id,
            'telephone' => 'nullable|string|max:20',
            'formateur_id' => 'nullable|exists:formateurs,id',
        ]);
        
        $assistant->update($data);
        
        // Mettre à jour l'enregistrement assistant si il existe
        $assistantRecord = \App\Models\Assistant::where('utilisateur_id', $assistant->id)->first();
        if ($assistantRecord) {
            $assistantRecord->update([
                'formateur_id' => $data['formateur_id'] ?? null,
            ]);
        }
        
        return redirect()->route('admin.assistants')->with('success', 'Assistant mis à jour avec succès!');
    }

    // Supprimer un assistant
    public function destroyAssistant($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $assistant = Utilisateur::findOrFail($id);
        
        // Supprimer l'enregistrement assistant si il existe
        \App\Models\Assistant::where('utilisateur_id', $assistant->id)->delete();
        
        // Supprimer l'utilisateur
        $assistant->delete();
        
        return redirect()->route('admin.assistants')->with('success', 'Assistant supprimé avec succès!');
    }

    /**
     * Afficher un paiement (admin)
     */
    public function showPaiement(Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $paiement->load(['apprenant.utilisateur', 'module']);
        return view('admin.paiements.show', compact('paiement'));
    }

    /**
     * Editer un paiement (admin)
     */
    public function editPaiement(Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $paiement->load(['apprenant.utilisateur', 'module']);
        return view('admin.paiements.edit', compact('paiement'));
    }

    /**
     * Mettre à jour un paiement (admin)
     */
    public function updatePaiement(Request $request, Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,valide,refuse',
            'methode' => 'nullable|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $paiement->update($data);
        return redirect()->route('admin.paiements')->with('success', 'Paiement modifié avec succès');
    }

    /**
     * Supprimer un paiement (admin)
     */
    public function destroyPaiement(Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $paiement->delete();
        return redirect()->route('admin.paiements')->with('success', 'Paiement supprimé avec succès');
    }

    /**
     * Afficher une inscription (admin)
     */
    public function showInscription(Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription->load(['apprenant.utilisateur', 'module']);
        return view('admin.inscriptions.show', compact('inscription'));
    }

    /**
     * Editer une inscription (admin)
     */
    public function editInscription(Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription->load(['apprenant.utilisateur', 'module']);
        return view('admin.inscriptions.edit', compact('inscription'));
    }

    /**
     * Mettre à jour une inscription (admin)
     */
    public function updateInscription(Request $request, Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $data = $request->validate([
            'statut' => 'required|in:en_attente,valide,refuse',
            'date_inscription' => 'required|date',
        ]);
        $inscription->update($data);
        return redirect()->route('admin.inscriptions')->with('success', 'Inscription modifiée avec succès');
    }

    /**
     * Supprimer une inscription (admin)
     */
    public function destroyInscription(Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription->delete();
        return redirect()->route('admin.inscriptions')->with('success', 'Inscription supprimée avec succès');
    }
}

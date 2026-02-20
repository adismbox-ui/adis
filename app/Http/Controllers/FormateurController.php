<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formateur;
use Illuminate\Support\Facades\Auth;

class FormateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Formateur::with(['utilisateur', 'niveaux']);
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->whereHas('utilisateur', function($uq) use ($search) {
                    $uq->where('prenom', 'like', "%$search%")
                       ->orWhere('nom', 'like', "%$search%")
                       ->orWhere('email', 'like', "%$search%")
                       ->orWhere('telephone', 'like', "%$search%");
                })
                ->orWhere('id', intval($search) > 0 ? intval($search) : 0);
            });
        }
        $formateurs = $query->get();
        return view('formateurs.index', compact('formateurs'));
    }

    public function toggleActivation(Formateur $formateur)
    {
        $utilisateur = $formateur->utilisateur;
        if (!$utilisateur) {
            return back()->with('error', "Utilisateur associé introuvable.");
        }
        $utilisateur->actif = !$utilisateur->actif;
        $utilisateur->save();
        $etat = $utilisateur->actif ? 'activé' : 'désactivé';
        return back()->with('success', "Le formateur a été $etat avec succès.");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $formateurs = Formateur::with('utilisateur')->where('valide', true)->get();
        return view('formateurs.create', compact('niveaux', 'formateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'telephone' => 'nullable|string|max:20',
            'formation_adis' => 'nullable|boolean',
            'formation_autre' => 'nullable|boolean',
            'niveau_coran' => 'nullable|string',
            'niveau_arabe' => 'nullable|string',
            'niveau_francais' => 'nullable|string',
            'ville' => 'nullable|string',
            'commune' => 'nullable|string',
            'quartier' => 'nullable|string',
            'mot_de_passe' => 'required|string|min:6|confirmed',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'devenir_assistant' => 'nullable|in:oui,non',
        ]);

        // Conversion des valeurs boolean
        $data['formation_adis'] = $request->has('formation_adis');
        $data['formation_autre'] = $request->has('formation_autre');

        // Créer un utilisateur de type formateur avec les vraies informations
        $utilisateur = \App\Models\Utilisateur::create([
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'email' => $data['email'],
            'mot_de_passe' => bcrypt($data['mot_de_passe']),
            'type_compte' => 'formateur',
            'sexe' => 'Homme', // Valeur par défaut
            'categorie' => 'Enseignant',
            'telephone' => $data['telephone'],
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        // Ajouter l'ID de l'utilisateur créé
        $data['utilisateur_id'] = $utilisateur->id;

        $formateur = Formateur::create($data);

        // S'il y a un niveau choisi, le lier comme responsable au niveau
        if (!empty($data['niveau_id'])) {
            $niveau = \App\Models\Niveau::find($data['niveau_id']);
            if ($niveau) {
                $niveau->formateur_id = $formateur->id;
                $niveau->save();
            }
        }

        // Si l'option "devenir assistant" est sélectionnée, créer aussi un enregistrement assistant
        if (!empty($data['devenir_assistant']) && $data['devenir_assistant'] === 'oui') {
            // Créer l'enregistrement assistant
            \App\Models\Assistant::create([
                'utilisateur_id' => $utilisateur->id,
                'formateur_id' => $formateur->id, // L'assistant est lié au formateur créé
                'actif' => true,
            ]);
        }

        $message = 'Formateur créé avec succès ! L\'utilisateur associé a été créé automatiquement.';
        if (!empty($data['devenir_assistant']) && $data['devenir_assistant'] === 'oui') {
            $message = 'Formateur-Assistant créé avec succès ! L\'utilisateur a les deux rôles.';
        }
        return redirect()->route('formateurs.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Formateur $formateur)
    {
        return view('formateurs.show', compact('formateur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formateur $formateur)
    {
        $formateur->load('utilisateur');
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        
        // Vérifier si le formateur est aussi assistant
        $isAssistant = \App\Models\Assistant::where('utilisateur_id', $formateur->utilisateur->id)
            ->where('actif', true)
            ->exists();
        
        return view('formateurs.edit', compact('formateur', 'niveaux', 'isAssistant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Formateur $formateur)
    {
        $utilisateur = $formateur->utilisateur;
        $data = $request->validate([
            'prenom' => 'nullable|string|max:255',
            'nom' => 'nullable|string|max:255',
            'email' => 'nullable|email' . ($utilisateur ? '|unique:utilisateurs,email,' . $utilisateur->id : ''),
            'telephone' => 'nullable|string|max:20',
            'mot_de_passe' => 'nullable|string|min:6|confirmed',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'devenir_assistant' => 'nullable|in:oui,non',
        ]);

        if ($utilisateur) {
            if (array_key_exists('prenom', $data) && $data['prenom'] !== null) $utilisateur->prenom = $data['prenom'];
            if (array_key_exists('nom', $data) && $data['nom'] !== null) $utilisateur->nom = $data['nom'];
            if (array_key_exists('email', $data) && $data['email'] !== null) $utilisateur->email = $data['email'];
            if (array_key_exists('telephone', $data)) $utilisateur->telephone = $data['telephone'];
            if (!empty($data['mot_de_passe'])) $utilisateur->mot_de_passe = bcrypt($data['mot_de_passe']);
            $utilisateur->save();
        }

        if (!empty($data['niveau_id'])) {
            $niveau = \App\Models\Niveau::find($data['niveau_id']);
            if ($niveau) {
                $niveau->formateur_id = $formateur->id;
                $niveau->save();
            }
        }

        // Gérer le statut assistant
        if (isset($data['devenir_assistant'])) {
            $existingAssistant = \App\Models\Assistant::where('utilisateur_id', $utilisateur->id)->first();
            
            if ($data['devenir_assistant'] === 'oui') {
                // Créer ou activer l'assistant
                if ($existingAssistant) {
                    $existingAssistant->actif = true;
                    $existingAssistant->save();
                } else {
                    \App\Models\Assistant::create([
                        'utilisateur_id' => $utilisateur->id,
                        'formateur_id' => $formateur->id,
                        'actif' => true,
                    ]);
                }
            } else {
                // Désactiver l'assistant
                if ($existingAssistant) {
                    $existingAssistant->actif = false;
                    $existingAssistant->save();
                }
            }
        }

        $message = 'Formateur mis à jour.';
        if (isset($data['devenir_assistant']) && $data['devenir_assistant'] === 'oui') {
            $message = 'Formateur mis à jour. Le rôle assistant a été ajouté.';
        } elseif (isset($data['devenir_assistant']) && $data['devenir_assistant'] === 'non') {
            $message = 'Formateur mis à jour. Le rôle assistant a été retiré.';
        }

        return redirect()->route('formateurs.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formateur $formateur)
    {
        $formateur->delete();
        return redirect()->route('formateurs.index');
    }



    /**
     * Affiche le dashboard du formateur avec gestion des documents.
     */
    public function dashboard()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $formateur = $user->formateur;
        $assistant = $user->assistant; // Charger le profil assistant si il existe
        $niveaux = $formateur ? $formateur->niveaux()->with(['modules.documents', 'modules.inscriptions.apprenant.utilisateur', 'modules.questionnaires.questions'])->get() : collect();
        
        // Pour chaque niveau, calculer les points de chaque étudiant inscrit dans ses modules
        foreach ($niveaux as $niveau) {
            foreach ($niveau->modules as $module) {
                foreach ($module->inscriptions as $inscription) {
                    $apprenant = $inscription->apprenant;
                    $totalPoints = 0;
                    // Récupérer toutes les questions des questionnaires du module
                    $questions = $module->questionnaires->flatMap->questions;
                    foreach ($questions as $question) {
                        $bonne = $question->bonne_reponse;
                        $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                            ->where('question_id', $question->id)
                            ->value('reponse');
                        if ($reponse === $bonne) {
                            $totalPoints += $question->points ?? 1;
                        }
                    }
                    $inscription->points = $totalPoints;
                }
            }
        }
        return view('formateurs.dashboard', compact('user', 'formateur', 'assistant', 'niveaux'));
    }

    /**
     * Affiche les documents liés aux modules du formateur.
     */
    public function documentFormateur()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $formateur = $user->formateur;
        // Charger documents ET audio (depuis la base de données) pour chaque module du formateur
        $modules = $formateur ? $formateur->modules()->with('documents')->get() : collect();

        // Charger aussi les documents "généraux de niveau" créés par l'admin
        // (niveau sélectionné, module laissé vide dans l'admin)
        $documentsNiveaux = collect();
        if ($formateur) {
            $niveauIds = $formateur->niveaux()->pluck('id');
            if ($niveauIds->isNotEmpty()) {
                $documentsNiveaux = \App\Models\Document::whereNull('module_id')
                    ->whereIn('niveau_id', $niveauIds)
                    ->with('niveau')
                    ->orderBy('date_envoi', 'desc')
                    ->get();
            }
        }
        // Pour chaque module, s'assurer que l'audio est bien le champ de la base
        foreach ($modules as $module) {
            if ($module->audio && !str_starts_with($module->audio, 'audios/')) {
                // Si l'audio n'est pas un chemin, on peut le corriger ici si besoin
                $module->audio = 'audios/' . $module->audio;
            }
        }
        return view('formateurs.document_formateur', compact('modules', 'documentsNiveaux'));
    }

    /**
     * Affiche les questionnaires liés aux niveaux/modules du formateur.
     */
    public function questionnairesFormateur()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $formateur = $user->formateur;
        if (!$formateur) {
            return redirect()->route('formateurs.dashboard')->withErrors(['Profil formateur introuvable.']);
        }

        // Récupérer les modules enseignés par le formateur, avec leurs questionnaires et niveau
        $modules = $formateur->modules()->with(['niveau', 'questionnaires.questions'])->get();

        // Aplatir les questionnaires avec infos de module/niveau pour l'affichage
        $questionnaires = collect();
        foreach ($modules as $module) {
            foreach ($module->questionnaires as $q) {
                $questionnaires->push([
                    'id' => $q->id,
                    'titre' => $q->titre,
                    'description' => $q->description,
                    'date_envoi' => $q->date_envoi,
                    'envoye' => $q->envoye,
                    'module' => $module,
                    'niveau' => $module->niveau,
                    'questions_count' => $q->questions ? $q->questions->count() : 0,
                ]);
            }
        }

        return view('formateurs.questionnaires', compact('questionnaires'));
    }

    /**
     * Connexion automatique via token unique (après validation admin)
     */
    public function autoLogin($token)
    {
        $formateur = Formateur::where('validation_token', $token)->where('valide', true)->with('utilisateur')->first();
        if (!$formateur || !$formateur->utilisateur) {
            return redirect('/login')->withErrors(['Lien invalide ou expiré.']);
        }
        // Connecter l'utilisateur
        auth()->login($formateur->utilisateur);
        // Invalider le token (usage unique)
        $formateur->validation_token = null;
        $formateur->save();
        return redirect()->route('formateurs.dashboard')->with('success', 'Bienvenue, votre compte a été activé !');
    }
}

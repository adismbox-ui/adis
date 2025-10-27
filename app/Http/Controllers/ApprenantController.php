<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apprenant;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\Formateur;
use App\Models\Certificat;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apprenants = Apprenant::with(['utilisateur', 'niveau'])->get();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('apprenants.index', compact('apprenants','niveaux'));
    }

    /**
     * Active/Désactive le compte utilisateur associé à l'apprenant.
     */
    public function toggleActivation(Apprenant $apprenant)
    {
        $utilisateur = $apprenant->utilisateur;
        if (!$utilisateur) {
            return back()->with('error', "Utilisateur associé introuvable.");
        }
        $utilisateur->actif = !$utilisateur->actif;
        $utilisateur->save();
        $etat = $utilisateur->actif ? 'activé' : 'désactivé';
        return back()->with('success', "L'apprenant a été $etat avec succès.");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        // Lister les utilisateurs formateurs existants pour permettre l'inscription comme apprenant
        $formateurs = \App\Models\Utilisateur::where('type_compte', 'formateur')
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get(['id','prenom','nom','email']);
        return view('apprenants.create', compact('niveaux', 'formateurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Si un utilisateur formateur existant est choisi, on ne crée pas un nouvel utilisateur
        if ($request->filled('utilisateur_id')) {
            $data = $request->validate([
                'utilisateur_id' => 'required|exists:utilisateurs,id',
                'niveau_id' => 'nullable|exists:niveaux,id',
                'connaissance_adis' => 'nullable|string',
                'formation_adis' => 'nullable|in:1,0',
                'formation_autre' => 'nullable|in:1,0',
                'niveau_coran' => 'nullable|in:Débutant,Intermédiaire,Avancé',
                'niveau_arabe' => 'nullable|in:Débutant,Intermédiaire,Avancé',
                'connaissance_tomes_medine' => 'nullable|in:1,0',
                'tomes_medine_etudies' => 'nullable|array',
                'disciplines_souhaitees' => 'nullable|array',
                'attentes' => 'nullable|array',
                'formateur_domicile' => 'nullable|in:1,0',
            ]);

            $utilisateur = \App\Models\Utilisateur::find($data['utilisateur_id']);
            if (!$utilisateur) {
                return back()->withErrors(['utilisateur_id' => "Utilisateur introuvable."])->withInput();
            }
            // Optionnel: s'assurer qu'il s'agit bien d'un formateur
            if ($utilisateur->type_compte !== 'formateur') {
                return back()->withErrors(['utilisateur_id' => "L'utilisateur sélectionné n'est pas un formateur."])->withInput();
            }
            // Empêcher la création en double
            $existeDeja = \App\Models\Apprenant::where('utilisateur_id', $utilisateur->id)->exists();
            if ($existeDeja) {
                return back()->withErrors(['utilisateur_id' => "Cet utilisateur possède déjà un profil apprenant."])->withInput();
            }

            \App\Models\Apprenant::create([
                'utilisateur_id' => $utilisateur->id,
                'niveau_id' => $data['niveau_id'] ?? null,
                'connaissance_adis' => $data['connaissance_adis'] ?? null,
                'formation_adis' => $data['formation_adis'] ?? null,
                'formation_autre' => $data['formation_autre'] ?? null,
                'niveau_coran' => $data['niveau_coran'] ?? null,
                'niveau_arabe' => $data['niveau_arabe'] ?? null,
                'connaissance_tomes_medine' => $data['connaissance_tomes_medine'] ?? null,
                'tomes_medine_etudies' => isset($data['tomes_medine_etudies']) ? json_encode($data['tomes_medine_etudies']) : null,
                'disciplines_souhaitees' => isset($data['disciplines_souhaitees']) ? json_encode($data['disciplines_souhaitees']) : null,
                'attentes' => isset($data['attentes']) ? json_encode($data['attentes']) : null,
                'formateur_domicile' => $data['formateur_domicile'] ?? null,
            ]);

            return redirect()->route('apprenants.index')->with('success', "Profil apprenant créé pour le formateur sélectionné.");
        }

        // Sinon, créer un nouvel utilisateur + apprenant
        $data = $request->validate([
            // Utilisateur
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:Homme,Femme',
            'categorie' => 'required|in:Enfant,Etudiant,Professionnel',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:utilisateurs,email',
            'mot_de_passe' => 'required|string|min:6',
            // Apprenant
            'niveau_id' => 'nullable|exists:niveaux,id',
            'connaissance_adis' => 'nullable|string',
            'formation_adis' => 'nullable|in:1,0',
            'formation_autre' => 'nullable|in:1,0',
            'niveau_coran' => 'nullable|in:Débutant,Intermédiaire,Avancé',
            'niveau_arabe' => 'nullable|in:Débutant,Intermédiaire,Avancé',
            'connaissance_tomes_medine' => 'nullable|in:1,0',
            'tomes_medine_etudies' => 'nullable|array',
            'disciplines_souhaitees' => 'nullable|array',
            'attentes' => 'nullable|array',
            'formateur_domicile' => 'nullable|in:1,0',
        ]);

        $utilisateur = \App\Models\Utilisateur::create([
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'sexe' => $data['sexe'],
            'categorie' => $data['categorie'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'mot_de_passe' => bcrypt($data['mot_de_passe']),
            'type_compte' => 'apprenant',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        \App\Models\Apprenant::create([
            'utilisateur_id' => $utilisateur->id,
            'niveau_id' => $data['niveau_id'] ?? null,
            'connaissance_adis' => $data['connaissance_adis'] ?? null,
            'formation_adis' => $data['formation_adis'] ?? null,
            'formation_autre' => $data['formation_autre'] ?? null,
            'niveau_coran' => $data['niveau_coran'] ?? null,
            'niveau_arabe' => $data['niveau_arabe'] ?? null,
            'connaissance_tomes_medine' => $data['connaissance_tomes_medine'] ?? null,
            'tomes_medine_etudies' => isset($data['tomes_medine_etudies']) ? json_encode($data['tomes_medine_etudies']) : null,
            'disciplines_souhaitees' => isset($data['disciplines_souhaitees']) ? json_encode($data['disciplines_souhaitees']) : null,
            'attentes' => isset($data['attentes']) ? json_encode($data['attentes']) : null,
            'formateur_domicile' => $data['formateur_domicile'] ?? null,
        ]);

        return redirect()->route('apprenants.index')->with('success', 'Apprenant et utilisateur créés avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Apprenant $apprenant)
    {
        $apprenant->load('niveau');
        return view('apprenants.show', compact('apprenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apprenant $apprenant)
    {
        $apprenant->load('utilisateur');
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('apprenants.edit', compact('apprenant', 'niveaux'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Apprenant $apprenant)
    {
        $userId = $apprenant->utilisateur_id;
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email,' . $userId,
            'mot_de_passe' => 'nullable|string|min:6',
            'sexe' => 'required|in:Homme,Femme',
            'categorie' => 'required|in:Enfant,Etudiant,Professionnel',
            'niveau_id' => 'nullable|exists:niveaux,id',
        ]);

        $utilisateur = $apprenant->utilisateur;
        if ($utilisateur) {
            $utilisateur->prenom = $data['prenom'];
            $utilisateur->nom = $data['nom'];
            $utilisateur->email = $data['email'];
            $utilisateur->sexe = $data['sexe'];
            $utilisateur->categorie = $data['categorie'];
            if (!empty($data['mot_de_passe'])) {
                $utilisateur->mot_de_passe = bcrypt($data['mot_de_passe']);
            }
            $utilisateur->save();
        }

        $apprenant->niveau_id = $data['niveau_id'] ?? null;
        $apprenant->save();

        return redirect()->route('apprenants.index')->with('success', "Profil de l'apprenant mis à jour.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apprenant $apprenant)
    {
        $apprenant->delete();
        return redirect()->route('apprenants.index');
    }

    /**
     * Admin: inscrit un apprenant à tous les modules de son niveau courant.
     */
    public function adminInscrireNiveau(Apprenant $apprenant)
    {
        // Récupérer le niveau courant de l'apprenant
        if (!$apprenant->niveau_id) {
            return back()->with('error', "Cet apprenant n'a pas de niveau défini.");
        }
        $modules = \App\Models\Module::where('niveau_id', $apprenant->niveau_id)->get();
        $inscrits = 0;
        foreach ($modules as $module) {
            $existe = \App\Models\Inscription::where('apprenant_id', $apprenant->id)
                ->where('module_id', $module->id)
                ->exists();
            if (!$existe) {
                \App\Models\Inscription::create([
                    'apprenant_id' => $apprenant->id,
                    'module_id' => $module->id,
                    'date_inscription' => now(),
                    'statut' => 'en_attente',
                ]);
                $inscrits++;
            }
        }
        return back()->with('success', "$inscrits module(s) inscrits pour l'apprenant #{$apprenant->id}.");
    }

    /**
     * Admin: fait passer un apprenant au niveau supérieur.
     */
    public function adminPasserNiveau(Apprenant $apprenant)
    {
        // Vérifier que l'apprenant a un niveau actuel
        if (!$apprenant->niveau_id) {
            return back()->with('error', "Cet apprenant n'a pas de niveau défini.");
        }

        // Récupérer le niveau actuel
        $niveauActuel = \App\Models\Niveau::find($apprenant->niveau_id);
        if (!$niveauActuel) {
            return back()->with('error', "Niveau actuel introuvable.");
        }

        // Vérifier que TOUS les modules du niveau actuel sont à 60% ou plus
        $modulesNiveau = \App\Models\Module::where('niveau_id', $niveauActuel->id)->get();
        $modulesInsuffisants = [];
        foreach ($modulesNiveau as $module) {
            // Récupérer toutes les questions des questionnaires du module
            $questionnaires = \App\Models\Questionnaire::where('module_id', $module->id)->get();
            $questions = collect();
            foreach ($questionnaires as $q) {
                $questions = $questions->merge($q->questions);
            }

            $pointsPossibles = $questions->sum(function($q) { return $q->points ?? 1; });
            // Si pas de questions configurées, considérer comme 0% (à configurer par l'équipe)
            if ($pointsPossibles <= 0) {
                $modulesInsuffisants[] = [
                    'module' => $module->titre,
                    'pourcentage' => 0,
                ];
                continue;
            }

            $pointsObtenus = 0;
            foreach ($questions as $q) {
                $bonne = $q->bonne_reponse;
                $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                    ->where('question_id', $q->id)
                    ->value('reponse');
                if ($reponse === $bonne) {
                    $pointsObtenus += $q->points ?? 1;
                }
            }

            $pourcentageModule = $pointsPossibles > 0 ? ($pointsObtenus / $pointsPossibles) * 100 : 0;
            if ($pourcentageModule < 60) {
                $modulesInsuffisants[] = [
                    'module' => $module->titre,
                    'pourcentage' => round($pourcentageModule, 2),
                ];
            }
        }

        if (!empty($modulesInsuffisants)) {
            // Préparer un message utilisateur clair listant les modules sous 60%
            $details = collect($modulesInsuffisants)
                ->map(fn($m) => $m['module'] . ' (' . $m['pourcentage'] . '%)')
                ->implode(', ');
            return back()->with('error', "Passage de niveau bloqué: l'apprenant doit atteindre au moins 60% dans chaque module. Modules insuffisants: " . $details);
        }

        // Trouver le niveau suivant
        $niveauSuivant = \App\Models\Niveau::where('ordre', '>', $niveauActuel->ordre)
            ->orderBy('ordre')
            ->first();

        if (!$niveauSuivant) {
            return back()->with('error', "Aucun niveau supérieur disponible.");
        }

        // Mettre à jour le niveau de l'apprenant
        $apprenant->update(['niveau_id' => $niveauSuivant->id]);

        // Générer le certificat du niveau précédent si non existant
        $titreCertif = 'Certificat de niveau ' . ($niveauActuel->nom ?? '');
        if (!empty($titreCertif)) {
            $certifExiste = \App\Models\Certificat::where('apprenant_id', $apprenant->id)
                ->whereNull('module_id')
                ->where('titre', $titreCertif)
                ->exists();
            if (!$certifExiste) {
                \App\Models\Certificat::create([
                    'apprenant_id' => $apprenant->id,
                    'module_id' => null,
                    'titre' => $titreCertif,
                    'date_obtention' => now(),
                ]);
            }
        }

        // Inscrire automatiquement l'apprenant aux modules du nouveau niveau
        $modules = \App\Models\Module::where('niveau_id', $niveauSuivant->id)->get();
        $inscrits = 0;
        foreach ($modules as $module) {
            $existe = \App\Models\Inscription::where('apprenant_id', $apprenant->id)
                ->where('module_id', $module->id)
                ->exists();
            if (!$existe) {
                \App\Models\Inscription::create([
                    'apprenant_id' => $apprenant->id,
                    'module_id' => $module->id,
                    'date_inscription' => now(),
                    'statut' => 'en_attente',
                ]);
                $inscrits++;
            }
        }

        return back()->with('success', "L'apprenant #{$apprenant->id} est passé du niveau '{$niveauActuel->nom}' au niveau '{$niveauSuivant->nom}'. Certificat du niveau précédent généré. {$inscrits} module(s) du nouveau niveau ont été ajoutés. L'apprenant pourra le visualiser sur /certificat-test.");
    }



    /**
     * Affiche le dashboard de l'apprenant.
     */
    public function dashboard()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $apprenant = $user->apprenant;
        // Formations en cours (statut 'en cours')
        $inscriptionsEnCours = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'en cours')->get() : collect();
        // Formations terminées (statut 'termine')
        $inscriptionsTerminees = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'termine')->get() : collect();
        // Certificats
        $certificats = $apprenant ? $apprenant->certificats()->with('module')->get() : collect();
        // Notifications (mock pour l'instant)
        $notifications = [
            'Nouvelle vidéo ajoutée à “Tajwîd - Initiation”',
            'Votre certificat “Lecture Coranique” est prêt à être téléchargé',
        ];
        // Documents proposés par le formateur pour les modules suivis
        $moduleIds = $inscriptionsEnCours->pluck('module_id')->merge($inscriptionsTerminees->pluck('module_id'))->unique()->filter();
        $documents = \App\Models\Document::with(['module', 'formateur.utilisateur'])->whereIn('module_id', $moduleIds)->get();
        foreach ($documents as $doc) {
            $doc->formateur_nom = 'Non renseigné';
            if ($doc->formateur && $doc->formateur->utilisateur) {
                $doc->formateur_nom = $doc->formateur->utilisateur->prenom . ' ' . $doc->formateur->utilisateur->nom;
            } elseif ($doc->module && isset($doc->module->formateur_id)) {
                $formateur = \App\Models\Formateur::with('utilisateur')->find($doc->module->formateur_id);
                if ($formateur && $formateur->utilisateur) {
                    $doc->formateur_nom = $formateur->utilisateur->prenom . ' ' . $formateur->utilisateur->nom;
                }
            }
        }
        // Documents généraux (envoyés à tous les apprenants)
        $documentsGeneraux = \App\Models\Document::with(['module', 'formateur.utilisateur'])->whereNull('module_id')->get();
        foreach ($documentsGeneraux as $doc) {
            $formateur = null;
            if ($doc->formateur && $doc->formateur->utilisateur) {
                $formateur = $doc->formateur;
            } elseif (!empty($doc->formateur_id)) {
                $formateur = \App\Models\Formateur::with('utilisateur')->find($doc->formateur_id);
            }
            if ($formateur && $formateur->utilisateur) {
                $doc->formateur_nom = $formateur->utilisateur->prenom . ' ' . $formateur->utilisateur->nom;
            } else {
                $doc->formateur_nom = 'Non renseigné';
            }
        }
        // Paiements de l'apprenant
        $paiements = $apprenant ? $apprenant->paiements()->with('module')->orderByDesc('created_at')->get() : collect();
        // Modules du niveau de l'apprenant pour la section "Découvrez nos formations"
        $modules = collect();
        if (
            isset(
                // Vérifie que l'apprenant existe et a un niveau défini
                $apprenant, $apprenant->niveau_id
            ) && $apprenant->niveau_id
        ) {
            $modules = \App\Models\Module::where('niveau_id', $apprenant->niveau_id)->orderBy('titre')->get();
        }

        // --- NOUVEAU CALCUL : MOYENNE DES POURCENTAGES PAR MODULE ---
        $moyenneModules = 0;
        $nbModules = 0;
        $totalPoints = 0; // Pour compatibilité, mais n'est plus affiché
        $pourcentage = 0;
        $modulesPourcentages = [];
        $modulesNonValides = [];
        if ($apprenant && $apprenant->niveau_id) {
            $niveauId = $apprenant->niveau_id;
            $modulesNiveau = \App\Models\Module::where('niveau_id', $niveauId)->get();
            $nbModules = $modulesNiveau->count();
            $sommePourcentages = 0;
            foreach ($modulesNiveau as $module) {
                // Récupérer tous les questionnaires du module
                $questionnaires = \App\Models\Questionnaire::where('module_id', $module->id)->get();
                $questions = collect();
                foreach ($questionnaires as $q) {
                    $questions = $questions->merge($q->questions);
                }
                $pointsPossibles = $questions->sum(function($q) { return $q->points ?? 1; });
                $pointsObtenus = 0;
                foreach ($questions as $q) {
                    $bonne = $q->bonne_reponse;
                    $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                        ->where('question_id', $q->id)
                        ->value('reponse');
                    if ($reponse === $bonne) {
                        $pointsObtenus += $q->points ?? 1;
                    }
                }
                $pourcentageModule = ($pointsPossibles > 0) ? ($pointsObtenus / $pointsPossibles) * 100 : 0;
                // Validation automatique du module
                $inscription = \App\Models\Inscription::where('apprenant_id', $apprenant->id)
                    ->where('module_id', $module->id)
                    ->first();
                if ($inscription) {
                    if ($pourcentageModule >= 60 && $inscription->statut !== 'valide') {
                        $inscription->statut = 'valide';
                        $inscription->save();
                    } elseif ($pourcentageModule < 60 && $inscription->statut === 'valide') {
                        $inscription->statut = 'en_attente';
                        $inscription->save();
                    }
                }
                if ($pourcentageModule < 60) {
                    $modulesNonValides[] = $module->titre;
                }
                $sommePourcentages += $pourcentageModule;
                $modulesPourcentages[] = [
                    'titre' => $module->titre,
                    'pourcentage' => round($pourcentageModule, 2),
                    'points_obtenus' => $pointsObtenus,
                    'points_possibles' => $pointsPossibles,
                ];
            }
            if ($nbModules > 0) {
                $moyenneModules = $sommePourcentages / $nbModules;
            }
            $pourcentage = round($moyenneModules, 2); // Pour affichage
        }
        // --- PROGRESSION DE NIVEAU AVEC NOUVELLE LOGIQUE ---
        $nextNiveau = null;
        // Passage de niveau : il faut que tous les modules soient à 60% ou plus
        if ($apprenant && $apprenant->niveau_id && $pourcentage >= 60 && empty($modulesNonValides)) {
            $currentNiveau = $apprenant->niveau;
            $nextNiveau = \App\Models\Niveau::where('ordre', '>', $currentNiveau->ordre ?? 0)
                ->orderBy('ordre')
                ->first();
            if ($nextNiveau) {
                $apprenant->niveau_id = $nextNiveau->id;
                $apprenant->save();
            }
        }
        return view('apprenants.dashboard', compact('user', 'apprenant', 'inscriptionsEnCours', 'inscriptionsTerminees', 'certificats', 'notifications', 'documents', 'documentsGeneraux', 'paiements', 'modules', 'totalPoints', 'nextNiveau', 'pourcentage', 'modulesPourcentages', 'modulesNonValides'));
    }

    /**
     * Génère un certificat de niveau pour l'apprenant et télécharge le PDF.
     */
    public function genererCertificatNiveau(Request $request, Apprenant $apprenant)
    {
        $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
        ]);
        $niveau = \App\Models\Niveau::find($request->input('niveau_id'));
        if (!$niveau) {
            return back()->with('error', 'Niveau introuvable.');
        }

        // Rechercher un certificat existant pour ce niveau
        $titre = 'Certificat de niveau ' . $niveau->nom;
        $certificat = Certificat::where('apprenant_id', $apprenant->id)
            ->whereNull('module_id')
            ->where('titre', $titre)
            ->first();

        if (!$certificat) {
            $certificat = Certificat::create([
                'apprenant_id' => $apprenant->id,
                'module_id' => null,
                'titre' => $titre,
                'date_obtention' => now(),
            ]);
        }

        // Ne pas télécharger; rediriger l'admin vers la page des certificats
        return redirect()->route('admin.certificats.index')
            ->with('success', "Certificat de niveau '{$niveau->nom}' généré pour l'apprenant #{$apprenant->id}. Consultez la page des certificats.");
    }

    /**
     * Affiche la page test-inscription de l'apprenant.
     */
    public function testInscription()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $apprenant = $user->apprenant;
        $inscriptionsEnCours = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'en cours')->get() : collect();
        $inscriptionsTerminees = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'termine')->get() : collect();
        $certificats = $apprenant ? $apprenant->certificats()->with('module')->get() : collect();
        $notifications = [
            'Nouvelle vidéo ajoutée à “Tajwîd - Initiation”',
            'Votre certificat “Lecture Coranique” est prêt à être téléchargé',
        ];
        $moduleIds = $inscriptionsEnCours->pluck('module_id')->merge($inscriptionsTerminees->pluck('module_id'))->unique()->filter();
        $documents = \App\Models\Document::with(['module', 'formateur.utilisateur'])->whereIn('module_id', $moduleIds)->get();
        foreach ($documents as $doc) {
            $doc->formateur_nom = 'Non renseigné';
            if ($doc->formateur && $doc->formateur->utilisateur) {
                $doc->formateur_nom = $doc->formateur->utilisateur->prenom . ' ' . $doc->formateur->utilisateur->nom;
            } elseif ($doc->module && isset($doc->module->formateur_id)) {
                $formateur = \App\Models\Formateur::with('utilisateur')->find($doc->module->formateur_id);
                if ($formateur && $formateur->utilisateur) {
                    $doc->formateur_nom = $formateur->utilisateur->prenom . ' ' . $formateur->utilisateur->nom;
                }
            }
        }
        $documentsGeneraux = \App\Models\Document::with(['module', 'formateur.utilisateur'])->whereNull('module_id')->get();
        foreach ($documentsGeneraux as $doc) {
            $formateur = null;
            if ($doc->formateur && $doc->formateur->utilisateur) {
                $formateur = $doc->formateur;
            } elseif (!empty($doc->formateur_id)) {
                $formateur = \App\Models\Formateur::with('utilisateur')->find($doc->formateur_id);
            }
            if ($formateur && $formateur->utilisateur) {
                $doc->formateur_nom = $formateur->utilisateur->prenom . ' ' . $formateur->utilisateur->nom;
            } else {
                $doc->formateur_nom = 'Non renseigné';
            }
        }
        $paiements = $apprenant ? $apprenant->paiements()->with('module')->orderByDesc('created_at')->get() : collect();
        $modules = \App\Models\Module::orderBy('titre')->get();
        return view('apprenants.test-inscription', compact('user', 'apprenant', 'inscriptionsEnCours', 'inscriptionsTerminees', 'certificats', 'notifications', 'documents', 'documentsGeneraux', 'paiements', 'modules'));
    }

    /**
     * Affiche la page gada de l'apprenant (identique au dashboard).
     */
    public function gada()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $apprenant = $user->apprenant;
        $inscriptionsEnCours = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'en cours')->get() : collect();
        $inscriptionsTerminees = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'termine')->get() : collect();
        $certificats = $apprenant ? $apprenant->certificats()->with('module')->get() : collect();
        $notifications = [
            'Nouvelle vidéo ajoutée à “Tajwîd - Initiation”',
            'Votre certificat “Lecture Coranique” est prêt à être téléchargé',
        ];
        $moduleIds = $inscriptionsEnCours->pluck('module_id')->merge($inscriptionsTerminees->pluck('module_id'))->unique()->filter();
        $documents = \App\Models\Document::with(['module', 'formateur.utilisateur'])->whereIn('module_id', $moduleIds)->get();
        foreach ($documents as $doc) {
            $doc->formateur_nom = 'Non renseigné';
            if ($doc->formateur && $doc->formateur->utilisateur) {
                $doc->formateur_nom = $doc->formateur->utilisateur->prenom . ' ' . $doc->formateur->utilisateur->nom;
            } elseif ($doc->module && isset($doc->module->formateur_id)) {
                $formateur = \App\Models\Formateur::with('utilisateur')->find($doc->module->formateur_id);
                if ($formateur && $formateur->utilisateur) {
                    $doc->formateur_nom = $formateur->utilisateur->prenom . ' ' . $formateur->utilisateur->nom;
                }
            }
        }
        $documentsGeneraux = \App\Models\Document::with(['module', 'formateur.utilisateur'])->whereNull('module_id')->get();
        foreach ($documentsGeneraux as $doc) {
            $formateur = null;
            if ($doc->formateur && $doc->formateur->utilisateur) {
                $formateur = $doc->formateur;
            } elseif (!empty($doc->formateur_id)) {
                $formateur = \App\Models\Formateur::with('utilisateur')->find($doc->formateur_id);
            }
            if ($formateur && $formateur->utilisateur) {
                $doc->formateur_nom = $formateur->utilisateur->prenom . ' ' . $formateur->utilisateur->nom;
            } else {
                $doc->formateur_nom = 'Non renseigné';
            }
        }
        $paiements = $apprenant ? $apprenant->paiements()->with('module')->orderByDesc('created_at')->get() : collect();
        $modules = \App\Models\Module::orderBy('titre')->get();
        return view('apprenants.gada', compact('user', 'apprenant', 'inscriptionsEnCours', 'inscriptionsTerminees', 'certificats', 'notifications', 'documents', 'documentsGeneraux', 'paiements', 'modules'));
    }

    /**
     * Affiche la liste des questionnaires disponibles pour l'apprenant.
     */
    public function questionnaires()
    {
        return view('apprenants.questionnaires');
    }

    /**
     * Affiche le formulaire pour répondre à un questionnaire.
     */
    public function repondreQuestionnaire(\App\Models\Questionnaire $questionnaire)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        $apprenant = $user->apprenant;
        $questionnaire->load('questions');
        return view('apprenants.repondre-questionnaire', compact('user', 'apprenant', 'questionnaire'));
    }

    /**
     * Corrige le questionnaire et affiche le résultat à l'apprenant.
     */
    public function validerQuestionnaire(\Illuminate\Http\Request $request, \App\Models\Questionnaire $questionnaire)
    {
        $questionnaire->load('questions');
        $reponses = $request->input('reponses', []);
        $resultats = [];
        $score = 0;
        foreach ($questionnaire->questions as $q) {
            $bonne = $q->bonne_reponse;
            $reponse = $reponses[$q->id] ?? null;
            $trouve = $reponse === $bonne;
            $resultats[] = [
                'question' => $q->texte,
                'reponse' => $reponse,
                'bonne_reponse' => $bonne,
                'trouve' => $trouve,
                'choix' => $q->choix, // Utiliser directement la propriété qui est déjà un tableau
            ];
            if ($trouve) $score++;
        }
        $total = count($questionnaire->questions);
        return view('apprenants.resultat-questionnaire', compact('questionnaire', 'resultats', 'score', 'total'));
    }

    /**
     * Page de test minimal pour vérifier le fonctionnement des vues personnalisées.
     */
    public function maPage()
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        return view('apprenants.ma-page', compact('user'));
    }

    /**
     * Page certificat-test : affiche les certificats générables si modules validés, génération PDF possible.
     */
    public function certificatTest(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        $apprenant = $user->apprenant;
        $modulesValidés = $apprenant ? $apprenant->inscriptions()->where('statut', 'valide')->with('module')->get() : collect();
        $certificats = $apprenant ? $apprenant->certificats()->with('module')->get() : collect();

        // --- Ajout certificat de niveau ---
        $niveauCertificat = null;
        $niveauPrecedent = null;
        if ($apprenant && $apprenant->niveau_id) {
            $niveauActuel = $apprenant->niveau;
            // Si l'apprenant a dépassé le niveau 1, il a donc validé au moins un niveau
            if ($niveauActuel && $niveauActuel->ordre > 1) {
                $niveauPrecedent = \App\Models\Niveau::where('ordre', $niveauActuel->ordre - 1)->first();
                if ($niveauPrecedent) {
                    $niveauCertificat = $niveauPrecedent;
                }
            }
        }
        // Générer automatiquement le certificat de niveau si besoin
        if ($niveauCertificat) {
            $certificat = \App\Models\Certificat::where('apprenant_id', $apprenant->id)
                ->where('module_id', 0)
                ->where('titre', 'Certificat de niveau ' . $niveauCertificat->nom)
                ->first();
            if (!$certificat) {
                \App\Models\Certificat::create([
                    'apprenant_id' => $apprenant->id,
                    'module_id' => null,
                    'titre' => 'Certificat de niveau ' . $niveauCertificat->nom,
                    'date_obtention' => now(),
                ]);
            }
        }
        // Génération PDF certificat de niveau
        if ($request->has('download_niveau') && $niveauCertificat) {
            // Pour les certificats de niveau, module_id = 0
            $certificat = \App\Models\Certificat::where('apprenant_id', $apprenant->id)
                ->where('module_id', 0)
                ->where('titre', 'Certificat de niveau ' . $niveauCertificat->nom)
                ->first();
            if (!$certificat) {
                $certificat = \App\Models\Certificat::create([
                    'apprenant_id' => $apprenant->id,
                    'module_id' => null, // null pour certificat de niveau (pas de module associé)
                    'titre' => 'Certificat de niveau ' . $niveauCertificat->nom,
                    'date_obtention' => now(),
                ]);
            }
            $pdf = Pdf::loadView('apprenants.certificat-niveau-pdf', [
                'apprenant' => $apprenant,
                'niveau' => $niveauCertificat
            ]);
            return $pdf->download('certificat-niveau-'.$apprenant->id.'-'.$niveauCertificat->id.'.pdf');
        }
        // Génération et enregistrement des certificats de module pour chaque module validé
        if ($request->has('download_module')) {
            $moduleId = $request->get('download_module');
            $module = \App\Models\Module::find($moduleId);
            if ($module) {
                // Vérifier si le certificat existe déjà
                $certificat = \App\Models\Certificat::where('apprenant_id', $apprenant->id)
                    ->where('module_id', $module->id)
                    ->first();
                if (!$certificat) {
                    $certificat = \App\Models\Certificat::create([
                        'apprenant_id' => $apprenant->id,
                        'module_id' => $module->id,
                        'titre' => 'Certificat de module ' . $module->titre,
                        'date_obtention' => now(),
                    ]);
                }
                $pdf = Pdf::loadView('apprenants.certificat-module-pdf', [
                    'apprenant' => $apprenant,
                    'module' => $module
                ]);
                return $pdf->download('certificat-module-'.$apprenant->id.'-'.$module->id.'.pdf');
            }
        }

        // Chercher le certificat de niveau si disponible (pour ouvrir le générateur readonly)
        $certificatNiveau = null;
        if ($apprenant && isset($niveauCertificat) && $niveauCertificat) {
            $certificatNiveau = \App\Models\Certificat::where('apprenant_id', $apprenant->id)
                ->whereNull('module_id')
                ->where('titre', 'Certificat de niveau ' . $niveauCertificat->nom)
                ->first();
        }
        return view('apprenants.certificat-test', compact('apprenant', 'modulesValidés', 'certificats', 'niveauCertificat', 'certificatNiveau'));
    }

    /**
     * Page module-test : affiche les modules payés et/ou inscrits validés.
     */
    public function moduleTest()
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        $apprenant = $user->apprenant;
        // Modules où inscription validée (après validation admin)
        $modulesInscrits = $apprenant ? $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray() : [];
        // Modules payés (paiement validé)
        $modulesPayes = $apprenant ? $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray() : [];
        // Unifier les IDs
        $moduleIds = collect($modulesInscrits)->merge($modulesPayes)->unique()->toArray();
        // Récupérer les modules une seule fois
        $modules = \App\Models\Module::whereIn('id', $moduleIds)->orderBy('titre')->get();
        return view('apprenants.module-test', compact('apprenant', 'modules'));
    }

    /**
     * Page documents-test : affiche les documents filtrés par modules inscrits et semaine (avec choix semaine).
     */
    public function documentsTest(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        $apprenant = $user->apprenant;
        $modulesInscrits = $apprenant ? $apprenant->inscriptions()->where('statut', 'valide')->with('module')->get()->pluck('module') : collect();
        // TEMPORAIRE : inclure tous les statuts pour debug
        $modulesInscritsAll = $apprenant ? $apprenant->inscriptions()->with('module')->get()->pluck('module') : collect();
        $moduleIds = $modulesInscrits->pluck('id');
        $moduleIdsAll = $modulesInscritsAll->pluck('id'); // Pour debug
        $niveauId = $apprenant->niveau_id;
        $semaine = $request->input('semaine', null); // Semaine sélectionnée
        $moduleFiltre = $request->input('module_id', null); // Module sélectionné
        $niveauFiltre = $request->input('niveau_id', null); // Niveau sélectionné

        // Préparer la requête documents
        $documentsQuery = \App\Models\Document::with(['module', 'formateur.utilisateur']);
        // Afficher uniquement les documents envoyés, liés à un module inscrit OU sans module (généraux)
        $documentsQuery->where('envoye', true)
            ->where(function($q) use ($moduleIdsAll) { // TEMPORAIRE : utiliser tous les statuts
                $q->whereIn('module_id', $moduleIdsAll)
                  ->orWhereNull('module_id');
            });
        // Filtrer par semaine si sélectionnée
        if ($semaine) {
            $documentsQuery->where('semaine', $semaine);
        }
        // Filtrer par module si sélectionné
        if ($moduleFiltre) {
            $documentsQuery->where('module_id', $moduleFiltre);
        }
        // Filtrer par niveau si sélectionné
        if ($niveauFiltre) {
            $documentsQuery->where('niveau_id', $niveauFiltre);
        }
        $documents = $documentsQuery->get();
        
        // Debug temporaire
        if (config('app.debug')) {
            \Log::info('Debug documentsTest:', [
                'apprenant_id' => $apprenant ? $apprenant->id : null,
                'moduleIds' => $moduleIds,
                'moduleIdsAll' => $moduleIdsAll, // TEMPORAIRE
                'documents_count' => $documents->count(),
                'query_sql' => $documentsQuery->toSql(),
                'query_bindings' => $documentsQuery->getBindings()
            ]);
            
            // Debug : tous les documents envoyés
            $allSentDocuments = \App\Models\Document::where('envoye', true)->get();
            \Log::info('Tous les documents envoyés:', [
                'count' => $allSentDocuments->count(),
                'documents' => $allSentDocuments->map(function($doc) {
                    return [
                        'id' => $doc->id,
                        'titre' => $doc->titre,
                        'module_id' => $doc->module_id,
                        'envoye' => $doc->envoye
                    ];
                })
            ]);
            
            // Debug : toutes les inscriptions de l'apprenant
            if ($apprenant) {
                $allInscriptions = $apprenant->inscriptions()->with('module')->get();
                \Log::info('Toutes les inscriptions de l\'apprenant:', [
                    'apprenant_id' => $apprenant->id,
                    'inscriptions' => $allInscriptions->map(function($inscription) {
                        return [
                            'id' => $inscription->id,
                            'module_id' => $inscription->module_id,
                            'module_titre' => $inscription->module->titre ?? 'N/A',
                            'statut' => $inscription->statut
                        ];
                    })
                ]);
            }
        }
        
        // Pour le filtre, on récupère toutes les semaines disponibles pour ces modules
        $semainesDisponibles = \App\Models\Document::whereIn('module_id', $moduleIds)->distinct()->pluck('semaine')->filter();
        // Pour le filtre module
        $modulesDisponibles = $modulesInscrits;
        // Pour le filtre niveau
        $niveauxDisponibles = $modulesInscrits->pluck('niveau')->unique('id')->filter();
        return view('apprenants.documents-test', compact('apprenant', 'modulesInscrits', 'documents', 'semainesDisponibles', 'semaine', 'modulesDisponibles', 'moduleFiltre', 'niveauxDisponibles', 'niveauFiltre'));
    }

    /**
     * Afficher le certificat de l'apprenant connecté
     */
    public function showCertificat($certificatId)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');
        
        $apprenant = $user->apprenant;
        if (!$apprenant) return redirect()->route('apprenants.dashboard');
        
        // Récupérer le certificat de l'apprenant
        $certificat = \App\Models\Certificat::where('id', $certificatId)
            ->where('apprenant_id', $apprenant->id)
            ->first();
        
        if (!$certificat) {
            return redirect()->route('apprenants.dashboard')->with('error', 'Certificat non trouvé ou non autorisé.');
        }
        
        // Récupérer les informations nécessaires
        $module = $certificat->module;
        $niveauApprenant = $apprenant->niveau;
        
        return view('apprenants.certificat', compact('certificat', 'apprenant', 'module', 'niveauApprenant'));
    }
}

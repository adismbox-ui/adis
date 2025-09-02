<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apprenant;
use App\Models\Document;
use App\Models\Formateur;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprenantApiController extends Controller
{
    public function index()
    {
        $apprenants = Apprenant::all();
        return response()->json(['apprenants' => $apprenants], 200);
    }

    public function create(Request $request)
    {
        $selectedModuleId = $request->get('module_id');
        $modules = \App\Models\Module::with('niveau')->orderBy('titre')->get();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        return response()->json([
            'modules' => $modules,
            'niveaux' => $niveaux,
            'selectedModuleId' => $selectedModuleId,
            'user' => $user,
            'apprenant' => $apprenant
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'mobile_money' => 'required|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'moyen_paiement' => 'nullable|string',
        ]);
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant lié à cet utilisateur.'], 401);
        }
        if (isset($data['niveau_id'])) {
            $apprenant->niveau_id = $data['niveau_id'];
            $apprenant->save();
        }
        $inscription = \App\Models\Inscription::create([
            'apprenant_id' => $apprenant->id,
            'module_id' => $data['module_id'],
            'mobile_money' => $data['mobile_money'],
            'moyen_paiement' => $data['moyen_paiement'] ?? null,
            'date_inscription' => now(),
            'statut' => 'en_attente',
        ]);
        return response()->json(['inscription' => $inscription, 'message' => "Votre demande d'inscription a été envoyée. Elle sera validée par l'administrateur après vérification du paiement."], 201);
    }

    public function show(Apprenant $apprenant)
    {
        $apprenant->load('niveau');
        return response()->json(['apprenant' => $apprenant], 200);
    }

    public function edit(Apprenant $apprenant)
    {
        return response()->json(['apprenant' => $apprenant], 200);
    }

    public function update(Request $request, Apprenant $apprenant)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $apprenant->update($data);
        return response()->json(['apprenant' => $apprenant, 'message' => 'Apprenant mis à jour avec succès'], 200);
    }

    public function destroy(Apprenant $apprenant)
    {
        $apprenant->delete();
        return response()->json(null, 204);
    }

    /**
     * Vérifie si tous les modules du niveau actuel de l'apprenant connecté sont validés
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifierModulesValides()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            $apprenant = $user->apprenant;
            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun profil apprenant trouvé pour cet utilisateur'
                ], 404);
            }

            // Utiliser la méthode du modèle Apprenant
            $verification = $apprenant->verifierModulesNiveauValides();

            return response()->json([
                'success' => true,
                'message' => 'Vérification des modules terminée',
                'data' => $verification
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Méthode pour que l'admin crée un apprenant
     */
    public function adminCreateApprenant(Request $request)
    {
        // Vérifier que l'utilisateur connecté est un admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès non autorisé. Seuls les administrateurs peuvent créer des apprenants.'], 403);
        }

        // Validation des données
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6',
            'sexe' => 'required|in:Homme,Femme',
            'categorie' => 'required|in:Enfant,Etudiant,Professionnel',
            'telephone' => 'nullable|string|max:20',
            'niveau_id' => 'nullable|exists:niveaux,id',
        ]);

        try {
            // Créer l'utilisateur
            $utilisateur = \App\Models\Utilisateur::create([
                'prenom' => $data['prenom'],
                'nom' => $data['nom'],
                'email' => $data['email'],
                'mot_de_passe' => bcrypt($data['password']),
                'type_compte' => 'apprenant',
                'sexe' => $data['sexe'],
                'categorie' => $data['categorie'],
                'telephone' => $data['telephone'] ?? null,
                'actif' => true,
                'email_verified_at' => now(), // Email vérifié automatiquement par l'admin
            ]);

            // Créer l'apprenant
            $apprenant = \App\Models\Apprenant::create([
                'utilisateur_id' => $utilisateur->id,
                'niveau_id' => $data['niveau_id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Apprenant créé avec succès par l\'administrateur',
                'utilisateur' => $utilisateur,
                'apprenant' => $apprenant,
                'created_by' => $user->prenom . ' ' . $user->nom
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la création de l\'apprenant: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $apprenant = $user->apprenant;
        $inscriptionsEnCours = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'en cours')->get() : collect();
        $inscriptionsTerminees = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'termine')->get() : collect();
        $certificats = $apprenant ? $apprenant->certificats()->with('module')->get() : collect();
        $notifications = [
            'Nouvelle vidéo ajoutée à "Tajwîd - Initiation"',
            'Votre certificat "Lecture Coranique" est prêt à être téléchargé',
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
        $totalPoints = 0;
        if ($apprenant) {
            $reponses = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)->get();
            foreach ($reponses as $reponse) {
                $question = $reponse->question;
                if ($question && $reponse->reponse === $question->bonne_reponse) {
                    $totalPoints += $question->points ?? 1;
                }
            }
        }
        $pourcentage = $totalPoints;
        if ($pourcentage > 100) $pourcentage = 100;
        $nextNiveau = null;
        if ($apprenant && $apprenant->niveau_id && $totalPoints >= 60) {
            $currentNiveau = $apprenant->niveau;
            $nextNiveau = \App\Models\Niveau::where('ordre', '>', $currentNiveau->ordre ?? 0)
                ->orderBy('ordre')
                ->first();
            if ($nextNiveau) {
                $apprenant->niveau_id = $nextNiveau->id;
                $apprenant->save();
            }
        }
        return response()->json([
            'user' => $user,
            'apprenant' => $apprenant,
            'inscriptionsEnCours' => $inscriptionsEnCours,
            'inscriptionsTerminees' => $inscriptionsTerminees,
            'certificats' => $certificats,
            'notifications' => $notifications,
            'documents' => $documents,
            'documentsGeneraux' => $documentsGeneraux,
            'paiements' => $paiements,
            'modules' => $modules,
            'totalPoints' => $totalPoints,
            'nextNiveau' => $nextNiveau,
            'pourcentage' => $pourcentage
        ], 200);
    }

    /**
     * Récupérer les résultats des questionnaires de l'apprenant
     */
    public function resultatsQuestionnaires()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Accès réservé aux apprenants.'], 401);
        }

        // Récupérer tous les questionnaires auxquels l'apprenant a répondu
        $reponses = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
            ->with(['question.questionnaire.module'])
            ->get()
            ->groupBy('question.questionnaire_id');

        $resultats = [];
        $totalScore = 0;
        $totalQuestions = 0;
        $totalEarnedPoints = 0;
        $totalPoints = 0;

        foreach ($reponses as $questionnaireId => $questionReponses) {
            $questionnaire = $questionReponses->first()->question->questionnaire;
            $questions = $questionnaire->questions;
            
            $score = 0;
            $earnedPoints = 0;
            $questionnaireTotalPoints = 0;
            $incorrects = [];

            foreach ($questions as $question) {
                $reponse = $questionReponses->where('question_id', $question->id)->first();
                $reponseTexte = $reponse ? $reponse->reponse : '';
                $questionPoints = $question->points ?? 1;
                $questionnaireTotalPoints += $questionPoints;

                if ($reponseTexte === $question->bonne_reponse) {
                    $score++;
                    $earnedPoints += $questionPoints;
                } else {
                    $incorrects[] = [
                        'texte' => $question->texte,
                        'bonne_reponse' => $question->bonne_reponse,
                        'votre_reponse' => $reponseTexte ?: 'Aucune réponse',
                        'points' => $questionPoints
                    ];
                }
            }

            $percentage = ($score / $questions->count()) * 100;
            $pointsPercentage = ($earnedPoints / $questionnaireTotalPoints) * 100;

            $resultats[] = [
                'questionnaire_id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'module' => $questionnaire->module ? $questionnaire->module->titre : 'N/A',
                'type_devoir' => $questionnaire->type_devoir,
                'semaine' => $questionnaire->semaine,
                'score' => $score,
                'totalQuestions' => $questions->count(),
                'percentage' => $percentage,
                'earnedPoints' => $earnedPoints,
                'totalPoints' => $questionnaireTotalPoints,
                'pointsPercentage' => $pointsPercentage,
                'message' => count($incorrects) === 0
                    ? "🎉 Bravo ! Toutes vos réponses sont correctes !"
                    : "📊 Certaines réponses sont incorrectes.",
                'incorrects' => $incorrects
            ];

            $totalScore += $score;
            $totalQuestions += $questions->count();
            $totalEarnedPoints += $earnedPoints;
            $totalPoints += $questionnaireTotalPoints;
        }

        // Calculer les totaux globaux
        $globalPercentage = $totalQuestions > 0 ? ($totalScore / $totalQuestions) * 100 : 0;
        $globalPointsPercentage = $totalPoints > 0 ? ($totalEarnedPoints / $totalPoints) * 100 : 0;

        return response()->json([
            'resultats_par_questionnaire' => $resultats,
            'totaux_globaux' => [
                'score' => $totalScore,
                'totalQuestions' => $totalQuestions,
                'percentage' => $globalPercentage,
                'earnedPoints' => $totalEarnedPoints,
                'totalPoints' => $totalPoints,
                'pointsPercentage' => $globalPointsPercentage,
                'message' => $globalPercentage === 100
                    ? "🎉 Excellent ! Vous avez réussi tous vos questionnaires !"
                    : "📊 Résultats globaux de vos questionnaires."
            ]
        ], 200);
    }

    /**
     * Calculer le pourcentage global d'un module spécifique pour l'apprenant connecté
     */
    public function pourcentageModule($moduleId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Accès réservé aux apprenants.'], 401);
        }

        // Vérifier que le module existe
        $module = \App\Models\Module::find($moduleId);
        if (!$module) {
            return response()->json(['error' => 'Module non trouvé.'], 404);
        }

        // Vérifier que l'apprenant a accès au module (inscrit ou payé)
        $moduleIds = [];
        $modulesInscrits = $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray();
        $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
        $moduleIds = collect($modulesInscrits)->merge($modulesPayes)->unique()->toArray();

        if (!in_array($moduleId, $moduleIds)) {
            return response()->json(['error' => 'Vous n\'avez pas accès à ce module.'], 403);
        }

        // Récupérer tous les questionnaires du module
        $questionnaires = \App\Models\Questionnaire::where('module_id', $moduleId)->get();
        
        if ($questionnaires->isEmpty()) {
            return response()->json([
                'module_id' => $moduleId,
                'module_titre' => $module->titre,
                'score' => 0,
                'totalQuestions' => 0,
                'percentage' => 0,
                'earnedPoints' => 0,
                'totalPoints' => 0,
                'pointsPercentage' => 0,
                'questionnaires_completes' => 0,
                'total_questionnaires' => 0,
                'message' => "Aucun questionnaire disponible pour ce module."
            ], 200);
        }

        $totalScore = 0;
        $totalQuestions = 0;
        $totalEarnedPoints = 0;
        $totalPoints = 0;
        $questionnairesCompletes = 0;
        $resultatsParQuestionnaire = [];

        foreach ($questionnaires as $questionnaire) {
            $questions = $questionnaire->questions;
            $reponses = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                ->whereIn('question_id', $questions->pluck('id'))
                ->get();

            if ($reponses->isNotEmpty()) {
                $questionnairesCompletes++;
                $score = 0;
                $earnedPoints = 0;
                $questionnaireTotalPoints = 0;

                foreach ($questions as $question) {
                    $reponse = $reponses->where('question_id', $question->id)->first();
                    $reponseTexte = $reponse ? $reponse->reponse : '';
                    $questionPoints = $question->points ?? 1;
                    $questionnaireTotalPoints += $questionPoints;

                    if ($reponseTexte === $question->bonne_reponse) {
                        $score++;
                        $earnedPoints += $questionPoints;
                    }
                }

                $percentage = ($score / $questions->count()) * 100;
                $pointsPercentage = ($earnedPoints / $questionnaireTotalPoints) * 100;

                $resultatsParQuestionnaire[] = [
                    'questionnaire_id' => $questionnaire->id,
                    'titre' => $questionnaire->titre,
                    'type_devoir' => $questionnaire->type_devoir,
                    'semaine' => $questionnaire->semaine,
                    'score' => $score,
                    'totalQuestions' => $questions->count(),
                    'percentage' => $percentage,
                    'earnedPoints' => $earnedPoints,
                    'totalPoints' => $questionnaireTotalPoints,
                    'pointsPercentage' => $pointsPercentage
                ];

                $totalScore += $score;
                $totalQuestions += $questions->count();
                $totalEarnedPoints += $earnedPoints;
                $totalPoints += $questionnaireTotalPoints;
            }
        }

        // Calculer les pourcentages globaux
        $globalPercentage = $totalQuestions > 0 ? ($totalScore / $totalQuestions) * 100 : 0;
        $globalPointsPercentage = $totalPoints > 0 ? ($totalEarnedPoints / $totalPoints) * 100 : 0;
        $completionPercentage = $questionnaires->count() > 0 ? ($questionnairesCompletes / $questionnaires->count()) * 100 : 0;

        // Validation automatique du module
        $inscription = \App\Models\Inscription::where('apprenant_id', $apprenant->id)
            ->where('module_id', $moduleId)
            ->first();
        
        $moduleValide = false;
        if ($inscription) {
            if ($globalPointsPercentage >= 60 && $inscription->statut !== 'valide') {
                $inscription->statut = 'valide';
                $inscription->save();
                $moduleValide = true;
            } elseif ($globalPointsPercentage < 60 && $inscription->statut === 'valide') {
                $inscription->statut = 'en_attente';
                $inscription->save();
            }
        }

        return response()->json([
            'module_id' => $moduleId,
            'module_titre' => $module->titre,
            'score' => $totalScore,
            'totalQuestions' => $totalQuestions,
            'percentage' => $globalPercentage,
            'earnedPoints' => $totalEarnedPoints,
            'totalPoints' => $totalPoints,
            'pointsPercentage' => $globalPointsPercentage,
            'questionnaires_completes' => $questionnairesCompletes,
            'total_questionnaires' => $questionnaires->count(),
            'completion_percentage' => $completionPercentage,
            'module_valide' => $moduleValide,
            'validation_seuil' => 60,
            'resultats_par_questionnaire' => $resultatsParQuestionnaire,
            'message' => $globalPointsPercentage >= 60 
                ? "🎉 Excellent ! Module validé avec {$globalPointsPercentage}% de réussite !"
                : "📊 Progression dans le module : {$globalPointsPercentage}%"
        ], 200);
    }

    /**
     * Méthode dédiée pour le passage de niveau basé sur la moyenne des modules
     */
    public function passageNiveau()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Accès réservé aux apprenants.'], 401);
        }

        if (!$apprenant->niveau_id) {
            return response()->json(['error' => 'Aucun niveau assigné à l\'apprenant.'], 400);
        }

        $niveauId = $apprenant->niveau_id;
        $modulesNiveau = \App\Models\Module::where('niveau_id', $niveauId)->get();
        
        if ($modulesNiveau->isEmpty()) {
            return response()->json([
                'message' => 'Aucun module disponible pour ce niveau.',
                'niveau_actuel' => $apprenant->niveau,
                'progression_possible' => false
            ], 200);
        }

        $nbModules = $modulesNiveau->count();
        $sommePourcentages = 0;
        $modulesPourcentages = [];
        $modulesNonValides = [];
        $modulesValides = [];

        foreach ($modulesNiveau as $module) {
            // Récupérer tous les questionnaires du module
            $questionnaires = \App\Models\Questionnaire::where('module_id', $module->id)->get();
            
            // Vérifier que tous les questionnaires sont complétés
            $questionnairesCompletes = 0;
            $totalQuestionnaires = $questionnaires->count();
            
            // Si aucun questionnaire n'est créé, le module ne peut pas être validé
            if ($totalQuestionnaires === 0) {
                $moduleComplet = false;
                $moduleValide = false;
            } else {
                // Logique hybride : valider seulement si TOUS les 12 questionnaires sont créés ET complétés
                $moduleComplet = false; // Sera calculé plus tard
                $moduleValide = false; // Sera calculé plus tard
            }
            $questions = collect();
            $pointsPossibles = 0;
            $pointsObtenus = 0;
            
            foreach ($questionnaires as $questionnaire) {
                $questionsQuestionnaire = $questionnaire->questions;
                $questions = $questions->merge($questionsQuestionnaire);
                
                // Vérifier si l'apprenant a répondu correctement à toutes les questions de ce questionnaire
                $questionsReponduesCorrectement = 0;
                $totalQuestionsQuestionnaire = $questionsQuestionnaire->count();
                
                foreach ($questionsQuestionnaire as $q) {
                    $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                        ->where('question_id', $q->id)
                        ->first();
                    
                    if ($reponse && $reponse->reponse === $q->bonne_reponse) {
                        $questionsReponduesCorrectement++;
                    }
                }
                
                if ($questionsReponduesCorrectement === $totalQuestionsQuestionnaire) {
                    $questionnairesCompletes++;
                }
                
                // Calculer les points pour ce questionnaire
                foreach ($questionsQuestionnaire as $q) {
                    $pointsPossibles += $q->points ?? 1;
                    
                    $bonne = $q->bonne_reponse;
                    $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                        ->where('question_id', $q->id)
                        ->value('reponse');
                    if ($reponse === $bonne) {
                        $pointsObtenus += $q->points ?? 1;
                    }
                }
            }
            
            $pourcentageModule = ($pointsPossibles > 0) ? ($pointsObtenus / $pointsPossibles) * 100 : 0;
            
            // Validation avec pondération par type : Module valide selon le système de pourcentages
            if ($totalQuestionnaires > 0) {
                // Calculer le pourcentage selon le type de questionnaire
                $pourcentageCalcule = 0;
                $questionnairesParType = [];
                
                foreach ($questionnaires as $questionnaire) {
                    $type = $questionnaire->type_devoir;
                    
                    // Compter les bonnes réponses
                    $questionsReponduesCorrectement = 0;
                    foreach ($questionnaire->questions as $question) {
                        $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                            ->where('question_id', $question->id)
                            ->first();
                        
                        if ($reponse && $reponse->reponse === $question->bonne_reponse) {
                            $questionsReponduesCorrectement++;
                        }
                    }
                    
                    $totalQuestionsQuestionnaire = $questionnaire->questions->count();
                    $pourcentageQuestionnaire = ($totalQuestionsQuestionnaire > 0) ? ($questionsReponduesCorrectement / $totalQuestionsQuestionnaire) * 100 : 0;
                    
                    // Pondération selon le type
                    $ponderation = 0;
                    switch ($type) {
                        case 'hebdomadaire':
                            $ponderation = 2; // 2% par semaine
                            break;
                        case 'mensuel':
                            $ponderation = 8; // 8% par mois
                            break;
                        case 'final':
                            $ponderation = 66; // 66% final
                            break;
                    }
                    
                    $pourcentageCalcule += ($pourcentageQuestionnaire * $ponderation) / 100;
                    
                    if (!isset($questionnairesParType[$type])) {
                        $questionnairesParType[$type] = [
                            'completes' => 0,
                            'total' => 0,
                            'pourcentage' => 0
                        ];
                    }
                    
                    if ($questionsReponduesCorrectement === $totalQuestionsQuestionnaire) {
                        $questionnairesParType[$type]['completes']++;
                    }
                    $questionnairesParType[$type]['total']++;
                    $questionnairesParType[$type]['pourcentage'] += $pourcentageQuestionnaire;
                }
                
                // Normaliser les pourcentages par type
                foreach ($questionnairesParType as $type => $data) {
                    if ($data['total'] > 0) {
                        $questionnairesParType[$type]['pourcentage'] = $data['pourcentage'] / $data['total'];
                    }
                }
                
                $moduleComplet = ($questionnairesCompletes === $totalQuestionnaires);
                $moduleValide = ($moduleComplet && $pourcentageCalcule >= 60);
                $pourcentageModule = $pourcentageCalcule; // Utiliser le pourcentage pondéré
            }
            
            // Validation automatique du module
            $inscription = \App\Models\Inscription::where('apprenant_id', $apprenant->id)
                ->where('module_id', $module->id)
                ->first();
            
            if ($inscription) {
                if ($moduleValide && $inscription->statut !== 'valide') {
                    $inscription->statut = 'valide';
                    $inscription->save();
                } elseif (!$moduleValide && $inscription->statut === 'valide') {
                    $inscription->statut = 'en_attente';
                    $inscription->save();
                }
            }
            
            if (!$moduleValide) {
                $modulesNonValides[] = [
                    'titre' => $module->titre,
                    'pourcentage' => round($pourcentageModule, 2),
                    'points_obtenus' => $pointsObtenus,
                    'points_possibles' => $pointsPossibles,
                    'questionnaires_completes' => $questionnairesCompletes,
                    'total_questionnaires' => $totalQuestionnaires,
                    'questionnaires_attendus' => 12,
                    'module_complet' => $moduleComplet,
                    'questionnaires_par_type' => $questionnairesParType ?? [],
                    'raison' => $totalQuestionnaires === 0 ? 'Aucun questionnaire créé' : 
                               ($totalQuestionnaires < 12 ? 'Questionnaires manquants (12 requis)' : 
                               (!$moduleComplet ? 'Questionnaires incomplets' : 'Score insuffisant'))
                ];
            } else {
                $modulesValides[] = [
                    'titre' => $module->titre,
                    'pourcentage' => round($pourcentageModule, 2),
                    'points_obtenus' => $pointsObtenus,
                    'points_possibles' => $pointsPossibles,
                    'questionnaires_completes' => $questionnairesCompletes,
                    'total_questionnaires' => $totalQuestionnaires,
                    'questionnaires_attendus' => 12,
                    'questionnaires_par_type' => $questionnairesParType ?? []
                ];
            }
            
            $sommePourcentages += $pourcentageModule;
            $modulesPourcentages[] = [
                'titre' => $module->titre,
                'pourcentage' => round($pourcentageModule, 2),
                'points_obtenus' => $pointsObtenus,
                'points_possibles' => $pointsPossibles,
                'valide' => $moduleValide,
                'complet' => $moduleComplet,
                'questionnaires_completes' => $questionnairesCompletes,
                'total_questionnaires' => $totalQuestionnaires,
                'questionnaires_attendus' => 12,
                'questionnaires_par_type' => $questionnairesParType ?? []
            ];
        }
        
        $moyenneModules = ($nbModules > 0) ? $sommePourcentages / $nbModules : 0;
        $pourcentage = round($moyenneModules, 2);
        
        // Progression de niveau - OBLIGATION de compléter tous les questionnaires
        $nextNiveau = null;
        $progressionPossible = false;
        $message = '';
        
        // Vérifier que tous les modules sont validés ET que la moyenne est ≥ 60%
        $tousModulesValides = empty($modulesNonValides);
        $moyenneSuffisante = ($pourcentage >= 60);
        
        if ($tousModulesValides && $moyenneSuffisante) {
            $currentNiveau = $apprenant->niveau;
            $nextNiveau = \App\Models\Niveau::where('ordre', '>', $currentNiveau->ordre ?? 0)
                ->orderBy('ordre')
                ->first();
            
            if ($nextNiveau) {
                $apprenant->niveau_id = $nextNiveau->id;
                $apprenant->save();
                $progressionPossible = true;
                $message = "🎉 Félicitations ! Vous avez progressé au niveau {$nextNiveau->nom} !";
            } else {
                $message = "🎉 Excellent ! Vous avez atteint le niveau maximum !";
            }
        } else {
            if (!$moyenneSuffisante) {
                $message = "📊 Moyenne insuffisante ({$pourcentage}% < 60%). Continuez vos efforts !";
            } elseif (!$tousModulesValides) {
                $message = "⚠️ Certains modules ne sont pas encore validés. Vous devez compléter TOUS les questionnaires (12 semaines) de chaque module ET obtenir au moins 60% de réussite.";
            } else {
                $message = "⚠️ Progression impossible. Vérifiez que tous les modules sont complétés et validés.";
            }
        }

        return response()->json([
            'niveau_actuel' => $apprenant->niveau,
            'niveau_suivant' => $nextNiveau,
            'moyenne_modules' => $pourcentage,
            'seuil_requis' => 60,
            'nb_modules' => $nbModules,
            'modules_valides' => $modulesValides,
            'modules_non_valides' => $modulesNonValides,
            'modules_pourcentages' => $modulesPourcentages,
            'progression_possible' => $progressionPossible,
            'message' => $message
        ], 200);
    }

    public function testInscription()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $apprenant = $user->apprenant;
        $inscriptionsEnCours = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'en cours')->get() : collect();
        $inscriptionsTerminees = $apprenant ? $apprenant->inscriptions()->with('module')->where('statut', 'termine')->get() : collect();
        $certificats = $apprenant ? $apprenant->certificats()->with('module')->get() : collect();
        $notifications = [
            'Nouvelle vidéo ajoutée à "Tajwîd - Initiation"',
            'Votre certificat "Lecture Coranique" est prêt à être téléchargé',
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
        return response()->json([
            'user' => $user,
            'apprenant' => $apprenant,
            'inscriptionsEnCours' => $inscriptionsEnCours,
            'inscriptionsTerminees' => $inscriptionsTerminees,
            'certificats' => $certificats,
            'notifications' => $notifications,
            'documents' => $documents,
            'documentsGeneraux' => $documentsGeneraux,
            'paiements' => $paiements,
            'modules' => $modules
        ], 200);
    }

    public function gada()
    {
        // Identique à dashboard
        return $this->dashboard();
    }

    public function questionnaires()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $apprenant = $user->apprenant;
        $modulesInscrits = $apprenant ? $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray() : [];
        $modulesPayes = $apprenant ? $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray() : [];
        $moduleIds = collect($modulesInscrits)->merge($modulesPayes)->unique()->toArray();
        $questionnaires = \App\Models\Questionnaire::with(['questions', 'module'])->whereIn('module_id', $moduleIds)->get();
        return response()->json(['questionnaires' => $questionnaires], 200);
    }

    public function repondreQuestionnaire(\App\Models\Questionnaire $questionnaire)
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Non authentifié'], 401);
        $apprenant = $user->apprenant;
        $questionnaire->load('questions');
        return response()->json([
            'user' => $user,
            'apprenant' => $apprenant,
            'questionnaire' => $questionnaire
        ], 200);
    }

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
                'choix' => json_decode($q->choix, true),
            ];
            if ($trouve) $score++;
        }
        $total = count($questionnaire->questions);
        return response()->json([
            'questionnaire' => $questionnaire,
            'resultats' => $resultats,
            'score' => $score,
            'total' => $total
        ], 200);
    }

    public function maPage()
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Non authentifié'], 401);
        return response()->json(['user' => $user], 200);
    }

    public function certificatTest(Request $request)
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Non authentifié'], 401);
        $apprenant = $user->apprenant;
        $modulesValidés = $apprenant ? $apprenant->inscriptions()->where('statut', 'valide')->with('module')->get() : collect();
        $certificats = $apprenant ? $apprenant->certificats()->with('module')->get() : collect();
        $niveauCertificat = null;
        $niveauPrecedent = null;
        if ($apprenant && $apprenant->niveau) {
            $niveauActuel = $apprenant->niveau;
            if ($niveauActuel && $niveauActuel->ordre > 1) {
                $niveauPrecedent = \App\Models\Niveau::where('ordre', $niveauActuel->ordre - 1)->first();
                if ($niveauPrecedent) {
                    $niveauCertificat = $niveauPrecedent;
                }
            }
        }
        return response()->json([
            'apprenant' => $apprenant,
            'modulesValidés' => $modulesValidés,
            'certificats' => $certificats,
            'niveauCertificat' => $niveauCertificat
        ], 200);
    }

    /**
     * Génère un certificat PDF et le retourne en base64 dans un JSON
     */
    public function generateCertificatPDF(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant lié à cet utilisateur.'], 404);
        }

        // Validation des paramètres
        $request->validate([
            'type' => 'required|in:niveau,module',
            'module_id' => 'required_if:type,module|exists:modules,id',
        ]);

        $type = $request->input('type');
        $pdfBase64 = null;
        $certificat = null;
        $filename = null;

        try {
            if ($type === 'niveau') {
                // Génération du certificat de niveau
                $niveauCertificat = null;
                if ($apprenant && $apprenant->niveau) {
                    $niveauActuel = $apprenant->niveau;
                    if ($niveauActuel && $niveauActuel->ordre > 1) {
                        $niveauPrecedent = \App\Models\Niveau::where('ordre', $niveauActuel->ordre - 1)->first();
                        if ($niveauPrecedent) {
                            $niveauCertificat = $niveauPrecedent;
                        }
                    }
                }

                if (!$niveauCertificat) {
                    return response()->json([
                        'error' => 'Aucun certificat de niveau disponible. Vous devez avoir validé au moins un niveau.',
                        'niveau_actuel' => $apprenant->niveau
                    ], 400);
                }

                // Créer ou récupérer le certificat en base de données
                $certificat = \App\Models\Certificat::where('apprenant_id', $apprenant->id)
                    ->where('module_id', null)
                    ->where('titre', 'Certificat de niveau ' . $niveauCertificat->nom)
                    ->first();

                if (!$certificat) {
                    $certificat = \App\Models\Certificat::create([
                        'apprenant_id' => $apprenant->id,
                        'module_id' => null,
                        'titre' => 'Certificat de niveau ' . $niveauCertificat->nom,
                        'date_obtention' => now(),
                    ]);
                }

                // Générer le PDF
                $pdf = Pdf::loadView('apprenants.certificat-niveau-pdf', [
                    'apprenant' => $apprenant,
                    'niveau' => $niveauCertificat
                ]);

                $pdfBase64 = base64_encode($pdf->output());
                $filename = 'certificat-niveau-' . $apprenant->id . '-' . $niveauCertificat->id . '.pdf';

            } elseif ($type === 'module') {
                // Génération du certificat de module
                $moduleId = $request->input('module_id');
                $module = \App\Models\Module::find($moduleId);

                if (!$module) {
                    return response()->json(['error' => 'Module non trouvé.'], 404);
                }

                // Vérifier que l'apprenant a accès à ce module
                $modulesInscrits = $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray();
                $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
                $modulesAccessibles = array_unique(array_merge($modulesInscrits, $modulesPayes));

                if (!in_array($moduleId, $modulesAccessibles)) {
                    return response()->json([
                        'error' => 'Vous n\'avez pas accès à ce module.',
                        'module_id' => $moduleId,
                        'modules_accessibles' => $modulesAccessibles
                    ], 403);
                }

                // Créer ou récupérer le certificat en base de données
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

                // Générer le PDF
                $pdf = Pdf::loadView('apprenants.certificat-module-pdf', [
                    'apprenant' => $apprenant,
                    'module' => $module
                ]);

                $pdfBase64 = base64_encode($pdf->output());
                $filename = 'certificat-module-' . $apprenant->id . '-' . $module->id . '.pdf';
            }

            return response()->json([
                'success' => true,
                'message' => 'Certificat généré avec succès',
                'certificat' => [
                    'id' => $certificat->id,
                    'titre' => $certificat->titre,
                    'date_obtention' => $certificat->date_obtention,
                    'type' => $type,
                    'module_id' => $certificat->module_id,
                ],
                'pdf' => [
                    'base64' => $pdfBase64,
                    'filename' => $filename,
                    'size_bytes' => strlen($pdfBase64),
                    'content_type' => 'application/pdf'
                ],
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->utilisateur->nom ?? '',
                    'prenom' => $apprenant->utilisateur->prenom ?? '',
                    'email' => $apprenant->utilisateur->email ?? '',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la génération du certificat',
                'message' => $e->getMessage(),
                'type' => $type
            ], 500);
        }
    }

    public function moduleTest()
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Non authentifié'], 401);
        $apprenant = $user->apprenant;
        $modulesInscrits = $apprenant ? $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray() : [];
        $modulesPayes = $apprenant ? $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray() : [];
        $moduleIds = collect($modulesInscrits)->merge($modulesPayes)->unique()->toArray();
        $modules = \App\Models\Module::whereIn('id', $moduleIds)->orderBy('titre')->get();
        return response()->json(['apprenant' => $apprenant, 'modules' => $modules], 200);
    }

    public function documentsTest(Request $request)
    {
        $user = auth()->user();
        if (!$user) return response()->json(['error' => 'Non authentifié'], 401);
        $apprenant = $user->apprenant;
        $modulesInscrits = $apprenant ? $apprenant->inscriptions()->where('statut', 'valide')->with('module')->get()->pluck('module') : collect();
        $moduleIds = $modulesInscrits->pluck('id');
        $niveauId = $apprenant->niveau_id;
        $semaine = $request->input('semaine', null);
        $moduleFiltre = $request->input('module_id', null);
        $niveauFiltre = $request->input('niveau_id', null);
        $documentsQuery = \App\Models\Document::with(['module', 'formateur.utilisateur']);
        $documentsQuery->whereIn('module_id', $moduleIds);
        if ($semaine) {
            $documentsQuery->where('semaine', $semaine);
        }
        if ($moduleFiltre) {
            $documentsQuery->where('module_id', $moduleFiltre);
        }
        if ($niveauFiltre) {
            $documentsQuery->where('niveau_id', $niveauFiltre);
        }
        $documents = $documentsQuery->get();
        $semainesDisponibles = \App\Models\Document::whereIn('module_id', $moduleIds)->distinct()->pluck('semaine')->filter();
        $modulesDisponibles = $modulesInscrits;
        $niveauxDisponibles = $modulesInscrits->pluck('niveau')->unique('id')->filter();
        return response()->json([
            'apprenant' => $apprenant,
            'modulesInscrits' => $modulesInscrits,
            'documents' => $documents,
            'semainesDisponibles' => $semainesDisponibles,
            'semaine' => $semaine,
            'modulesDisponibles' => $modulesDisponibles,
            'moduleFiltre' => $moduleFiltre,
            'niveauxDisponibles' => $niveauxDisponibles,
            'niveauFiltre' => $niveauFiltre
        ], 200);
    }

    public function infosApprenant()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant lié à cet utilisateur.'], 404);
        }

        $utilisateur = $apprenant->utilisateur;
        if (!$utilisateur) {
            return response()->json(['error' => 'Aucun utilisateur lié à cet apprenant.'], 404);
        }

        return response()->json([
            'nom' => $utilisateur->nom,
            'prenom' => $utilisateur->prenom,
            'email' => $utilisateur->email,
            'telephone' => $utilisateur->telephone,
        ], 200);
    }

    public function listeApprenantsInfos()
    {
        $user = auth()->user();
        // Optionnel : vérifier que c'est un admin
        // if (!$user || $user->type_compte !== 'admin') {
        //     return response()->json(['error' => 'Non autorisé'], 403);
        // }

        $apprenants = \App\Models\Apprenant::with('utilisateur')->get();

        $result = $apprenants->map(function($apprenant) {
            return [
                'nom' => $apprenant->utilisateur->nom ?? null,
                'prenom' => $apprenant->utilisateur->prenom ?? null,
                'email' => $apprenant->utilisateur->email ?? null,
                'telephone' => $apprenant->utilisateur->telephone ?? null,
            ];
        });

        return response()->json(['apprenants' => $result], 200);
    }

    /**
     * Récupère tous les modules de l'apprenant connecté
     * @return \Illuminate\Http\JsonResponse
     */
    public function mesModules()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant lié à cet utilisateur.'], 404);
        }

        // Vérifier si l'apprenant a un niveau assigné
        if (!$apprenant->niveau_id) {
            return response()->json([
                'error' => 'Aucun niveau assigné à cet apprenant',
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'niveau_id' => $apprenant->niveau_id
                ]
            ], 400);
        }

        // Récupérer tous les modules du niveau de l'apprenant
        $modules = \App\Models\Module::where('niveau_id', $apprenant->niveau_id)
            ->with(['niveau.formateur.utilisateur'])
            ->orderBy('date_debut')
            ->get();

        // Formater la réponse avec les informations d'inscription et de paiement
        $modulesFormates = $modules->map(function ($module) use ($apprenant) {
            // Vérifier l'inscription
            $inscription = $apprenant->inscriptions()
                ->where('module_id', $module->id)
                ->first();

            // Vérifier le paiement
            $paiement = $apprenant->paiements()
                ->where('module_id', $module->id)
                ->where('statut', 'valide')
                ->first();

            // Déterminer le statut d'accès
            $statutAcces = 'non_inscrit';
            $peutAcceder = false;
            
            if ($inscription) {
                $statutAcces = $inscription->statut;
                $peutAcceder = $inscription->statut === 'valide';
            } elseif ($paiement) {
                $statutAcces = 'paye';
                $peutAcceder = true;
            }

            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'discipline' => $module->discipline,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'prix' => $module->prix,
                'niveau' => [
                    'id' => $module->niveau->id ?? null,
                    'nom' => $module->niveau->nom ?? null,
                    'ordre' => $module->niveau->ordre ?? null,
                    'formateur' => $module->niveau->formateur ? [
                        'id' => $module->niveau->formateur->id ?? null,
                        'nom' => $module->niveau->formateur->utilisateur->nom ?? null,
                        'prenom' => $module->niveau->formateur->utilisateur->prenom ?? null,
                        'email' => $module->niveau->formateur->utilisateur->email ?? null,
                        'specialite' => $module->niveau->formateur->specialite ?? null,
                        'valide' => $module->niveau->formateur->valide ?? false,
                    ] : null,
                    'lien_meet' => $module->niveau->lien_meet ?? null,
                    'session_formation' => $module->niveau->sessionFormation ? [
                        'id' => $module->niveau->sessionFormation->id ?? null,
                        'nom' => $module->niveau->sessionFormation->nom ?? null,
                        'date_debut' => $module->niveau->sessionFormation->date_debut ?? null,
                        'date_fin' => $module->niveau->sessionFormation->date_fin ?? null,
                    ] : null,
                ],
                'statut_acces' => $statutAcces,
                'peut_acceder' => $peutAcceder,
                'inscription' => $inscription ? [
                    'id' => $inscription->id,
                    'date_inscription' => $inscription->date_inscription,
                    'statut' => $inscription->statut,
                    'mobile_money' => $inscription->mobile_money,
                    'moyen_paiement' => $inscription->moyen_paiement,
                    'session_formation_id' => $inscription->session_formation_id
                ] : null,
                'paiement' => $paiement ? [
                    'id' => $paiement->id,
                    'transaction_id' => $paiement->transaction_id,
                    'amount' => $paiement->amount,
                    'currency' => $paiement->currency,
                    'statut' => $paiement->statut,
                    'payment_method' => $paiement->payment_method,
                    'created_at' => $paiement->created_at
                ] : null,
            ];
        });

        return response()->json([
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                    'ordre' => $apprenant->niveau->ordre
                ] : null
            ],
            'modules' => $modulesFormates,
            'total' => $modulesFormates->count(),
            'modules_inscrits' => $modulesFormates->where('statut_acces', 'valide')->count(),
            'modules_payes' => $modulesFormates->where('statut_acces', 'paye')->count(),
            'modules_disponibles' => $modulesFormates->where('statut_acces', 'non_inscrit')->count()
        ], 200);
    }

    /**
     * Récupère le niveau de l'apprenant connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNiveauApprenantConnecte()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant lié à cet utilisateur.'], 404);
        }

        $niveau = $apprenant->niveau;
        if (!$niveau) {
            return response()->json(['error' => 'Aucun niveau assigné à cet apprenant.'], 404);
        }

        return response()->json([
            'success' => true,
            'niveau' => [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description,
                'ordre' => $niveau->ordre,
                'prix' => $niveau->prix,
                'actif' => $niveau->actif
            ]
        ], 200);
    }

    /**
     * Récupère les informations complètes du niveau de l'apprenant connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNiveauApprenantConnecteComplet()
    {
        $utilisateur = auth()->user();
        if (!$utilisateur) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = Apprenant::where('utilisateur_id', $utilisateur->id)->first();
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }
        
        $niveau = $apprenant->niveau;
        if (!$niveau) {
            return response()->json(['error' => 'Aucun niveau assigné à cet apprenant'], 404);
        }
        
        return response()->json([
            'success' => true,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $apprenant->nom,
                'prenom' => $apprenant->prenom,
                'email' => $utilisateur->email
            ],
            'niveau' => [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description,
                'ordre' => $niveau->ordre,
                'prix' => $niveau->prix
            ]
        ], 200);
    }

    /**
     * Récupérer les supports de cours des modules de l'apprenant connecté
     */
    public function mesSupportsCours()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }
        
        // Récupérer les modules où l'apprenant est inscrit (statut valide) ou a payé
        $modulesInscrits = $apprenant->inscriptions()
            ->where('statut', 'valide')
            ->pluck('module_id')
            ->toArray();
            
        $modulesPayes = $apprenant->paiements()
            ->where('statut', 'valide')
            ->pluck('module_id')
            ->toArray();
        
        // Combiner et dédupliquer les modules
        $moduleIds = array_unique(array_merge($modulesInscrits, $modulesPayes));
        
        if (empty($moduleIds)) {
            return response()->json([
                'success' => true,
                'message' => 'Aucun module accessible trouvé. Vous devez être inscrit ou avoir payé un module pour accéder aux supports de cours.',
                'total_modules' => 0,
                'modules_avec_supports' => 0,
                'modules_accessibles' => 0,
                'supports' => []
            ], 200);
        }
        
        // Récupérer seulement les modules accessibles
        $modulesAccessibles = \App\Models\Module::with(['niveau.formateur.utilisateur:id,nom,prenom'])
            ->whereIn('id', $moduleIds)
            ->get();
        
        $supports = $modulesAccessibles->map(function ($module) use ($apprenant) {
            // Vérifier le statut d'accès
            $inscription = $apprenant->inscriptions()->where('module_id', $module->id)->first();
            $paiement = $apprenant->paiements()->where('module_id', $module->id)->first();
            
            $statutAcces = 'non_inscrit';
            if ($inscription) {
                $statutAcces = $inscription->statut;
            } elseif ($paiement) {
                $statutAcces = $paiement->statut;
            }
            
            return [
                'module_id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom
                ] : null,
                'formateur' => $module->niveau && $module->niveau->formateur && $module->niveau->formateur->utilisateur ? [
                    'id' => $module->niveau->formateur->id,
                    'nom' => $module->niveau->formateur->utilisateur->nom,
                    'prenom' => $module->niveau->formateur->utilisateur->prenom
                ] : null,
                'statut_acces' => $statutAcces,
                'supports' => [
                    'support_cours' => $module->support ? url('storage/' . $module->support) : null,
                    'audio' => $module->audio ? url('storage/' . $module->audio) : null,
                    'lien_externe' => $module->lien
                ],
                'a_du_support' => !empty($module->support) || !empty($module->audio) || !empty($module->lien)
            ];
        });
        
        return response()->json([
            'success' => true,
            'total_modules' => $modulesAccessibles->count(),
            'modules_avec_supports' => $supports->where('a_du_support', true)->count(),
            'modules_accessibles' => count($moduleIds),
            'supports' => $supports
        ], 200);
    }

    /**
     * Récupérer les supports de cours d'un module spécifique
     */
    public function supportsModule($moduleId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }
        
        // Vérifier si l'apprenant a accès à ce module
        $aAcces = $apprenant->inscriptions()
            ->where('module_id', $moduleId)
            ->where('statut', 'valide')
            ->exists();
            
        if (!$aAcces) {
            $aAcces = $apprenant->paiements()
                ->where('module_id', $moduleId)
                ->where('statut', 'valide')
                ->exists();
        }
        
        if (!$aAcces) {
            return response()->json(['error' => 'Vous n\'avez pas accès à ce module'], 403);
        }
        
        $module = \App\Models\Module::with(['niveau.formateur.utilisateur:id,nom,prenom'])
            ->find($moduleId);
            
        if (!$module) {
            return response()->json(['error' => 'Module non trouvé'], 404);
        }
        
        return response()->json([
            'success' => true,
            'module' => [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom
                ] : null,
                'formateur' => $module->formateur ? [
                    'id' => $module->formateur->id,
                    'nom' => $module->formateur->utilisateur->nom,
                    'prenom' => $module->formateur->utilisateur->prenom
                ] : null,
                'supports' => [
                    'support_cours' => $module->support ? url('storage/' . $module->support) : null,
                    'audio' => $module->audio ? url('storage/' . $module->audio) : null,
                    'lien_externe' => $module->lien
                ]
            ]
        ], 200);
    }

    /**
     * Diagnostic pour voir les modules et leurs statuts
     */
    public function diagnosticModules()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }
        
        // Récupérer toutes les inscriptions
        $inscriptions = $apprenant->inscriptions()->with('module')->get();
        
        // Récupérer tous les paiements
        $paiements = $apprenant->paiements()->with('module')->get();
        
        // Récupérer tous les modules disponibles
        $tousModules = \App\Models\Module::with(['formateur.utilisateur:id,nom,prenom', 'niveau:id,nom'])->get();
        
        return response()->json([
            'success' => true,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $apprenant->nom,
                'prenom' => $apprenant->prenom
            ],
            'statistiques' => [
                'total_modules_disponibles' => $tousModules->count(),
                'total_inscriptions' => $inscriptions->count(),
                'total_paiements' => $paiements->count(),
                'inscriptions_par_statut' => $inscriptions->groupBy('statut')->map->count(),
                'paiements_par_statut' => $paiements->groupBy('statut')->map->count()
            ],
            'inscriptions' => $inscriptions->map(function($inscription) {
                return [
                    'id' => $inscription->id,
                    'module_id' => $inscription->module_id,
                    'module_titre' => $inscription->module ? $inscription->module->titre : 'N/A',
                    'statut' => $inscription->statut,
                    'date_inscription' => $inscription->date_inscription
                ];
            }),
            'paiements' => $paiements->map(function($paiement) {
                return [
                    'id' => $paiement->id,
                    'module_id' => $paiement->module_id,
                    'module_titre' => $paiement->module ? $paiement->module->titre : 'N/A',
                    'statut' => $paiement->statut,
                    'montant' => $paiement->montant,
                    'date_paiement' => $paiement->date_paiement
                ];
            }),
            'modules_disponibles' => $tousModules->map(function($module) {
                return [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'niveau' => $module->niveau ? $module->niveau->nom : 'N/A',
                    'formateur' => $module->formateur ? $module->formateur->utilisateur->nom . ' ' . $module->formateur->utilisateur->prenom : 'N/A',
                    'a_support' => !empty($module->support),
                    'a_audio' => !empty($module->audio),
                    'a_lien' => !empty($module->lien)
                ];
            })
        ], 200);
    }

    /**
     * Récupérer toutes les questions disponibles pour les modules de l'apprenant inscrit
     */
    public function mesQuestionsDisponibles()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }
        
        // Récupérer les modules où l'apprenant est inscrit (statut valide) ou a payé
        $modulesInscrits = $apprenant->inscriptions()
            ->where('statut', 'valide')
            ->pluck('module_id')
            ->toArray();
            
        $modulesPayes = $apprenant->paiements()
            ->where('statut', 'valide')
            ->pluck('module_id')
            ->toArray();
        
        // Combiner et dédupliquer les modules
        $moduleIds = array_unique(array_merge($modulesInscrits, $modulesPayes));
        
        if (empty($moduleIds)) {
            return response()->json([
                'success' => true,
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->nom,
                    'prenom' => $apprenant->prenom,
                    'email' => $user->email,
                    'niveau' => $apprenant->niveau ? [
                        'id' => $apprenant->niveau->id,
                        'nom' => $apprenant->niveau->nom,
                        'description' => $apprenant->niveau->description,
                        'ordre' => $apprenant->niveau->ordre
                    ] : null
                ],
                'message' => 'Aucun module accessible trouvé. Vous devez être inscrit ou avoir payé un module pour accéder aux questions.',
                'statistiques' => [
                    'total_questions' => 0,
                    'total_questionnaires' => 0,
                    'total_modules' => 0,
                    'modules_accessibles' => 0
                ],
                'modules_accessibles' => [],
                'questions_par_module' => []
            ], 200);
        }
        
        // Récupérer seulement les modules accessibles avec leurs questionnaires et questions
        $modulesAccessibles = \App\Models\Module::with([
            'niveau:id,nom',
            'formateur.utilisateur:id,nom,prenom',
            'questionnaires.questions'
        ])->whereIn('id', $moduleIds)->get();
        
        // Organiser les questions par module et questionnaire
        $questionsOrganisees = $modulesAccessibles->map(function ($module) use ($apprenant) {
            // Vérifier le statut d'accès
            $inscription = $apprenant->inscriptions()->where('module_id', $module->id)->first();
            $paiement = $apprenant->paiements()->where('module_id', $module->id)->first();
            
            $statutAcces = 'non_inscrit';
            if ($inscription) {
                $statutAcces = $inscription->statut;
            } elseif ($paiement) {
                $statutAcces = $paiement->statut;
            }
            
            $questionnaires = $module->questionnaires->map(function ($questionnaire) {
                return [
                    'questionnaire' => [
                        'id' => $questionnaire->id,
                        'titre' => $questionnaire->titre,
                        'description' => $questionnaire->description,
                        'type_devoir' => $questionnaire->type_devoir,
                        'minutes' => $questionnaire->minutes,
                        'semaine' => $questionnaire->semaine
                    ],
                    'questions' => $questionnaire->questions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'texte' => $question->texte,
                            'choix' => json_decode($question->choix, true),
                            'bonne_reponse' => $question->bonne_reponse,
                            'points' => $question->points,
                            'questionnaire_id' => $question->questionnaire_id
                        ];
                    })
                ];
            });
            
            return [
                'module' => [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'description' => $module->description,
                    'niveau' => $module->niveau ? [
                        'id' => $module->niveau->id,
                        'nom' => $module->niveau->nom
                    ] : null,
                    'formateur' => $module->niveau && $module->niveau->formateur && $module->niveau->formateur->utilisateur ? [
                        'id' => $module->niveau->formateur->id,
                        'nom' => $module->niveau->formateur->utilisateur->nom,
                        'prenom' => $module->niveau->formateur->utilisateur->prenom
                    ] : null
                ],
                'statut_acces' => $statutAcces,
                'accessible' => true,
                'questionnaires' => $questionnaires
            ];
        });
        
        $totalQuestions = $modulesAccessibles->sum(function ($module) {
            return $module->questionnaires->sum(function ($questionnaire) {
                return $questionnaire->questions->count();
            });
        });
        
        $totalQuestionnaires = $modulesAccessibles->sum(function ($module) {
            return $module->questionnaires->count();
        });
        
        return response()->json([
            'success' => true,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $apprenant->nom,
                'prenom' => $apprenant->prenom,
                'email' => $user->email,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                    'description' => $apprenant->niveau->description,
                    'ordre' => $apprenant->niveau->ordre
                ] : null
            ],
            'statistiques' => [
                'total_questions' => $totalQuestions,
                'total_questionnaires' => $totalQuestionnaires,
                'total_modules' => $modulesAccessibles->count(),
                'modules_accessibles' => count($moduleIds)
            ],
            'modules_accessibles' => $moduleIds,
            'questions_par_module' => $questionsOrganisees
        ], 200);
    }

    /**
     * Récupérer toutes les questions disponibles (version de test - sans vérification d'accès)
     */
    public function toutesQuestionsDisponibles()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }
        
        // Récupérer tous les questionnaires avec leurs questions
        $questionnaires = \App\Models\Questionnaire::with([
            'module.niveau', 
            'questions'
        ])->get();
        
        // Organiser les questions par module et questionnaire
        $questionsOrganisees = $questionnaires->groupBy('module_id')->map(function ($questionnairesModule, $moduleId) {
            $firstQuestionnaire = $questionnairesModule->first();
            $module = $firstQuestionnaire->module;
            
            // Vérifier si le module existe
            if (!$module) {
                return [
                    'module' => [
                        'id' => null,
                        'titre' => 'Module non trouvé',
                        'description' => 'Module supprimé ou inexistant',
                        'niveau' => null,
                        'formateur' => null
                    ],
                    'questionnaires' => $questionnairesModule->map(function ($questionnaire) {
                        return [
                            'questionnaire' => [
                                'id' => $questionnaire->id,
                                'titre' => $questionnaire->titre,
                                'description' => $questionnaire->description,
                                'type_devoir' => $questionnaire->type_devoir,
                                'minutes' => $questionnaire->minutes,
                                'semaine' => $questionnaire->semaine
                            ],
                            'questions' => $questionnaire->questions->map(function ($question) {
                                return [
                                    'id' => $question->id,
                                    'texte' => $question->texte,
                                    'choix' => json_decode($question->choix, true),
                                    'bonne_reponse' => $question->bonne_reponse,
                                    'points' => $question->points,
                                    'questionnaire_id' => $question->questionnaire_id
                                ];
                            })
                        ];
                    })
                ];
            }
            
            return [
                'module' => [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'description' => $module->description,
                    'niveau' => $module->niveau ? [
                        'id' => $module->niveau->id,
                        'nom' => $module->niveau->nom
                    ] : null,
                    'formateur' => $module->niveau && $module->niveau->formateur && $module->niveau->formateur->utilisateur ? [
                        'id' => $module->niveau->formateur->id,
                        'nom' => $module->niveau->formateur->utilisateur->nom,
                        'prenom' => $module->niveau->formateur->utilisateur->prenom
                    ] : null
                ],
                'questionnaires' => $questionnairesModule->map(function ($questionnaire) {
                    return [
                        'questionnaire' => [
                            'id' => $questionnaire->id,
                            'titre' => $questionnaire->titre,
                            'description' => $questionnaire->description,
                            'type_devoir' => $questionnaire->type_devoir,
                            'minutes' => $questionnaire->minutes,
                            'semaine' => $questionnaire->semaine
                        ],
                        'questions' => $questionnaire->questions->map(function ($question) {
                            return [
                                'id' => $question->id,
                                'texte' => $question->texte,
                                'choix' => json_decode($question->choix, true),
                                'bonne_reponse' => $question->bonne_reponse,
                                'points' => $question->points,
                                'questionnaire_id' => $question->questionnaire_id
                            ];
                        })
                    ];
                })
            ];
        })->values();
        
        $totalQuestions = $questionnaires->sum(function ($q) {
            return $q->questions->count();
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Toutes les questions disponibles (version de test)',
            'total_questions' => $totalQuestions,
            'total_questionnaires' => $questionnaires->count(),
            'total_modules' => $questionnaires->groupBy('module_id')->count(),
            'questions_par_module' => $questionsOrganisees
        ], 200);
    }

    /**
     * Récupérer les questions d'un module spécifique
     */
    public function questionsModule($moduleId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }
        
        // Vérifier si l'apprenant a accès à ce module
        $aAcces = $apprenant->inscriptions()
            ->where('module_id', $moduleId)
            ->where('statut', 'valide')
            ->exists();
            
        if (!$aAcces) {
            $aAcces = $apprenant->paiements()
                ->where('module_id', $moduleId)
                ->where('statut', 'valide')
                ->exists();
        }
        
        if (!$aAcces) {
            return response()->json(['error' => 'Vous n\'avez pas accès à ce module'], 403);
        }
        
        // Récupérer le module avec ses questionnaires et questions
        $module = \App\Models\Module::with([
            'niveau:id,nom',
            'niveau.formateur.utilisateur:id,nom,prenom',
            'questionnaires.questions'
        ])->find($moduleId);
        
        if (!$module) {
            return response()->json(['error' => 'Module non trouvé'], 404);
        }
        
        $questionnaires = $module->questionnaires->map(function ($questionnaire) {
            return [
                'questionnaire' => [
                    'id' => $questionnaire->id,
                    'titre' => $questionnaire->titre,
                    'description' => $questionnaire->description,
                    'type_devoir' => $questionnaire->type_devoir,
                    'minutes' => $questionnaire->minutes,
                    'semaine' => $questionnaire->semaine
                ],
                'questions' => $questionnaire->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'texte' => $question->texte,
                        'choix' => json_decode($question->choix, true),
                        'bonne_reponse' => $question->bonne_reponse,
                        'points' => $question->points
                    ];
                })
            ];
        });
        
        return response()->json([
            'success' => true,
            'module' => [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom
                ] : null,
                'formateur' => $module->formateur ? [
                    'id' => $module->formateur->id,
                    'nom' => $module->formateur->utilisateur->nom,
                    'prenom' => $module->formateur->utilisateur->prenom
                ] : null
            ],
            'total_questionnaires' => $questionnaires->count(),
            'total_questions' => $questionnaires->sum(function ($q) {
                return count($q['questions']);
            }),
            'questionnaires' => $questionnaires
        ], 200);
    }

    /**
     * Récupérer tous les modules disponibles pour le niveau de l'apprenant connecté
     */
    public function modulesDisponibles()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }

        if (!$apprenant->niveau_id) {
            return response()->json([
                'error' => 'Aucun niveau assigné à cet apprenant',
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->nom,
                    'prenom' => $apprenant->prenom,
                    'niveau_id' => $apprenant->niveau_id
                ]
            ], 400);
        }

        // Récupérer tous les modules du niveau de l'apprenant
        $modules = \App\Models\Module::where('niveau_id', $apprenant->niveau_id)
            ->with(['niveau.formateur.utilisateur', 'niveau'])
            ->orderBy('date_debut')
            ->get();

        $modulesFormates = $modules->map(function ($module) use ($apprenant) {
            // Vérifier si l'apprenant est inscrit
            $inscription = $apprenant->inscriptions()
                ->where('module_id', $module->id)
                ->first();

            // Vérifier si l'apprenant a payé
            $paiement = $apprenant->paiements()
                ->where('module_id', $module->id)
                ->where('statut', 'valide')
                ->first();

            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'discipline' => $module->discipline,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'lien' => $module->lien,
                'support' => $module->support,
                'audio' => $module->audio,
                'certificat' => $module->certificat,
                'prix' => $module->prix,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                    'ordre' => $module->niveau->ordre
                ] : null,
                'formateur' => $module->niveau && $module->niveau->formateur && $module->niveau->formateur->utilisateur ? [
                    'id' => $module->niveau->formateur->id,
                    'nom' => $module->niveau->formateur->utilisateur->nom,
                    'prenom' => $module->niveau->formateur->utilisateur->prenom,
                    'email' => $module->niveau->formateur->utilisateur->email
                ] : null,
                'statut_acces' => [
                    'inscrit' => $inscription ? true : false,
                    'paye' => $paiement ? true : false,
                    'accessible' => $inscription || $paiement
                ],
                'inscription' => $inscription ? [
                    'id' => $inscription->id,
                    'date_inscription' => $inscription->date_inscription,
                    'statut' => $inscription->statut,
                    'mobile_money' => $inscription->mobile_money,
                    'moyen_paiement' => $inscription->moyen_paiement,
                    'session_formation_id' => $inscription->session_formation_id
                ] : null,
                'paiement' => $paiement ? [
                    'id' => $paiement->id,
                    'montant' => $paiement->montant,
                    'methode' => $paiement->methode,
                    'reference' => $paiement->reference,
                    'date_paiement' => $paiement->date_paiement,
                    'statut' => $paiement->statut
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $apprenant->nom,
                'prenom' => $apprenant->prenom,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                    'ordre' => $apprenant->niveau->ordre
                ] : null
            ],
            'statistiques' => [
                'total_modules' => $modules->count(),
                'modules_inscrits' => $modulesFormates->where('statut_acces.inscrit', true)->count(),
                'modules_payes' => $modulesFormates->where('statut_acces.paye', true)->count(),
                'modules_accessibles' => $modulesFormates->where('statut_acces.accessible', true)->count(),
                'modules_disponibles' => $modulesFormates->where('statut_acces.accessible', false)->count()
            ],
            'modules' => $modulesFormates
        ], 200);
    }

    /**
     * Récupérer la liste des modules payés par l'apprenant connecté
     */
    public function mesModulesPayes()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }

        // Récupérer les paiements valides avec les modules et leurs niveaux
        $paiements = $apprenant->paiements()
            ->where('statut', 'valide')
            ->whereNotNull('module_id')
            ->with(['module.niveau.formateur.utilisateur'])
            ->orderByDesc('created_at')
            ->get();

        $modulesPayes = $paiements->map(function ($paiement) {
            return [
                'paiement' => [
                    'id' => $paiement->id,
                    'montant' => $paiement->montant,
                    'methode' => $paiement->methode,
                    'reference' => $paiement->reference,
                    'date_paiement' => $paiement->date_paiement,
                    'statut' => $paiement->statut,
                    'created_at' => $paiement->created_at
                ],
                'module' => $paiement->module ? [
                    'id' => $paiement->module->id,
                    'titre' => $paiement->module->titre,
                    'description' => $paiement->module->description,
                    'discipline' => $paiement->module->discipline,
                    'date_debut' => $paiement->module->date_debut,
                    'date_fin' => $paiement->module->date_fin,
                    'niveau' => $paiement->module->niveau ? [
                        'id' => $paiement->module->niveau->id,
                        'nom' => $paiement->module->niveau->nom,
                        'ordre' => $paiement->module->niveau->ordre
                    ] : null,
                    'formateur' => $paiement->module->niveau && $paiement->module->niveau->formateur && $paiement->module->niveau->formateur->utilisateur ? [
                        'id' => $paiement->module->niveau->formateur->id,
                        'nom' => $paiement->module->niveau->formateur->utilisateur->nom,
                        'prenom' => $paiement->module->niveau->formateur->utilisateur->prenom,
                        'email' => $paiement->module->niveau->formateur->utilisateur->email
                    ] : null
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $apprenant->utilisateur->nom,
                'prenom' => $apprenant->utilisateur->prenom,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                    'ordre' => $apprenant->niveau->ordre
                ] : null
            ],
            'statistiques' => [
                'total_modules_payes' => $modulesPayes->count(),
                'total_montant_paye' => $paiements->sum('montant'),
                'paiements_valides' => $paiements->count()
            ],
            'modules_payes' => $modulesPayes
        ], 200);
    }

    /**
     * Récupérer le lien Google Meet du niveau actuel de l'apprenant connecté
     */
    public function mesLiensGoogleMeet()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
        }

        // Récupérer le niveau actuel de l'apprenant
        $niveau = $apprenant->niveau;
        if (!$niveau) {
            return response()->json([
                'success' => false,
                'error' => 'Aucun niveau assigné à cet apprenant'
            ], 404);
        }

        // Vérifier que l'apprenant a payé au moins un module de ce niveau
        $modulesPayes = $apprenant->paiements()
            ->where('statut', 'valide')
            ->whereHas('module', function ($query) use ($niveau) {
                $query->where('niveau_id', $niveau->id);
            })
            ->exists();

        if (!$modulesPayes) {
            return response()->json([
                'success' => false,
                'error' => 'Vous devez payer au moins un module de ce niveau pour accéder au lien Google Meet'
            ], 403);
        }

        // Récupérer les informations du niveau avec le formateur
        $niveauAvecFormateur = \App\Models\Niveau::with('formateur.utilisateur')
            ->where('id', $niveau->id)
            ->first();

        // Vérifier si le niveau a un lien Google Meet
        if (!$niveauAvecFormateur->lien_meet) {
            return response()->json([
                'success' => false,
                'error' => 'Aucun lien Google Meet disponible pour ce niveau'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $apprenant->utilisateur->nom,
                'prenom' => $apprenant->utilisateur->prenom,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre
                ]
            ],
            'lien_google_meet' => [
                'url' => $niveauAvecFormateur->lien_meet,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description
                ],
                'formateur' => $niveauAvecFormateur->formateur && $niveauAvecFormateur->formateur->utilisateur ? [
                    'id' => $niveauAvecFormateur->formateur->id,
                    'nom' => $niveauAvecFormateur->formateur->utilisateur->nom,
                    'prenom' => $niveauAvecFormateur->formateur->utilisateur->prenom,
                    'email' => $niveauAvecFormateur->formateur->utilisateur->email
                ] : null,
                'session_formation' => $niveauAvecFormateur->sessionFormation ? [
                    'id' => $niveauAvecFormateur->sessionFormation->id,
                    'nom' => $niveauAvecFormateur->sessionFormation->nom,
                    'date_debut' => $niveauAvecFormateur->sessionFormation->date_debut,
                    'date_fin' => $niveauAvecFormateur->sessionFormation->date_fin
                ] : null
            ],
            'statistiques' => [
                'modules_payes_du_niveau' => $apprenant->paiements()
                    ->where('statut', 'valide')
                    ->whereHas('module', function ($query) use ($niveau) {
                        $query->where('niveau_id', $niveau->id);
                    })
                    ->count(),
                'total_modules_du_niveau' => \App\Models\Module::where('niveau_id', $niveau->id)->count(),
                'niveau_actif' => $niveau->actif
            ],
            'message' => 'Lien Google Meet récupéré avec succès'
        ], 200);
    }

    /**
     * Récupérer tous les modules d'un niveau spécifique pour l'apprenant connecté
     */
    public function getModulesByNiveau($niveauId)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }
            
            $apprenant = $user->apprenant;
            if (!$apprenant) {
                return response()->json(['error' => 'Aucun apprenant trouvé pour cet utilisateur'], 404);
            }

            // Vérifier que le niveau existe
            $niveau = \App\Models\Niveau::findOrFail($niveauId);
            
            // Récupérer tous les modules du niveau avec leurs relations
            $modules = \App\Models\Module::with(['niveau.formateur.utilisateur'])
                ->where('niveau_id', $niveauId)
                ->orderBy('date_debut', 'asc')
                ->get()
                ->map(function($module) use ($apprenant) {
                    // Vérifier si l'apprenant est inscrit à ce module
                    $inscription = $apprenant->inscriptions()
                        ->where('module_id', $module->id)
                        ->first();
                    
                    // Vérifier si l'apprenant a payé ce module
                    $paiement = $apprenant->paiements()
                        ->where('module_id', $module->id)
                        ->where('statut', 'valide')
                        ->first();

                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'discipline' => $module->discipline,
                        'prix' => $module->prix,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'horaire' => $module->horaire,
                        'lien' => $module->lien,
                        'support' => $module->support,
                        'audio' => $module->audio,
                        'certificat' => $module->certificat,
                        'niveau' => [
                            'id' => $module->niveau->id,
                            'nom' => $module->niveau->nom,
                            'description' => $module->niveau->description,
                            'ordre' => $module->niveau->ordre
                        ],
                        'formateur' => $module->niveau && $module->niveau->formateur && $module->niveau->formateur->utilisateur ? [
                            'id' => $module->niveau->formateur->id,
                            'nom' => $module->niveau->formateur->utilisateur->nom,
                            'prenom' => $module->niveau->formateur->utilisateur->prenom,
                            'email' => $module->niveau->formateur->utilisateur->email,
                            'specialite' => $module->niveau->formateur->specialite ?? null,
                            'niveau_coran' => $module->niveau->formateur->niveau_coran ?? null,
                            'niveau_arabe' => $module->niveau->formateur->niveau_arabe ?? null
                        ] : null,
                        'statut_apprenant' => [
                            'inscrit' => $inscription ? true : false,
                            'statut_inscription' => $inscription ? $inscription->statut : null,
                            'date_inscription' => $inscription ? $inscription->date_inscription : null,
                            'paye' => $paiement ? true : false,
                            'date_paiement' => $paiement ? $paiement->date_paiement : null,
                            'montant_paye' => $paiement ? $paiement->montant : null
                        ],
                        'disponibilite' => [
                            'inscriptions_ouvertes' => $module->date_debut > now(),
                            'inscriptions_fermees' => $module->date_fin < now(),
                            'en_cours' => $module->date_debut <= now() && $module->date_fin >= now()
                        ]
                    ];
                });

            // Statistiques pour l'apprenant
            $statistiques = [
                'total_modules_niveau' => $modules->count(),
                'modules_inscrits' => $modules->where('statut_apprenant.inscrit', true)->count(),
                'modules_payes' => $modules->where('statut_apprenant.paye', true)->count(),
                'modules_disponibles' => $modules->where('disponibilite.inscriptions_ouvertes', true)->count(),
                'modules_en_cours' => $modules->where('disponibilite.en_cours', true)->count(),
                'modules_termines' => $modules->where('disponibilite.inscriptions_fermees', true)->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Modules du niveau récupérés avec succès',
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'niveau_actuel' => $apprenant->niveau ? [
                        'id' => $apprenant->niveau->id,
                        'nom' => $apprenant->niveau->nom,
                        'ordre' => $apprenant->niveau->ordre
                    ] : null
                ],
                'niveau_demande' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre
                ],
                'modules' => $modules,
                'statistiques' => $statistiques
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des modules du niveau',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}

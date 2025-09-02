<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;

class QuestionnaireApiController extends Controller
{
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
        $questionnaires = Questionnaire::with(['questions', 'module'])
            ->whereIn('module_id', $moduleIds)
            ->where('date_envoi', '<=', now())
            ->get();
        return response()->json(['questionnaires' => $questionnaires], 200);
    }



    public function apprenantTest()
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        $moduleIds = [];
        $inscritModuleIds = [];
        if ($apprenant) {
            $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
            $moduleIds = array_unique($modulesPayes);
            $inscritModuleIds = $moduleIds;
        }
        $questionnaires = collect();
        if (!empty($moduleIds) && $apprenant && $apprenant->niveau_id) {
            // Récupérer tous les questionnaires accessibles
            $questionnairesAccessibles = Questionnaire::with(['module.niveau'])
                ->whereIn('module_id', $moduleIds)
                ->where('date_envoi', '<=', now())
                ->whereHas('module', function($q) use ($apprenant) {
                    $q->where('niveau_id', $apprenant->niveau_id);
                })
                ->get();

            // Filtrer pour ne garder que les questionnaires non répondu
            $questionnairesNonRepondu = collect();
            foreach ($questionnairesAccessibles as $questionnaire) {
                // Vérifier si l'apprenant a répondu à ce questionnaire
                $questionsRepondues = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                    ->whereIn('question_id', $questionnaire->questions->pluck('id'))
                    ->count();
                
                $totalQuestions = $questionnaire->questions->count();
                
                // Si l'apprenant n'a pas répondu à toutes les questions, le questionnaire n'est pas complété
                if ($questionsRepondues < $totalQuestions) {
                    $questionnairesNonRepondu->push($questionnaire);
                }
            }
            
            $questionnaires = $questionnairesNonRepondu;
        }
        return response()->json([
            'questionnaires' => $questionnaires,
            'inscritModuleIds' => $inscritModuleIds,
            'total_questionnaires_non_repondu' => $questionnaires->count()
        ], 200);
    }

    public function showForApprenant(Questionnaire $questionnaire)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $apprenant = $user->apprenant;
        if (!$apprenant) {
            return response()->json(['error' => 'Accès réservé aux apprenants.'], 401);
        }
        $moduleIds = [];
        $modulesInscrits = $apprenant->inscriptions()->where('statut', 'valide')->pluck('module_id')->toArray();
        $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
        $moduleIds = collect($modulesInscrits)->merge($modulesPayes)->unique()->toArray();
        if (!in_array($questionnaire->module_id, $moduleIds)) {
            return response()->json(['error' => 'Vous n\'avez pas accès à ce questionnaire.'], 403);
        }
        $sessionKey = "questionnaire_{$questionnaire->id}_completed_{$apprenant->id}";
        if (session($sessionKey)) {
            return response()->json(['error' => 'Vous avez déjà répondu à ce questionnaire.'], 403);
        }
        $questionnaire->load('questions');
        return response()->json(['questionnaire' => $questionnaire], 200);
    }

    public function repondre(Request $request, Questionnaire $questionnaire)
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        if (!$apprenant) {
            return response()->json(['error' => 'Accès réservé aux apprenants.'], 401);
        }
        $sessionKey = "questionnaire_{$questionnaire->id}_completed_{$apprenant->id}";
        if (session($sessionKey)) {
            return response()->json(['error' => 'Vous avez déjà répondu à ce questionnaire.'], 403);
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
            $reponse = $data['reponses'][$question->id] ?? '';
            $questionPoints = $question->points ?? 1;
            $totalPoints += $questionPoints;
            if ($reponse === $question->bonne_reponse) {
                $score++;
                $earnedPoints += $questionPoints;
            } else {
                $incorrects[] = [
                    'texte' => $question->texte,
                    'bonne_reponse' => $question->bonne_reponse,
                    'votre_reponse' => $reponse ?: 'Aucune réponse',
                    'points' => $questionPoints
                ];
            }
            \App\Models\ReponseQuestionnaire::updateOrCreate([
                'apprenant_id' => $apprenant->id,
                'question_id' => $question->id,
            ], [
                'reponse' => $reponse ?: '',
            ]);
        }
        session([$sessionKey => true]);
        $percentage = ($score / $totalQuestions) * 100;
        $pointsPercentage = ($earnedPoints / $totalPoints) * 100;
        return response()->json([
            'score' => $score,
            'totalQuestions' => $totalQuestions,
            'percentage' => $percentage,
            'earnedPoints' => $earnedPoints,
            'totalPoints' => $totalPoints,
            'pointsPercentage' => $pointsPercentage,
            'incorrects' => $incorrects,
            'message' => count($incorrects) === 0
                ? "🎉 Bravo ! Toutes vos réponses sont correctes !"
                : "📊 Certaines réponses sont incorrectes."
        ], 200);
    }

    public function index()
    {
        $questionnaires = Questionnaire::with(['module.niveau'])->get();
        return response()->json(['questionnaires' => $questionnaires], 200);
    }

    public function create()
    {
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $modules = \App\Models\Module::with('niveau')->get();
        return response()->json(['niveaux' => $niveaux, 'modules' => $modules], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'required|exists:modules,id',
            'minutes' => 'required|integer|min:1|max:180',
            'semaine' => 'required|integer|min:1|max:12',
            'type_devoir' => 'required|in:hebdomadaire,mensuel,final',
            'date_envoi' => 'required|date|after_or_equal:today',
            'questions' => 'required|array|min:1',
            'questions.*.texte' => 'required|string',
            'questions.*.choix' => 'required|array|min:2',
            'questions.*.bonne_reponse' => 'required|string',
            'questions.*.points' => 'required|integer|min:1|max:1', // Forcer à 1 point maximum
        ]);

        // Validation de la cohérence semaine/type selon le nouveau système
        $semainesHebdomadaires = [1, 2, 3, 5, 6, 7, 9, 10, 11];
        $semainesMensuelles = [4, 8];
        $semaineFinale = 12;

        if ($data['type_devoir'] === 'hebdomadaire' && !in_array($data['semaine'], $semainesHebdomadaires)) {
            return response()->json([
                'error' => "Les questionnaires hebdomadaires ne peuvent être créés que pour les semaines 1, 2, 3, 5, 6, 7, 9, 10, 11."
            ], 422);
        }

        if ($data['type_devoir'] === 'mensuel' && !in_array($data['semaine'], $semainesMensuelles)) {
            return response()->json([
                'error' => "Les questionnaires mensuels ne peuvent être créés que pour les semaines 4 et 8."
            ], 422);
        }

        if ($data['type_devoir'] === 'final' && $data['semaine'] !== $semaineFinale) {
            return response()->json([
                'error' => "Le questionnaire final ne peut être créé que pour la semaine 12."
            ], 422);
        }

        // Validation du nombre exact de questions selon le type
        $questionCount = count($data['questions']);
        $questionsRequises = [
            'hebdomadaire' => 2,
            'mensuel' => 8,
            'final' => 66
        ];

        if ($questionCount !== $questionsRequises[$data['type_devoir']]) {
            return response()->json([
                'error' => "Le devoir {$data['type_devoir']} nécessite exactement {$questionsRequises[$data['type_devoir']]} questions. Vous en avez ajouté {$questionCount}."
            ], 422);
        }

        // Validation que toutes les questions ont 1 point
        foreach ($data['questions'] as $index => $question) {
            if ($question['points'] !== 1) {
                return response()->json([
                    'error' => "Toutes les questions doivent avoir exactement 1 point. La question " . ($index + 1) . " a {$question['points']} point(s)."
                ], 422);
            }
        }

        // Vérifier qu'il n'existe pas déjà un questionnaire pour cette semaine et ce module
        $questionnaireExistant = Questionnaire::where('module_id', $data['module_id'])
            ->where('semaine', $data['semaine'])
            ->first();

        if ($questionnaireExistant) {
            return response()->json([
                'error' => "Un questionnaire pour la semaine {$data['semaine']} existe déjà pour ce module."
            ], 422);
        }

        $questionnaire = Questionnaire::create([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'module_id' => $data['module_id'],
            'minutes' => $data['minutes'],
            'semaine' => $data['semaine'],
            'type_devoir' => $data['type_devoir'],
            'date_envoi' => $data['date_envoi'],
        ]);

        if (!empty($data['questions'])) {
            foreach ($data['questions'] as $q) {
                // Nettoyer et valider les choix avant l'encodage
                $choix = $q['choix'];
                
                // Si les choix sont déjà une chaîne JSON, les décoder d'abord
                if (is_string($choix) && $this->isJson($choix)) {
                    $choix = json_decode($choix, true);
                }
                
                // S'assurer que les choix sont un tableau
                if (!is_array($choix)) {
                    $choix = [$choix];
                }
                
                // Nettoyer chaque choix (supprimer les caractères d'échappement)
                $choixNettoyes = array_map(function($choixItem) {
                    if (is_string($choixItem)) {
                        // Décoder les caractères Unicode si nécessaire
                        $choixItem = json_decode('"' . $choixItem . '"');
                        // Nettoyer les caractères spéciaux
                        $choixItem = html_entity_decode($choixItem, ENT_QUOTES, 'UTF-8');
                    }
                    return $choixItem;
                }, $choix);
                
                $questionnaire->questions()->create([
                    'texte' => $q['texte'],
                    'choix' => json_encode($choixNettoyes, JSON_UNESCAPED_UNICODE),
                    'bonne_reponse' => $q['bonne_reponse'],
                    'points' => 1, // Forcer à 1 point selon le nouveau système
                ]);
            }
        }

        // Calculer le total des points pour vérification
        $totalPoints = $questionnaire->questions()->sum('points');
        $pointsAttendus = $questionsRequises[$data['type_devoir']];

        return response()->json([
            'questionnaire' => $questionnaire->load('questions'),
            'date_envoi' => $questionnaire->date_envoi,
            'total_points' => $totalPoints,
            'points_attendus' => $pointsAttendus,
            'message' => "Questionnaire créé avec succès ! Total des points : {$totalPoints}/{$pointsAttendus}"
        ], 201);
    }

    /**
     * Vérifie si une chaîne est un JSON valide
     * @param string $string
     * @return bool
     */
    private function isJson($string) {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function show(Questionnaire $questionnaire)
    {
        $questionnaire->load('questions');
        return response()->json(['questionnaire' => $questionnaire], 200);
    }

    public function edit(Questionnaire $questionnaire)
    {
        return response()->json(['questionnaire' => $questionnaire], 200);
    }

    public function update(Request $request, Questionnaire $questionnaire)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $questionnaire->update($data);
        return response()->json(['questionnaire' => $questionnaire, 'message' => 'Questionnaire mis à jour avec succès'], 200);
    }

    public function destroy(Questionnaire $questionnaire)
    {
        $questionnaire->delete();
        return response()->json(null, 204);
    }

    /**
     * Afficher le nombre de questions dans un questionnaire
     */
    public function nombreQuestions(Questionnaire $questionnaire)
    {
        $nombreQuestions = $questionnaire->questions()->count();
        
        return response()->json([
            'success' => true,
            'questionnaire_id' => $questionnaire->id,
            'titre' => $questionnaire->titre,
            'nombre_questions' => $nombreQuestions,
            'message' => "Le questionnaire '{$questionnaire->titre}' contient {$nombreQuestions} question(s)."
        ], 200);
    }

    /**
     * Afficher le nombre de questions pour tous les questionnaires
     */
    public function nombreQuestionsTous()
    {
        $questionnaires = Questionnaire::withCount('questions')->get();
        
        $resultats = $questionnaires->map(function ($questionnaire) {
            return [
                'id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'module' => $questionnaire->module ? $questionnaire->module->titre : 'N/A',
                'nombre_questions' => $questionnaire->questions_count,
                'type_devoir' => $questionnaire->type_devoir,
                'minutes' => $questionnaire->minutes
            ];
        });
        
        return response()->json([
            'success' => true,
            'total_questionnaires' => $questionnaires->count(),
            'total_questions' => $questionnaires->sum('questions_count'),
            'questionnaires' => $resultats
        ], 200);
    }

    /**
     * Créer automatiquement tous les questionnaires d'une session complète (12 semaines)
     */
    public function creerSessionComplete(Request $request)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'titre_base' => 'required|string|max:255',
            'description_base' => 'nullable|string',
            'minutes_hebdomadaire' => 'required|integer|min:1|max:60',
            'minutes_mensuel' => 'required|integer|min:1|max:120',
            'minutes_final' => 'required|integer|min:1|max:180',
        ]);

        $module = \App\Models\Module::find($data['module_id']);
        if (!$module) {
            return response()->json(['error' => 'Module non trouvé'], 404);
        }

        $questionnairesCrees = [];
        $totalQuestions = 0;
        $totalPoints = 0;

        // Vérifier qu'aucun questionnaire n'existe déjà pour ce module
        $questionnairesExistants = Questionnaire::where('module_id', $data['module_id'])->count();
        if ($questionnairesExistants > 0) {
            return response()->json([
                'error' => "Des questionnaires existent déjà pour ce module. Veuillez les supprimer avant de créer une session complète."
            ], 422);
        }

        // Créer les questionnaires hebdomadaires (semaines 1-3, 5-7, 9-11)
        $semainesHebdomadaires = [1, 2, 3, 5, 6, 7, 9, 10, 11];
        foreach ($semainesHebdomadaires as $semaine) {
            $questionnaire = Questionnaire::create([
                'titre' => "{$data['titre_base']} - Questionnaire Hebdomadaire Semaine {$semaine}",
                'description' => $data['description_base'] ? "{$data['description_base']} - Semaine {$semaine}" : "Questionnaire hebdomadaire de la semaine {$semaine}",
                'module_id' => $data['module_id'],
                'minutes' => $data['minutes_hebdomadaire'],
                'semaine' => $semaine,
                'type_devoir' => 'hebdomadaire',
            ]);

            // Créer 2 questions par questionnaire hebdomadaire
            for ($i = 1; $i <= 2; $i++) {
                $questionnaire->questions()->create([
                    'texte' => "Question {$i} du questionnaire hebdomadaire semaine {$semaine}",
                    'choix' => json_encode(['A', 'B', 'C', 'D']),
                    'bonne_reponse' => 'A',
                    'points' => 1,
                ]);
            }

            $questionnairesCrees[] = [
                'id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'semaine' => $semaine,
                'type' => 'hebdomadaire',
                'questions' => 2,
                'points' => 2
            ];
            $totalQuestions += 2;
            $totalPoints += 2;
        }

        // Créer les questionnaires mensuels (semaines 4 et 8)
        $semainesMensuelles = [4, 8];
        foreach ($semainesMensuelles as $semaine) {
            $questionnaire = Questionnaire::create([
                'titre' => "{$data['titre_base']} - Devoir Mensuel Semaine {$semaine}",
                'description' => $data['description_base'] ? "{$data['description_base']} - Devoir mensuel semaine {$semaine}" : "Devoir mensuel de la semaine {$semaine}",
                'module_id' => $data['module_id'],
                'minutes' => $data['minutes_mensuel'],
                'semaine' => $semaine,
                'type_devoir' => 'mensuel',
            ]);

            // Créer 8 questions par questionnaire mensuel
            for ($i = 1; $i <= 8; $i++) {
                $questionnaire->questions()->create([
                    'texte' => "Question {$i} du devoir mensuel semaine {$semaine}",
                    'choix' => json_encode(['A', 'B', 'C', 'D']),
                    'bonne_reponse' => 'A',
                    'points' => 1,
                ]);
            }

            $questionnairesCrees[] = [
                'id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'semaine' => $semaine,
                'type' => 'mensuel',
                'questions' => 8,
                'points' => 8
            ];
            $totalQuestions += 8;
            $totalPoints += 8;
        }

        // Créer le questionnaire final (semaine 12)
        $questionnaire = Questionnaire::create([
            'titre' => "{$data['titre_base']} - Devoir Final",
            'description' => $data['description_base'] ? "{$data['description_base']} - Devoir final" : "Devoir final de la session",
            'module_id' => $data['module_id'],
            'minutes' => $data['minutes_final'],
            'semaine' => 12,
            'type_devoir' => 'final',
        ]);

        // Créer 66 questions pour le questionnaire final
        for ($i = 1; $i <= 66; $i++) {
            $questionnaire->questions()->create([
                'texte' => "Question {$i} du devoir final",
                'choix' => json_encode(['A', 'B', 'C', 'D']),
                'bonne_reponse' => 'A',
                'points' => 1,
            ]);
        }

        $questionnairesCrees[] = [
            'id' => $questionnaire->id,
            'titre' => $questionnaire->titre,
            'semaine' => 12,
            'type' => 'final',
            'questions' => 66,
            'points' => 66
        ];
        $totalQuestions += 66;
        $totalPoints += 66;

        return response()->json([
            'success' => true,
            'module' => [
                'id' => $module->id,
                'titre' => $module->titre
            ],
            'questionnaires_crees' => $questionnairesCrees,
            'resume' => [
                'total_questionnaires' => count($questionnairesCrees),
                'total_questions' => $totalQuestions,
                'total_points' => $totalPoints,
                'repartition' => [
                    'hebdomadaires' => count($semainesHebdomadaires),
                    'mensuels' => count($semainesMensuelles),
                    'final' => 1
                ]
            ],
            'message' => "Session complète créée avec succès ! {$totalQuestions} questions pour {$totalPoints} points au total."
        ], 201);
    }
}

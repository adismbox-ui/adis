<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Inscription;
use App\Models\Document;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\ReponseQuestionnaire;
use App\Models\Paiement;
use App\Models\SessionFormation;
use Carbon\Carbon;

class ApiApprenantController extends Controller
{
    /**
     * Récupère les formations de l'apprenant (en cours, terminées, à venir)
     */
    public function getMesFormations(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        $inscriptions = Inscription::where('apprenant_id', $apprenant->id)
            ->where('statut', 'valide')
            ->with(['module', 'module.niveau', 'sessionFormation'])
            ->get();

        $enCours = [];
        $terminees = [];
        $aVenir = [];
        $now = Carbon::now();

        foreach ($inscriptions as $inscription) {
            $module = $inscription->module;
            if (!$module) continue;

            $moduleData = [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'lien' => $module->lien,
                'prix' => $module->prix,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
            ];

            if ($module->date_debut && $module->date_fin) {
                $dateDebut = Carbon::parse($module->date_debut);
                $dateFin = Carbon::parse($module->date_fin);

                if ($now->between($dateDebut, $dateFin)) {
                    $enCours[] = $moduleData;
                } elseif ($now->gt($dateFin)) {
                    $terminees[] = $moduleData;
                } else {
                    $aVenir[] = $moduleData;
                }
            } else {
                $enCours[] = $moduleData;
            }
        }

        return response()->json([
            'success' => true,
            'en_cours' => $enCours,
            'terminees' => $terminees,
            'a_venir' => $aVenir,
        ], 200);
    }

    /**
     * Récupère les modules de l'apprenant
     */
    public function getModules(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        $inscriptions = Inscription::where('apprenant_id', $apprenant->id)
            ->where('statut', 'valide')
            ->with(['module.niveau', 'module.formateur.utilisateur'])
            ->get();

        $modules = $inscriptions->map(function ($inscription) {
            $module = $inscription->module;
            if (!$module) return null;

            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'lien' => $module->lien,
                'prix' => $module->prix,
                'support' => $module->support,
                'audio' => $module->audio,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
                'formateur' => $module->formateur && $module->formateur->utilisateur ? [
                    'id' => $module->formateur->id,
                    'nom' => $module->formateur->utilisateur->nom,
                    'prenom' => $module->formateur->utilisateur->prenom,
                ] : null,
            ];
        })->filter();

        return response()->json([
            'success' => true,
            'modules' => $modules->values(),
        ], 200);
    }

    /**
     * Récupère les documents de l'apprenant
     */
    public function getMesDocuments(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        // Récupérer les modules de l'apprenant
        $moduleIds = Inscription::where('apprenant_id', $apprenant->id)
            ->where('statut', 'valide')
            ->pluck('module_id')
            ->filter();

        // Récupérer les documents des modules
        $documents = Document::whereIn('module_id', $moduleIds)
            ->orWhere('niveau_id', $apprenant->niveau_id)
            ->with(['module', 'niveau'])
            ->get();

        $documentsFormates = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'titre' => $document->titre,
                'type' => $document->type,
                'fichier' => $document->fichier ? url('/storage/' . $document->fichier) : null,
                'audio' => $document->audio,
                'module' => $document->module ? [
                    'id' => $document->module->id,
                    'titre' => $document->module->titre,
                ] : null,
                'niveau' => $document->niveau ? [
                    'id' => $document->niveau->id,
                    'nom' => $document->niveau->nom,
                ] : null,
                'created_at' => $document->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $documentsFormates,
        ], 200);
    }

    /**
     * Récupère les questionnaires disponibles pour l'apprenant
     */
    public function getQuestionnaires(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        // Récupérer les modules de l'apprenant
        $moduleIds = Inscription::where('apprenant_id', $apprenant->id)
            ->where('statut', 'valide')
            ->pluck('module_id')
            ->filter();

        $questionnaires = Questionnaire::whereIn('module_id', $moduleIds)
            ->orWhere('niveau_id', $apprenant->niveau_id)
            ->with(['module', 'niveau', 'questions'])
            ->get();

        $questionnairesFormates = $questionnaires->map(function ($questionnaire) use ($apprenant) {
            // Vérifier si l'apprenant a déjà répondu
            $questionsIds = $questionnaire->questions->pluck('id');
            $aRepondu = ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                ->whereIn('question_id', $questionsIds)
                ->exists();

            return [
                'id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'description' => $questionnaire->description,
                'minutes' => $questionnaire->minutes,
                'type_devoir' => $questionnaire->type_devoir,
                'module' => $questionnaire->module ? [
                    'id' => $questionnaire->module->id,
                    'titre' => $questionnaire->module->titre,
                ] : null,
                'a_repondu' => $aRepondu,
                'created_at' => $questionnaire->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'questionnaires' => $questionnairesFormates,
        ], 200);
    }

    /**
     * Récupère un questionnaire spécifique
     */
    public function getQuestionnaire(Request $request, $id)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        $questionnaire = Questionnaire::with(['questions', 'module', 'niveau'])->find($id);

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'error' => 'Questionnaire non trouvé'
            ], 404);
        }

        $questions = $questionnaire->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'texte' => $question->texte,
                'choix' => $question->choix,
                'points' => $question->points,
            ];
        });

        return response()->json([
            'success' => true,
            'questionnaire' => [
                'id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'description' => $questionnaire->description,
                'minutes' => $questionnaire->minutes,
                'type_devoir' => $questionnaire->type_devoir,
                'questions' => $questions,
            ],
        ], 200);
    }

    /**
     * Soumet les réponses à un questionnaire
     */
    public function repondreQuestionnaire(Request $request, $id)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        $questionnaire = Questionnaire::with('questions')->find($id);

        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'error' => 'Questionnaire non trouvé'
            ], 404);
        }

        $reponses = $request->input('reponses', []);
        $score = 0;
        $totalPoints = 0;
        $incorrects = [];

        foreach ($questionnaire->questions as $question) {
            $totalPoints += $question->points;
            $reponseApprenant = $reponses[$question->id] ?? null;

            // Sauvegarder la réponse
            ReponseQuestionnaire::updateOrCreate(
                [
                    'apprenant_id' => $apprenant->id,
                    'question_id' => $question->id,
                ],
                [
                    'reponse' => $reponseApprenant,
                ]
            );

            // Vérifier si la réponse est correcte
            if ($reponseApprenant === $question->bonne_reponse) {
                $score += $question->points;
            } else {
                $incorrects[] = [
                    'texte' => $question->texte,
                    'bonne_reponse' => $question->bonne_reponse,
                    'votre_reponse' => $reponseApprenant,
                    'points' => $question->points,
                ];
            }
        }

        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
        $totalQuestions = $questionnaire->questions->count();
        $scoreQuestions = $totalQuestions - count($incorrects);

        $message = "🎉 Excellent travail ! Vous avez obtenu un score parfait.";
        if ($percentage < 100 && $percentage >= 70) {
            $message = "✅ Bon travail ! Quelques erreurs mineures.";
        } elseif ($percentage < 70) {
            $message = "📊 Certaines réponses sont incorrectes.";
        }

        return response()->json([
            'success' => true,
            'score' => $scoreQuestions,
            'totalQuestions' => $totalQuestions,
            'percentage' => round(($scoreQuestions / $totalQuestions) * 100, 2),
            'earnedPoints' => $score,
            'totalPoints' => $totalPoints,
            'pointsPercentage' => round($percentage, 2),
            'message' => $message,
            'incorrects' => $incorrects,
        ], 200);
    }

    /**
     * Récupère les résultats des questionnaires de l'apprenant
     */
    public function getResultatsQuestionnaires(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        // Récupérer tous les questionnaires auxquels l'apprenant a répondu
        $reponses = ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
            ->with(['question.questionnaire'])
            ->get()
            ->groupBy('question.questionnaire_id');

        $resultats = [];

        foreach ($reponses as $questionnaireId => $reponsesGroup) {
            $questionnaire = $reponsesGroup->first()->question->questionnaire;
            if (!$questionnaire) continue;

            $questions = $questionnaire->questions;
            $score = 0;
            $totalPoints = 0;
            $incorrects = [];

            foreach ($questions as $question) {
                $totalPoints += $question->points;
                $reponse = $reponsesGroup->where('question_id', $question->id)->first();

                if ($reponse && $reponse->reponse === $question->bonne_reponse) {
                    $score += $question->points;
                } elseif ($reponse) {
                    $incorrects[] = [
                        'texte' => $question->texte,
                        'bonne_reponse' => $question->bonne_reponse,
                        'votre_reponse' => $reponse->reponse,
                        'points' => $question->points,
                    ];
                }
            }

            $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
            $totalQuestions = $questions->count();
            $scoreQuestions = $totalQuestions - count($incorrects);

            $message = "🎉 Excellent travail !";
            if ($percentage < 100 && $percentage >= 70) {
                $message = "✅ Bon travail !";
            } elseif ($percentage < 70) {
                $message = "📊 Certaines réponses sont incorrectes.";
            }

            $resultats[] = [
                'id' => $questionnaire->id,
                'questionnaire' => [
                    'id' => $questionnaire->id,
                    'titre' => $questionnaire->titre,
                    'description' => $questionnaire->description,
                    'type_questionnaire' => $questionnaire->type_devoir,
                    'duree_minutes' => $questionnaire->minutes,
                ],
                'score' => $scoreQuestions,
                'totalQuestions' => $totalQuestions,
                'percentage' => round(($scoreQuestions / $totalQuestions) * 100, 2),
                'earnedPoints' => $score,
                'totalPoints' => $totalPoints,
                'pointsPercentage' => round($percentage, 2),
                'message' => $message,
                'incorrects' => $incorrects,
                'date_soumission' => $reponsesGroup->max('created_at'),
                'created_at' => $reponsesGroup->max('created_at'),
                'updated_at' => $reponsesGroup->max('updated_at'),
            ];
        }

        return response()->json([
            'success' => true,
            'resultats' => $resultats,
        ], 200);
    }

    /**
     * Récupère le profil de l'apprenant
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'sexe' => $user->sexe,
                'telephone' => $user->telephone,
                'type_compte' => $user->type_compte,
            ],
            'apprenant' => [
                'id' => $apprenant->id,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Met à jour le profil de l'apprenant
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'prenom' => 'sometimes|string|max:255',
            'nom' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|max:20',
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'telephone' => $user->telephone,
            ],
        ], 200);
    }

    /**
     * Récupère la progression de l'apprenant
     */
    public function getProgression(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        // Récupérer les statistiques
        $inscriptions = Inscription::where('apprenant_id', $apprenant->id)
            ->where('statut', 'valide')
            ->count();

        $questionnairesCompletes = ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
            ->distinct('question_id')
            ->count('question_id');

        return response()->json([
            'success' => true,
            'progression' => [
                'modules_inscrits' => $inscriptions,
                'questionnaires_completes' => $questionnairesCompletes,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Récupère les paiements de l'apprenant
     */
    public function getPaiements(Request $request)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        $paiements = Paiement::where('apprenant_id', $apprenant->id)
            ->with('module')
            ->orderBy('date_paiement', 'desc')
            ->get();

        $paiementsFormates = $paiements->map(function ($paiement) {
            return [
                'id' => $paiement->id,
                'montant' => $paiement->montant,
                'statut' => $paiement->statut,
                'methode' => $paiement->methode,
                'date_paiement' => $paiement->date_paiement,
                'module' => $paiement->module ? [
                    'id' => $paiement->module->id,
                    'titre' => $paiement->module->titre,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'paiements' => $paiementsFormates,
        ], 200);
    }
}









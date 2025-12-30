<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\Question;

class ApiQuestionnaireController extends Controller
{
    /**
     * Récupère tous les questionnaires
     */
    public function index(Request $request)
    {
        $questionnaires = Questionnaire::with(['module', 'niveau', 'questions'])->get();

        $questionnairesFormates = $questionnaires->map(function ($questionnaire) {
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
                'nombre_questions' => $questionnaire->questions->count(),
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
    public function show(Request $request, $id)
    {
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
}


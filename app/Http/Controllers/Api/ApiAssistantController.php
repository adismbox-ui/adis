<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assistant;
use App\Models\Apprenant;
use App\Models\Formateur;

class ApiAssistantController extends Controller
{
    /**
     * Récupère le profil de l'assistant
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $assistant = $user->assistant;

        if (!$assistant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil assistant non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'telephone' => $user->telephone,
            ],
            'assistant' => [
                'id' => $assistant->id,
                'actif' => $assistant->actif ?? true,
            ],
        ], 200);
    }

    /**
     * Met à jour le profil de l'assistant
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $assistant = $user->assistant;

        if (!$assistant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil assistant non trouvé'
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
        ], 200);
    }

    /**
     * Récupère la liste des apprenants
     */
    public function getApprenants(Request $request)
    {
        $user = $request->user();
        $assistant = $user->assistant;

        if (!$assistant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil assistant non trouvé'
            ], 404);
        }

        $apprenants = Apprenant::with(['utilisateur', 'niveau'])->get();

        $apprenantsFormates = $apprenants->map(function ($apprenant) {
            return [
                'id' => $apprenant->id,
                'utilisateur' => [
                    'id' => $apprenant->utilisateur->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                ],
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'apprenants' => $apprenantsFormates,
        ], 200);
    }

    /**
     * Récupère la liste des formateurs
     */
    public function getFormateurs(Request $request)
    {
        $user = $request->user();
        $assistant = $user->assistant;

        if (!$assistant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil assistant non trouvé'
            ], 404);
        }

        $formateurs = Formateur::with(['utilisateur', 'niveaux'])->get();

        $formateursFormates = $formateurs->map(function ($formateur) {
            return [
                'id' => $formateur->id,
                'utilisateur' => [
                    'id' => $formateur->utilisateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                ],
                'valide' => $formateur->valide,
                'specialite' => $formateur->specialite,
            ];
        });

        return response()->json([
            'success' => true,
            'formateurs' => $formateursFormates,
        ], 200);
    }
}


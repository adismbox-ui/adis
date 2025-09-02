<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LienSocial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LienSocialController extends Controller
{
    /**
     * Récupère tous les liens sociaux (pour l'admin)
     */
    public function index()
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $liens = LienSocial::orderBy('ordre', 'asc')->get();

            return response()->json([
                'success' => true,
                'liens' => $liens,
                'total' => $liens->count(),
                'message' => 'Liens sociaux récupérés avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des liens sociaux: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère tous les liens sociaux actifs (pour les apprenants)
     */
    public function liensActifs()
    {
        try {
            $liens = LienSocial::liensActifs();

            return response()->json([
                'success' => true,
                'liens' => $liens,
                'total' => $liens->count(),
                'message' => 'Liens sociaux actifs récupérés avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des liens sociaux: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère tous les liens sociaux (actifs et inactifs) pour l'apprenant connecté
     */
    public function tousLesLiens()
    {
        try {
            // Vérifier que l'utilisateur est connecté
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Récupérer tous les liens sociaux triés par ordre
            $liens = LienSocial::orderBy('ordre', 'asc')->get();

            return response()->json([
                'success' => true,
                'liens' => $liens,
                'total' => $liens->count(),
                'message' => 'Tous les liens sociaux récupérés avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des liens sociaux: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère un lien social spécifique
     */
    public function show($id)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $lien = LienSocial::find($id);

            if (!$lien) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lien social non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'lien' => $lien,
                'message' => 'Lien social récupéré avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du lien social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée un nouveau lien social
     */
    public function store(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:50|unique:liens_sociaux,nom',
                'titre' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
                'url' => 'required|url|max:500',
                'icone' => 'nullable|string|max:50',
                'couleur' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
                'actif' => 'boolean',
                'ordre' => 'integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 422);
            }

            $lien = LienSocial::create($request->all());

            return response()->json([
                'success' => true,
                'lien' => $lien,
                'message' => 'Lien social créé avec succès'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du lien social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour un lien social
     */
    public function update(Request $request, $id)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $lien = LienSocial::find($id);

            if (!$lien) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lien social non trouvé'
                ], 404);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:50|unique:liens_sociaux,nom,' . $id,
                'titre' => 'sometimes|required|string|max:100',
                'description' => 'nullable|string|max:500',
                'url' => 'sometimes|required|url|max:500',
                'icone' => 'nullable|string|max:50',
                'couleur' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
                'actif' => 'boolean',
                'ordre' => 'integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 422);
            }

            $lien->update($request->all());

            return response()->json([
                'success' => true,
                'lien' => $lien,
                'message' => 'Lien social mis à jour avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour du lien social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un lien social
     */
    public function destroy($id)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $lien = LienSocial::find($id);

            if (!$lien) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lien social non trouvé'
                ], 404);
            }

            $lien->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lien social supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression du lien social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Active ou désactive un lien social
     */
    public function toggleActif($id)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $lien = LienSocial::find($id);

            if (!$lien) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lien social non trouvé'
                ], 404);
            }

            $lien->toggleActif();

            return response()->json([
                'success' => true,
                'lien' => $lien,
                'message' => 'Statut du lien social modifié avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la modification du statut: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour l'ordre des liens sociaux
     */
    public function updateOrdre(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'liens' => 'required|array',
                'liens.*.id' => 'required|integer|exists:liens_sociaux,id',
                'liens.*.ordre' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 422);
            }

            foreach ($request->liens as $lienData) {
                $lien = LienSocial::find($lienData['id']);
                if ($lien) {
                    $lien->changerOrdre($lienData['ordre']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordre des liens sociaux mis à jour avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour de l\'ordre: ' . $e->getMessage()
            ], 500);
        }
    }
}

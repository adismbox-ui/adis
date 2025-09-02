<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assistant;
use App\Models\Formateur;
use App\Models\Utilisateur;

class AssistantApiController extends Controller
{
    /**
     * Vérifier si l'assistant connecté a aussi un profil formateur
     */
    public function verifierProfilFormateur()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            $assistant = $user->assistant;
            if (!$assistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil assistant non trouvé'
                ], 404);
            }

            // Vérifier si l'utilisateur a un profil formateur
            $profilFormateur = Formateur::where('utilisateur_id', $user->id)->first();

            if (!$profilFormateur) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vous n\'avez pas de profil formateur',
                    'a_profil_formateur' => false,
                    'assistant' => [
                        'id' => $assistant->id,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'bio' => $assistant->bio,
                        'actif' => $assistant->actif
                    ],
                    'profil_formateur' => null
                ], 200);
            }

            // Récupérer les niveaux assignés au formateur
            $niveauxAssignes = $profilFormateur->niveaux()->with('modules')->get();

            return response()->json([
                'success' => true,
                'message' => 'Vous avez un profil formateur',
                'a_profil_formateur' => true,
                'assistant' => [
                    'id' => $assistant->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'bio' => $assistant->bio,
                    'actif' => $assistant->actif
                ],
                'profil_formateur' => [
                    'id' => $profilFormateur->id,
                    'specialite' => $profilFormateur->specialite,
                    'valide' => $profilFormateur->valide,
                    'connaissance_adis' => $profilFormateur->connaissance_adis,
                    'formation_adis' => $profilFormateur->formation_adis,
                    'formation_autre' => $profilFormateur->formation_autre,
                    'niveau_coran' => $profilFormateur->niveau_coran,
                    'niveau_arabe' => $profilFormateur->niveau_arabe,
                    'niveau_francais' => $profilFormateur->niveau_francais,
                    'ville' => $profilFormateur->ville,
                    'commune' => $profilFormateur->commune,
                    'quartier' => $profilFormateur->quartier,
                    'date_creation' => $profilFormateur->created_at->format('Y-m-d H:i:s'),
                    'derniere_mise_a_jour' => $profilFormateur->updated_at->format('Y-m-d H:i:s')
                ],
                'niveaux_assignes' => $niveauxAssignes->map(function ($niveau) {
                    return [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'description' => $niveau->description,
                        'ordre' => $niveau->ordre,
                        'actif' => $niveau->actif,
                        'lien_meet' => $niveau->lien_meet,
                        'modules_count' => $niveau->modules->count()
                    ];
                }),
                'resume' => [
                    'message' => "Vous êtes assistant ET formateur !",
                    'statut' => "Double profil actif",
                    'permissions' => [
                        'peut_assister' => $assistant->actif,
                        'peut_former' => $profilFormateur->valide,
                        'peut_gerer_contenu' => true,
                        'niveaux_assignes' => $niveauxAssignes->count()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la vérification du profil formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le profil de l'assistant connecté
     */
    public function getProfile()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            $assistant = $user->assistant;
            if (!$assistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil assistant non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'assistant' => [
                    'id' => $assistant->id,
                    'utilisateur' => [
                        'id' => $user->id,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'telephone' => $user->telephone,
                        'sexe' => $user->sexe,
                        'categorie' => $user->categorie,
                        'type_compte' => $user->type_compte,
                        'actif' => $user->actif,
                        'email_verified_at' => $user->email_verified_at
                    ],
                    'bio' => $assistant->bio,
                    'actif' => $assistant->actif,
                    'formateur_id' => $assistant->formateur_id,
                    'date_creation' => $assistant->created_at->format('Y-m-d H:i:s'),
                    'derniere_mise_a_jour' => $assistant->updated_at->format('Y-m-d H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du profil',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}


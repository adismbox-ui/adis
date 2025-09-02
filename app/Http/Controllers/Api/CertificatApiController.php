<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificat;
use Illuminate\Support\Facades\Log;

class CertificatApiController extends Controller
{
    /**
     * Affiche tous les certificats (réservé aux admins)
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès non autorisé. Seuls les administrateurs peuvent voir tous les certificats.'], 403);
        }

        $certificats = \App\Models\Certificat::with(['apprenant.utilisateur', 'module'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['certificats' => $certificats], 200);
    }

    /**
     * Affiche les certificats de l'apprenant connecté
     */
    public function mesCertificats()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        // Debug: Vérifier l'utilisateur connecté
        Log::info('Utilisateur connecté:', [
            'user_id' => $user->id,
            'email' => $user->email,
            'type_compte' => $user->type_compte
        ]);

        $apprenant = $user->apprenant;
        
        // Debug: Vérifier si l'apprenant existe
        if (!$apprenant) {
            Log::warning('Aucun apprenant trouvé pour l\'utilisateur', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            // Vérifier s'il y a un apprenant avec cet utilisateur_id
            $apprenantExistant = \App\Models\Apprenant::where('utilisateur_id', $user->id)->first();
            if ($apprenantExistant) {
                Log::info('Apprenant trouvé par requête directe:', [
                    'apprenant_id' => $apprenantExistant->id,
                    'utilisateur_id' => $apprenantExistant->utilisateur_id
                ]);
                $apprenant = $apprenantExistant;
            } else {
                return response()->json([
                    'error' => 'Aucun apprenant lié à cet utilisateur.',
                    'debug' => [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'type_compte' => $user->type_compte
                    ]
                ], 404);
            }
        }

        Log::info('Apprenant trouvé:', [
            'apprenant_id' => $apprenant->id,
            'utilisateur_id' => $apprenant->utilisateur_id
        ]);

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

        // Récupérer tous les certificats de l'apprenant
        $tousCertificats = $apprenant->certificats()
            ->with(['module.niveau', 'module.formateur.utilisateur'])
            ->orderByDesc('created_at')
            ->get();

        // Filtrer les certificats pour ne garder que ceux des modules accessibles
        $certificats = $tousCertificats->filter(function($certificat) use ($moduleIds) {
            // Si le certificat a un module, vérifier que l'apprenant a accès à ce module
            if ($certificat->module) {
                return in_array($certificat->module->id, $moduleIds);
            }
            // Si pas de module, c'est un certificat de niveau (on le garde)
            return true;
        });

        Log::info('Certificats trouvés:', [
            'total_certificats' => $tousCertificats->count(),
            'certificats_accessibles' => $certificats->count(),
            'modules_accessibles' => $moduleIds,
            'apprenant_id' => $apprenant->id
        ]);

        // Formater la réponse selon le modèle Certificat réel
        $certificatsFormates = $certificats->map(function($certificat) {
            // Si le certificat a un module, afficher les infos du module
            if ($certificat->module) {
                return [
                    'id' => $certificat->id,
                    'titre' => $certificat->titre,
                    'date_obtention' => $certificat->date_obtention,
                    'fichier' => $certificat->fichier,
                    'type' => 'module',
                    'module' => [
                        'id' => $certificat->module->id ?? null,
                        'titre' => $certificat->module->titre ?? null,
                        'description' => $certificat->module->description ?? null,
                        'discipline' => $certificat->module->discipline ?? null,
                        'date_debut' => $certificat->module->date_debut ?? null,
                        'date_fin' => $certificat->module->date_fin ?? null,
                        'niveau' => [
                            'id' => $certificat->module->niveau->id ?? null,
                            'nom' => $certificat->module->niveau->nom ?? null,
                            'ordre' => $certificat->module->niveau->ordre ?? null,
                        ],
                        'formateur' => [
                            'id' => $certificat->module->formateur->id ?? null,
                            'nom' => $certificat->module->formateur->utilisateur->nom ?? null,
                            'prenom' => $certificat->module->formateur->utilisateur->prenom ?? null,
                            'email' => $certificat->module->formateur->utilisateur->email ?? null,
                        ]
                    ],
                    'created_at' => $certificat->created_at,
                    'updated_at' => $certificat->updated_at
                ];
            } else {
                // Si pas de module, c'est un certificat de niveau
                // Extraire le nom du niveau du titre du certificat
                $niveauNom = null;
                $niveauOrdre = null;
                if (preg_match('/Niveau (\d+)/', $certificat->titre, $matches)) {
                    $niveauOrdre = $matches[1];
                    // Chercher le niveau correspondant
                    $niveau = \App\Models\Niveau::where('ordre', $niveauOrdre)->first();
                    if ($niveau) {
                        $niveauNom = $niveau->nom;
                    }
                }

                return [
                    'id' => $certificat->id,
                    'titre' => $certificat->titre,
                    'date_obtention' => $certificat->date_obtention,
                    'fichier' => $certificat->fichier,
                    'type' => 'niveau',
                    'niveau' => [
                        'nom' => $niveauNom,
                        'ordre' => $niveauOrdre,
                    ],
                    'module' => null,
                    'created_at' => $certificat->created_at,
                    'updated_at' => $certificat->updated_at
                ];
            }
        });

        return response()->json([
            'apprenant' => [
                'id' => $apprenant->id,
                'utilisateur_id' => $apprenant->utilisateur_id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
            ],
            'certificats' => $certificatsFormates,
            'total' => $certificatsFormates->count(),
            'statistiques' => [
                'total_certificats' => $tousCertificats->count(),
                'certificats_accessibles' => $certificats->count(),
                'modules_accessibles' => count($moduleIds)
            ],
            'debug' => [
                'user_id' => $user->id,
                'apprenant_id' => $apprenant->id,
                'utilisateur_id_in_apprenant' => $apprenant->utilisateur_id,
                'modules_accessibles' => $moduleIds
            ]
        ], 200);
    }

    public function create()
    {
        return response()->json(['message' => 'Endpoint pour création de certificat'], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $certificat = Certificat::create($data);
        return response()->json(['certificat' => $certificat, 'message' => 'Certificat créé avec succès'], 201);
    }

    public function show(Certificat $certificat)
    {
        return response()->json(['certificat' => $certificat], 200);
    }

    public function edit(Certificat $certificat)
    {
        return response()->json(['certificat' => $certificat], 200);
    }

    public function update(Request $request, Certificat $certificat)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $certificat->update($data);
        return response()->json(['certificat' => $certificat, 'message' => 'Certificat mis à jour avec succès'], 200);
    }

    public function destroy(Certificat $certificat)
    {
        $certificat->delete();
        return response()->json(null, 204);
    }
}

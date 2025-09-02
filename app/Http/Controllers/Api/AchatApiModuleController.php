<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class AchatApiModuleController extends Controller
{
    // Affiche les modules du niveau de l'apprenant connecté
    public function showAchat()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        
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

        $modules = Module::where('niveau_id', $apprenant->niveau_id)
            ->with(['formateur.utilisateur', 'niveau'])
            ->orderBy('date_debut')
            ->get();

        $modulesFormates = $modules->map(function ($module) use ($apprenant) {
            $paiementExistant = $apprenant->paiements()
                ->where('module_id', $module->id)
                ->where('statut', 'valide')
                ->first();

            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'discipline' => $module->discipline,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                    'ordre' => $module->niveau->ordre
                ] : null,
                'formateur' => $module->formateur && $module->formateur->utilisateur ? [
                    'id' => $module->formateur->id,
                    'nom' => $module->formateur->utilisateur->nom,
                    'prenom' => $module->formateur->utilisateur->prenom,
                    'email' => $module->formateur->utilisateur->email
                ] : null,
                'is_paye' => $paiementExistant ? true : false,
                'paiement_existant' => $paiementExistant ? [
                    'id' => $paiementExistant->id,
                    'montant' => $paiementExistant->montant,
                    'methode' => $paiementExistant->methode,
                    'reference' => $paiementExistant->reference,
                    'date_paiement' => $paiementExistant->date_paiement,
                    'statut' => $paiementExistant->statut
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
            'total_modules' => $modules->count(),
            'modules_payes' => $modulesFormates->where('is_paye', true)->count(),
            'modules_disponibles' => $modulesFormates->where('is_paye', false)->count(),
            'modules' => $modulesFormates
        ], 200);
    }

    // Paiement de tous les modules du niveau de l'apprenant
    public function payerModule(Request $request, $moduleId = null)
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        if (!$apprenant) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 401);
        }

        // Validation des données
        $request->validate([
            'methode' => 'nullable|string|in:mobile_money,carte_bancaire,especes,manuel',
            'reference' => 'nullable|string|max:255'
        ]);

        // Vérifier si l'apprenant a un niveau assigné
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
        $modules = Module::where('niveau_id', $apprenant->niveau_id)
            ->with(['niveau', 'formateur.utilisateur'])
            ->get();

        if ($modules->isEmpty()) {
            return response()->json([
                'error' => 'Aucun module trouvé pour ce niveau',
                'niveau' => [
                    'id' => $apprenant->niveau_id,
                    'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Niveau inconnu'
                ]
            ], 404);
        }

        // Calculer le prix total de tous les modules
        $prixTotal = $modules->sum('prix');
        
        // Vérifier si l'apprenant a déjà payé des modules de ce niveau
        $modulesDejaPayes = $apprenant->paiements()
            ->whereIn('module_id', $modules->pluck('id'))
            ->where('statut', 'valide')
            ->pluck('module_id')
            ->toArray();

        if (!empty($modulesDejaPayes)) {
            $modulesPayes = $modules->whereIn('id', $modulesDejaPayes);
            return response()->json([
                'error' => 'Vous avez déjà payé certains modules de ce niveau',
                'modules_deja_payes' => $modulesPayes->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'prix' => $module->prix
                    ];
                }),
                'total_deja_paye' => $modulesPayes->sum('prix'),
                'prix_total_niveau' => $prixTotal
            ], 400);
        }

        // Générer une référence unique pour tous les paiements
        $reference = $request->input('reference', 'NIVEAU-' . uniqid());
        $methode = $request->input('methode', 'manuel');

        // Créer un paiement pour chaque module du niveau
        $paiements = [];
        foreach ($modules as $module) {
            $paiement = $apprenant->paiements()->create([
                'module_id' => $module->id,
                'montant' => $module->prix,
                'methode' => $methode,
                'reference' => $reference,
                'date_paiement' => now(),
                'statut' => 'valide',
            ]);
            $paiements[] = $paiement;
        }

        // Formater la réponse
        $modulesFormates = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'prix' => $module->prix,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                    'ordre' => $module->niveau->ordre
                ] : null,
                'formateur' => $module->formateur && $module->formateur->utilisateur ? [
                    'id' => $module->formateur->id,
                    'nom' => $module->formateur->utilisateur->nom,
                    'prenom' => $module->formateur->utilisateur->prenom,
                    'email' => $module->formateur->utilisateur->email
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Paiement du niveau enregistré avec succès !',
            'paiement_niveau' => [
                'reference' => $reference,
                'methode' => $methode,
                'date_paiement' => now(),
                'statut' => 'valide',
                'prix_total' => $prixTotal,
                'nombre_modules' => $modules->count(),
                'nombre_paiements' => count($paiements)
            ],
            'niveau' => [
                'id' => $apprenant->niveau_id,
                'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Niveau inconnu',
                'ordre' => $apprenant->niveau ? $apprenant->niveau->ordre : null
            ],
            'modules_payes' => $modulesFormates,
            'paiements_crees' => $paiements
        ], 201);
    }
}

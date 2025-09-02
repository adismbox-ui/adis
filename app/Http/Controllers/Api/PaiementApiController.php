<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;

class PaiementApiController extends Controller
{
    public function index()
    {
        $paiements = Paiement::all();
        return response()->json(['paiements' => $paiements], 200);
    }

    public function create()
    {
        return response()->json(['message' => 'Endpoint pour création de paiement'], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        
        if (!$apprenant) {
            return response()->json(['error' => 'Aucun apprenant connecté.'], 401);
        }

        // Vérifier si on veut payer tout le niveau ou un module spécifique
        if ($request->has('payer_tout_niveau') && $request->boolean('payer_tout_niveau')) {
            // Payer tous les modules du niveau
            return $this->payerToutNiveau($request, $apprenant);
        } else {
            // Payer un module spécifique
            return $this->payerModuleSpecifique($request, $apprenant);
        }
    }

    /**
     * Payer un module spécifique
     */
    private function payerModuleSpecifique(Request $request, $apprenant)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'montant' => 'required|numeric|min:0',
            'methode' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        // Vérifier que le module appartient au niveau de l'apprenant
        $module = \App\Models\Module::find($data['module_id']);
        if (!$module) {
            return response()->json(['error' => 'Module non trouvé.'], 404);
        }

        if ($module->niveau_id !== $apprenant->niveau_id) {
            return response()->json([
                'error' => 'Ce module n\'appartient pas à votre niveau actuel.',
                'votre_niveau_id' => $apprenant->niveau_id,
                'module_niveau_id' => $module->niveau_id
            ], 403);
        }

        // Vérifier si l'apprenant a déjà payé ce module
        $paiementExistant = Paiement::where('apprenant_id', $apprenant->id)
            ->where('module_id', $data['module_id'])
            ->whereIn('statut', ['en_attente', 'valide'])
            ->first();

        if ($paiementExistant) {
            return response()->json([
                'error' => 'Vous avez déjà une demande de paiement ou un paiement validé pour ce module.',
                'paiement_existant' => [
                    'id' => $paiementExistant->id,
                    'statut' => $paiementExistant->statut,
                    'date_paiement' => $paiementExistant->date_paiement
                ]
            ], 409);
        }

        $data['apprenant_id'] = $apprenant->id;
        $data['date_paiement'] = now();
        $data['statut'] = 'en_attente';

        $paiement = Paiement::create($data);

        return response()->json([
            'success' => true,
            'paiement' => [
                'id' => $paiement->id,
                'apprenant_id' => $paiement->apprenant_id,
                'module_id' => $paiement->module_id,
                'montant' => $paiement->montant,
                'date_paiement' => $paiement->date_paiement,
                'statut' => $paiement->statut,
                'methode' => $paiement->methode,
                'reference' => $paiement->reference,
                'created_at' => $paiement->created_at,
                'updated_at' => $paiement->updated_at
            ],
            'module' => [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'prix' => $module->prix,
                'niveau' => [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom
                ]
            ],
            'message' => 'Paiement enregistré, en attente de validation.'
        ], 201);
    }

    /**
     * Payer tous les modules du niveau de l'apprenant
     */
    private function payerToutNiveau(Request $request, $apprenant)
    {
        $data = $request->validate([
            'methode' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Récupérer le niveau actuel de l'apprenant
        $niveau = \App\Models\Niveau::find($apprenant->niveau_id);
        if (!$niveau) {
            return response()->json(['error' => 'Niveau actuel non trouvé.'], 404);
        }

        // Récupérer tous les modules du niveau
        $modules = \App\Models\Module::where('niveau_id', $niveau->id)->get();
        if ($modules->isEmpty()) {
            return response()->json(['error' => 'Aucun module trouvé pour ce niveau.'], 404);
        }

        // Vérifier les paiements existants
        $paiementsExistants = Paiement::where('apprenant_id', $apprenant->id)
            ->whereIn('module_id', $modules->pluck('id'))
            ->whereIn('statut', ['en_attente', 'valide'])
            ->get();

        if ($paiementsExistants->isNotEmpty()) {
            return response()->json([
                'error' => 'Vous avez déjà des paiements en cours ou validés pour certains modules de ce niveau.',
                'paiements_existants' => $paiementsExistants->map(function($paiement) {
                    return [
                        'id' => $paiement->id,
                        'module_id' => $paiement->module_id,
                        'statut' => $paiement->statut,
                        'date_paiement' => $paiement->date_paiement
                    ];
                })
            ], 409);
        }

        // Calculer le montant total
        $montantTotal = $modules->sum('prix');

        // Créer les paiements pour chaque module
        $paiementsCrees = [];
        $referenceBase = $data['reference'] ?? 'PAIEMENT_NIVEAU_' . $niveau->id . '_' . time();

        foreach ($modules as $index => $module) {
            $paiement = Paiement::create([
                'apprenant_id' => $apprenant->id,
                'module_id' => $module->id,
                'montant' => $module->prix,
                'date_paiement' => now(),
                'statut' => 'en_attente',
                'methode' => $data['methode'],
                'reference' => $referenceBase . '_' . ($index + 1),
                'notes' => $data['notes'] ?? "Paiement automatique pour le niveau {$niveau->nom}"
            ]);

            $paiementsCrees[] = [
                'id' => $paiement->id,
                'module_id' => $module->id,
                'module_titre' => $module->titre,
                'montant' => $module->prix,
                'reference' => $paiement->reference,
                'statut' => $paiement->statut
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiements pour tous les modules du niveau créés avec succès.',
            'niveau' => [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description
            ],
            'total_modules' => $modules->count(),
            'montant_total' => $montantTotal,
            'methode_paiement' => $data['methode'],
            'paiements_crees' => $paiementsCrees,
            'created_at' => now()
        ], 201);
    }

    public function show(Paiement $paiement)
    {
        return response()->json(['paiement' => $paiement], 200);
    }

    public function edit(Paiement $paiement)
    {
        return response()->json(['paiement' => $paiement], 200);
    }

    public function update(Request $request, Paiement $paiement)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $paiement->update($data);
        return response()->json(['paiement' => $paiement, 'message' => 'Paiement mis à jour avec succès'], 200);
    }

    public function destroy(Paiement $paiement)
    {
        $paiement->delete();
        return response()->json(null, 204);
    }
}

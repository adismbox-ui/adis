<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Inscription;

class ApiPaiementController extends Controller
{
    /**
     * Initialise un paiement
     */
    public function initialize(Request $request)
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
            'module_id' => 'required|exists:modules,id',
            'montant' => 'required|numeric|min:0',
            'methode' => 'nullable|string',
        ]);

        $module = Module::find($data['module_id']);

        if (!$module) {
            return response()->json([
                'success' => false,
                'error' => 'Module non trouvé'
            ], 404);
        }

        // Créer le paiement
        $paiement = Paiement::create([
            'apprenant_id' => $apprenant->id,
            'module_id' => $data['module_id'],
            'montant' => $data['montant'],
            'statut' => 'en_attente',
            'methode' => $data['methode'] ?? 'cinetpay',
            'date_paiement' => now(),
        ]);

        // TODO: Intégrer CinetPay ici pour obtenir l'URL de paiement
        // Pour l'instant, on retourne juste le paiement créé

        return response()->json([
            'success' => true,
            'message' => 'Paiement initialisé',
            'paiement' => [
                'id' => $paiement->id,
                'montant' => $paiement->montant,
                'statut' => $paiement->statut,
                'module' => [
                    'id' => $module->id,
                    'titre' => $module->titre,
                ],
            ],
            // 'payment_url' => 'https://secure-checkout.cinetpay.com/...', // À implémenter avec CinetPay
        ], 201);
    }

    /**
     * Récupère le statut d'un paiement
     */
    public function getStatus(Request $request, $id)
    {
        $user = $request->user();
        $apprenant = $user->apprenant;

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Profil apprenant non trouvé'
            ], 404);
        }

        $paiement = Paiement::where('id', $id)
            ->where('apprenant_id', $apprenant->id)
            ->with('module')
            ->first();

        if (!$paiement) {
            return response()->json([
                'success' => false,
                'error' => 'Paiement non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'paiement' => [
                'id' => $paiement->id,
                'montant' => $paiement->montant,
                'statut' => $paiement->statut,
                'methode' => $paiement->methode,
                'date_paiement' => $paiement->date_paiement,
                'module' => $paiement->module ? [
                    'id' => $paiement->module->id,
                    'titre' => $paiement->module->titre,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Gère les notifications de paiement (webhook CinetPay)
     */
    public function handleNotification(Request $request)
    {
        // TODO: Implémenter la logique de webhook CinetPay
        // Vérifier la signature, mettre à jour le statut du paiement, créer l'inscription si succès
        
        $data = $request->all();
        
        // Exemple de traitement
        if (isset($data['cpm_trans_id'])) {
            $paiement = Paiement::where('reference', $data['cpm_trans_id'])->first();
            
            if ($paiement) {
                if ($data['cpm_result'] === '00') {
                    $paiement->statut = 'valide';
                    $paiement->save();
                    
                    // Créer l'inscription si elle n'existe pas
                    $inscription = Inscription::firstOrCreate(
                        [
                            'apprenant_id' => $paiement->apprenant_id,
                            'module_id' => $paiement->module_id,
                        ],
                        [
                            'statut' => 'valide',
                            'date_inscription' => now(),
                        ]
                    );
                } else {
                    $paiement->statut = 'echec';
                    $paiement->save();
                }
            }
        }

        return response()->json(['success' => true], 200);
    }
}


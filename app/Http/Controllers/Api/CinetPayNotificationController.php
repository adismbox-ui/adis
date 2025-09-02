<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Paiement;
use App\Models\Module;
use App\Models\User;
use App\Services\CinetPayService;

class CinetPayNotificationController extends Controller
{
    protected $cinetpayService;

    public function __construct(CinetPayService $cinetpayService)
    {
        $this->cinetpayService = $cinetpayService;
    }

    /**
     * Traiter les notifications de paiement CinetPay
     */
    public function handleNotification(Request $request)
    {
        try {
            Log::info('Notification CinetPay reçue', $request->all());

            // Valider la notification
            $this->cinetpayService->validateNotification($request->all());

            // Récupérer les données de la notification
            $transactionId = $request->input('transaction_id');
            $status = $request->input('status');
            $amount = $request->input('amount');
            $currency = $request->input('currency');
            $paymentMethod = $request->input('payment_method');
            $operator = $request->input('operator');
            $phone = $request->input('phone');
            $description = $request->input('description');

            // Vérifier que la transaction existe
            $paiement = Paiement::where('reference', $transactionId)->first();
            
            if (!$paiement) {
                Log::error('Paiement non trouvé pour la transaction: ' . $transactionId);
                return response()->json(['error' => 'Transaction non trouvée'], 404);
            }

            // Mettre à jour le statut du paiement
            $paiement->statut = $this->mapCinetPayStatus($status);
            
            // Mettre à jour les informations de paiement
            $informationsPaiement = json_decode($paiement->informations_paiement, true) ?? [];
            $informationsPaiement['cinetpay_data'] = $request->all();
            $informationsPaiement['operator'] = $operator;
            $informationsPaiement['phone'] = $phone;
            $informationsPaiement['payment_method'] = $paymentMethod ?? 'cinetpay';
            
            $paiement->informations_paiement = json_encode($informationsPaiement);
            $paiement->methode = $paymentMethod ?? 'cinetpay';
            $paiement->save();

            // Si le paiement est réussi, créer l'inscription
            if ($status === 'SUCCESS') {
                $this->createInscription($paiement);
            }

            Log::info('Notification CinetPay traitée avec succès', [
                'transaction_id' => $transactionId,
                'status' => $status,
                'paiement_id' => $paiement->id
            ]);

            return response()->json(['success' => true, 'message' => 'Notification traitée']);

        } catch (\Exception $e) {
            Log::error('Erreur traitement notification CinetPay: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne'], 500);
        }
    }

    /**
     * Initialiser un paiement depuis l'API
     */
    public function initializePayment(Request $request)
    {
        try {
            // Récupérer l'utilisateur connecté
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $request->validate([
                'amount' => 'required|numeric|min:1',
                'description' => 'required|string|max:255',
                'module_id' => 'required|exists:modules,id',
                'customer_name' => 'nullable|string|max:255',
                'customer_email' => 'nullable|email',
                'customer_phone' => 'nullable|string|max:20',
            ]);

            // Générer un ID de transaction unique
            $transactionId = $this->cinetpayService->generateTransactionId();

            // Préparer les données pour CinetPay
            $paymentData = [
                'transaction_id' => $transactionId,
                'amount' => $request->amount,
                'description' => $request->description,
                'customer_name' => $request->customer_name ?? $user->prenom . ' ' . $user->nom,
                'customer_email' => $request->customer_email ?? $user->email,
                'customer_phone' => $request->customer_phone ?? $user->telephone,
            ];

            // Initialiser le paiement avec CinetPay
            $result = $this->cinetpayService->initializePayment($paymentData);

            if ($result['success']) {
                // Récupérer l'apprenant associé à l'utilisateur
                $apprenant = $user->apprenant;
                if (!$apprenant) {
                    throw new \Exception('Aucun apprenant associé à cet utilisateur');
                }

                // Enregistrer le paiement en attente
                $paiement = Paiement::create([
                    'apprenant_id' => $apprenant->id,
                    'module_id' => $request->module_id,
                    'montant' => $request->amount,
                    'date_paiement' => now(),
                    'statut' => 'en_attente',
                    'methode' => 'cinetpay',
                    'reference' => $transactionId,
                    'notes' => 'Paiement CinetPay - ' . $request->description,
                    'informations_paiement' => json_encode([
                        'transaction_id' => $transactionId,
                        'cinetpay_token' => $result['token'] ?? null,
                        'payment_method' => 'cinetpay',
                        'currency' => config('cinetpay.currency'),
                    ]),
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'transaction_id' => $transactionId,
                    'paiement_id' => $paiement->id,
                    'message' => 'Paiement initialisé avec succès',
                ]);
            } else {
                throw new \Exception($result['message'] ?? 'Erreur lors de l\'initialisation du paiement');
            }

        } catch (\Exception $e) {
            Log::error('Erreur initialisation paiement: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mapper les statuts CinetPay vers les statuts internes
     */
    private function mapCinetPayStatus($cinetpayStatus)
    {
        $statusMap = [
            'PENDING' => 'en_attente',
            'SUCCESS' => 'valide',
            'FAILED' => 'echoue',
            'CANCELED' => 'annule',
        ];

        return $statusMap[$cinetpayStatus] ?? 'en_attente';
    }

    /**
     * Créer une inscription après paiement réussi
     */
    private function createInscription($paiement)
    {
        try {
            // Vérifier si l'inscription existe déjà
            $existingInscription = \App\Models\Inscription::where([
                'apprenant_id' => $paiement->apprenant_id,
                'module_id' => $paiement->module_id,
            ])->first();

            if ($existingInscription) {
                Log::info('Inscription déjà existante', [
                    'paiement_id' => $paiement->id,
                    'inscription_id' => $existingInscription->id
                ]);
                return;
            }

            // Créer l'inscription
            $inscription = \App\Models\Inscription::create([
                'apprenant_id' => $paiement->apprenant_id,
                'module_id' => $paiement->module_id,
                'date_inscription' => now(),
                'statut' => 'valide',
            ]);

            Log::info('Inscription créée après paiement', [
                'paiement_id' => $paiement->id,
                'inscription_id' => $inscription->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur création inscription: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|string',
            ]);

            $transactionId = $request->input('transaction_id');
            
            // Vérifier d'abord dans notre base de données
            $paiement = Paiement::where('reference', $transactionId)->first();
            
            if (!$paiement) {
                return response()->json(['error' => 'Transaction non trouvée'], 404);
            }

            // Vérifier le statut avec CinetPay
            $cinetpayStatus = $this->cinetpayService->checkPaymentStatus($transactionId);

            return response()->json([
                'success' => true,
                'paiement' => [
                    'id' => $paiement->id,
                    'transaction_id' => $paiement->reference,
                    'status' => $paiement->statut,
                    'cinetpay_status' => $cinetpayStatus['status'],
                    'amount' => $paiement->montant,
                    'currency' => 'XOF',
                    'created_at' => $paiement->created_at,
                    'updated_at' => $paiement->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur vérification statut: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne'], 500);
        }
    }

    /**
     * Mettre à jour le statut d'un paiement
     */
    public function updatePaymentStatus(Request $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|string',
                'status' => 'required|string',
            ]);

            $transactionId = $request->input('transaction_id');
            $status = $request->input('status');
            $cinetpayData = $request->input('cinetpay_data');

            $paiement = Paiement::where('reference', $transactionId)->first();
            
            if (!$paiement) {
                return response()->json(['error' => 'Transaction non trouvée'], 404);
            }

            $paiement->statut = $this->mapCinetPayStatus($status);
            if ($cinetpayData) {
                $informationsPaiement = json_decode($paiement->informations_paiement, true) ?? [];
                $informationsPaiement['cinetpay_data'] = $cinetpayData;
                $paiement->informations_paiement = json_encode($informationsPaiement);
            }
            $paiement->save();

            // Si le paiement est réussi, créer l'inscription
            if ($status === 'SUCCESS') {
                $this->createInscription($paiement);
            }

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'paiement' => [
                    'id' => $paiement->id,
                    'transaction_id' => $paiement->reference,
                    'status' => $paiement->statut,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour statut: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne'], 500);
        }
    }

    /**
     * Page de succès après paiement
     */
    public function paymentSuccess(Request $request)
    {
        try {
            $transactionId = $request->query('transaction_id');
            
            if (!$transactionId) {
                return response()->json(['error' => 'Transaction ID manquant'], 400);
            }

            $paiement = Paiement::where('reference', $transactionId)->first();
            
            if (!$paiement) {
                return response()->json(['error' => 'Transaction non trouvée'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement effectué avec succès',
                'paiement' => [
                    'id' => $paiement->id,
                    'transaction_id' => $paiement->reference,
                    'status' => $paiement->statut,
                    'amount' => $paiement->montant,
                    'currency' => 'XOF',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur page succès: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne'], 500);
        }
    }

    /**
     * Page d'annulation après paiement
     */
    public function paymentCancel(Request $request)
    {
        try {
            $transactionId = $request->query('transaction_id');
            
            if (!$transactionId) {
                return response()->json(['error' => 'Transaction ID manquant'], 400);
            }

            $paiement = Paiement::where('reference', $transactionId)->first();
            
            if (!$paiement) {
                return response()->json(['error' => 'Transaction non trouvée'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement annulé',
                'paiement' => [
                    'id' => $paiement->id,
                    'transaction_id' => $paiement->reference,
                    'status' => $paiement->statut,
                    'amount' => $paiement->montant,
                    'currency' => 'XOF',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur page annulation: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne'], 500);
        }
    }
}

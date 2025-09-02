<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Paiement;
use App\Models\Apprenant;
use App\Models\Inscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AchatApiController extends Controller
{
    /**
     * Affiche la page d'achat.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $apprenant = null;
        $modules = collect();
        if ($user) {
            $apprenant = \App\Models\Apprenant::where('utilisateur_id', $user->id)->first();
            if ($apprenant && $apprenant->niveau_id) {
                $modules = Module::with(['niveau', 'formateur.utilisateur'])
                    ->where('niveau_id', $apprenant->niveau_id)
                    ->get()
                    ->filter(function($m) use ($apprenant) {
                        return !$apprenant->paiements()->where('module_id', $m->id)->where('statut', 'valide')->exists();
                    });
            }
        }
        return response()->json(['modules' => $modules], 200);
    }

    /**
     * Traite la demande de paiement.
     */
    public function traiterPaiement(Request $request)
    {
        try {
            $request->validate([
                'modules' => 'required|array|min:2|max:2',
                'modules.*' => 'required|exists:modules,id',
                'methode_paiement' => 'required|in:carte,mobile_money,especes,virement,cheque',
                'notes' => 'nullable|string|max:500',
            ]);
            if ($request->methode_paiement === 'carte') {
                $request->validate([
                    'numero_carte' => 'required|string|min:13|max:19',
                    'nom_titulaire' => 'required|string|min:3|max:100',
                    'date_expiration' => 'required|string|regex:/^\d{2}\/\d{2}$/',
                    'cvv' => 'required|string|min:3|max:4',
                ]);
            } elseif ($request->methode_paiement === 'mobile_money') {
                $request->validate([
                    'operateur' => 'required|in:moov,mtn',
                    'numero_mobile' => 'required|string|size:10',
                ]);
            } elseif ($request->methode_paiement === 'virement') {
                $request->validate([
                    'reference_virement' => 'required|string|min:3|max:100',
                ]);
            }
            $utilisateur = Auth::user();
            if (!$utilisateur) {
                Log::error('AchatController: Aucun utilisateur connecté');
                return response()->json(['error' => 'Vous devez être connecté pour effectuer cette action.'], 401);
            }
            $apprenant = Apprenant::where('utilisateur_id', $utilisateur->id)->first();
            if (!$apprenant) {
                Log::error('AchatController: Aucun apprenant trouvé pour l\'utilisateur ID: ' . $utilisateur->id);
                return response()->json(['error' => 'Vous devez être un apprenant pour effectuer cette action.'], 401);
            }
            $modules = Module::whereIn('id', $request->modules)->get();
            $montantTotal = $modules->sum('prix');
            $paiements = [];
            foreach ($modules as $module) {
                $paiement = Paiement::create([
                    'apprenant_id' => $apprenant->id,
                    'module_id' => $module->id,
                    'montant' => $module->prix ?? 0,
                    'date_paiement' => now(),
                    'statut' => 'en_attente',
                    'methode' => $request->methode_paiement,
                    'reference' => $this->genererReference($request->methode_paiement, $request),
                    'notes' => $request->notes,
                    'informations_paiement' => $this->getInformationsPaiement($request),
                ]);
                $paiements[] = $paiement;
            }
            Log::info('Paiement groupé créé avec succès', [
                'apprenant_id' => $apprenant->id,
                'modules' => $modules->pluck('id'),
                'montant_total' => $montantTotal
            ]);
            return response()->json([
                'paiements' => $paiements,
                'message' => 'Votre demande de paiement a bien été envoyée à l\'administrateur pour validation.'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation dans AchatController', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur dans AchatController::traiterPaiement', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Une erreur est survenue lors du traitement de votre demande. Veuillez réessayer.'], 500);
        }
    }

    private function genererReference($methode, $request)
    {
        $prefix = strtoupper(substr($methode, 0, 3));
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return $prefix . '-' . $timestamp . '-' . $random;
    }

    private function getInformationsPaiement($request)
    {
        $informations = [];
        switch ($request->methode_paiement) {
            case 'carte':
                $informations = [
                    'numero_carte' => substr($request->numero_carte, -4),
                    'nom_titulaire' => $request->nom_titulaire,
                    'date_expiration' => $request->date_expiration,
                    'type_carte' => $this->detecterTypeCarte($request->numero_carte),
                ];
                break;
            case 'mobile_money':
                $informations = [
                    'operateur' => $request->operateur,
                    'numero_mobile' => $request->numero_mobile,
                ];
                break;
            case 'virement':
                $informations = [
                    'reference_virement' => $request->reference_virement,
                ];
                break;
            case 'especes':
                $informations = [
                    'commentaire' => 'Paiement en espèces - à valider en personne',
                ];
                break;
            case 'cheque':
                $informations = [
                    'commentaire' => 'Paiement par chèque - à valider en personne',
                ];
                break;
        }
        return json_encode($informations);
    }

    private function detecterTypeCarte($numero)
    {
        $numero = preg_replace('/\s/', '', $numero);
        if (preg_match('/^4/', $numero)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5]|^2[2-7]|^222[1-9]|^22[3-9]|^2[3-6]|^27[0-1]|^2720/', $numero)) {
            return 'Mastercard';
        } elseif (preg_match('/^3[47]/', $numero)) {
            return 'American Express';
        } else {
            return 'Autre';
        }
    }
}

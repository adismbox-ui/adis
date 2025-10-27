<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Paiement;
use App\Models\Apprenant;
use App\Models\Inscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AchatController extends Controller
{
    /**
     * Affiche la page d'achat.
     */
    public function index(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        $apprenant = null;
        $modules = collect();
        if ($user) {
            $apprenant = \App\Models\Apprenant::where('utilisateur_id', $user->id)->first();
            if ($apprenant && $apprenant->niveau_id) {
                // Récupérer les modules obligatoires du niveau non encore payés
                $modules = Module::with(['niveau', 'formateur.utilisateur'])
                    ->where('niveau_id', $apprenant->niveau_id)
                    ->get()
                    ->filter(function($m) use ($apprenant) {
                        return !$apprenant->paiements()->where('module_id', $m->id)->where('statut', 'valide')->exists();
                    });
            }
        }
        return view('achat', compact('modules'));
    }

    /**
     * Envoie une demande d'achat à l'admin en créant des paiements en attente
     * pour tous les modules disponibles du niveau de l'apprenant.
     */
    public function envoyerDemande(Request $request)
    {
        $utilisateur = Auth::user();
        if (!$utilisateur) {
            return back()->with('error', 'Vous devez être connecté pour envoyer une demande.');
        }
        $apprenant = Apprenant::where('utilisateur_id', $utilisateur->id)->first();
        if (!$apprenant) {
            return back()->with('error', 'Vous devez être un apprenant pour envoyer une demande.');
        }

        if (!$apprenant->niveau_id) {
            return back()->with('error', "Votre niveau n'est pas défini. Impossible d'envoyer une demande.");
        }

        // Récupérer les modules du niveau non encore payés
        $modules = Module::with(['niveau', 'formateur.utilisateur'])
            ->where('niveau_id', $apprenant->niveau_id)
            ->get()
            ->filter(function($m) use ($apprenant) {
                return !$apprenant->paiements()
                    ->where('module_id', $m->id)
                    ->where('statut', 'valide')
                    ->exists();
            });

        if ($modules->isEmpty()) {
            return back()->with('error', 'Aucun module à demander pour votre niveau.');
        }

        // Créer des demandes de paiement en attente pour chaque module sans doublon
        $nbDemandesCreees = 0;
        foreach ($modules as $module) {
            $existeDemande = Paiement::where('apprenant_id', $apprenant->id)
                ->where('module_id', $module->id)
                ->where('statut', 'en_attente')
                ->exists();
            if ($existeDemande) {
                continue;
            }
            Paiement::create([
                'apprenant_id' => $apprenant->id,
                'module_id' => $module->id,
                'montant' => $module->prix ?? 0,
                'date_paiement' => now(),
                'statut' => 'en_attente',
                'methode' => 'demande',
                'reference' => $this->genererReference('demande', $request),
                'notes' => 'Demande transmise depuis la page achat',
                'informations_paiement' => json_encode(['source' => 'achat_page']),
            ]);
            $nbDemandesCreees++;
        }

        if ($nbDemandesCreees === 0) {
            return back()->with('success', 'Vos demandes étaient déjà en attente de validation.');
        }

        return back()->with('success', 'Votre demande a été envoyée. Un administrateur va la traiter.');
    }

    /**
     * Traite la demande de paiement.
     */
    public function traiterPaiement(Request $request)
    {
        $start = microtime(true);
        try {
            // Validation de base : on exige un tableau de modules
            $request->validate([
                'modules' => 'required|array|min:2|max:2',
                'modules.*' => 'required|exists:modules,id',
                'methode_paiement' => 'required|in:carte,mobile_money,especes,virement,cheque',
                'notes' => 'nullable|string|max:500',
            ]);

            // Validation spécifique selon le mode de paiement
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

            // Récupérer l'apprenant connecté
            $utilisateur = Auth::user();
            if (!$utilisateur) {
                \Log::error('AchatController: Aucun utilisateur connecté');
                return back()->with('error', 'Vous devez être connecté pour effectuer cette action.');
            }
            $apprenant = Apprenant::where('utilisateur_id', $utilisateur->id)->first();
            if (!$apprenant) {
                \Log::error('AchatController: Aucun apprenant trouvé pour l\'utilisateur ID: ' . $utilisateur->id);
                return back()->with('error', 'Vous devez être un apprenant pour effectuer cette action.');
            }

            // Calcul du montant total
            $modules = Module::whereIn('id', $request->modules)->get();
            $montantTotal = $modules->sum('prix');

            // Créer un paiement groupé (statut en_attente)
            foreach ($modules as $module) {
                Paiement::create([
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
            }
            $afterPaiement = microtime(true);
            \Log::info('Performance Paiement', [
                'step' => 'paiement_cree',
                'duree_paiement' => $afterPaiement - $start
            ]);

            \Log::info('Paiement groupé créé avec succès', [
                'apprenant_id' => $apprenant->id,
                'modules' => $modules->pluck('id'),
                'montant_total' => $montantTotal
            ]);
            $end = microtime(true);
            \Log::info('Performance Paiement', [
                'step' => 'fin_traiterPaiement',
                'duree_totale' => $end - $start
            ]);

            // Rediriger l'apprenant vers son dashboard avec un message de succès
            return redirect()->route('apprenants.dashboard')->with('success', 'Votre demande de paiement a bien été envoyée à l\'administrateur pour validation.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation dans AchatController', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Erreur dans AchatController::traiterPaiement', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Une erreur est survenue lors du traitement de votre demande. Veuillez réessayer.');
        }
    }

    /**
     * Génère une référence unique pour le paiement.
     */
    private function genererReference($methode, $request)
    {
        $prefix = strtoupper(substr($methode, 0, 3));
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . '-' . $timestamp . '-' . $random;
    }

    /**
     * Récupère les informations de paiement selon la méthode.
     */
    private function getInformationsPaiement($request)
    {
        $informations = [];

        switch ($request->methode_paiement) {
            case 'carte':
                $informations = [
                    'numero_carte' => substr($request->numero_carte, -4), // Seulement les 4 derniers chiffres
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

    /**
     * Détecte le type de carte basé sur le numéro.
     */
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

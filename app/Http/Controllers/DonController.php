<?php

namespace App\Http\Controllers;

use App\Models\Don;
use App\Models\Projet;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\RecuDonMail;

class DonController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projetsEnCours = Projet::where('statut', 'en_cours')->get();
        $projetsAFinancer = Projet::where('statut', 'a_financer')->get();
        return view('projets.don', compact('projetsEnCours', 'projetsAFinancer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('DonController@store: Début de la méthode', ['request' => $request->all()]);
        
        $request->validate([
            'nom_donateur' => 'required|string|max:255',
            'email_donateur' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'montant' => 'required',
            'montant_autre' => 'nullable|numeric|min:100',
            'type_don' => 'required|in:ponctuel,mensuel',
            'projet_id' => 'required',
            'mode_paiement' => 'required|in:carte,virement,mobile',
            'devis_email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:1000',
            'conditions' => 'required|accepted'
        ]);

        try {
            \Log::info('DonController@store: Validation réussie');
            
            DB::beginTransaction();
            
            // Calculer le montant final
            $montantFinal = $request->montant === 'autre' ? $request->montant_autre : $request->montant;
            
            // Convertir projet_id 'fonds_general' en null, sinon vérifier que c'est un entier valide
            $projetId = $request->projet_id === 'fonds_general' ? null : (is_numeric($request->projet_id) ? (int) $request->projet_id : null);
            
            // Créer le don
            $don = Don::create([
                'nom_donateur' => $request->nom_donateur,
                'email_donateur' => $request->email_donateur,
                'telephone' => $request->telephone,
                'montant' => $montantFinal,
                'type_don' => $request->type_don,
                'projet_id' => $projetId,
                'mode_paiement' => $request->mode_paiement,
                'recu_demande' => true,
                'message' => $request->message,
                'statut' => 'en_attente',
                'date_don' => Carbon::now(),
                'numero_reference' => 'DON-' . date('Ymd') . '-' . strtoupper(uniqid()),
            ]);
            
            \Log::info('DonController@store: Don créé', ['don_id' => $don->id]);

            // Créer une notification pour l'admin
            try {
                $projetNom = $request->projet_id === 'fonds_general' ? 'Fonds général' : (Projet::find($projetId)->intitule ?? 'Projet inconnu');
                
                Notification::create([
                    'type' => 'don',
                    'title' => 'Nouveau don reçu',
                    'message' => "Un nouveau don de {$montantFinal} F CFA a été reçu de {$request->nom_donateur} pour le projet : {$projetNom}",
                    'icon' => 'fas fa-hand-holding-heart',
                    'color' => 'success',
                    'data' => json_encode([
                        'don_id' => $don->id,
                        'donateur' => $request->nom_donateur,
                        'montant' => $montantFinal,
                        'projet' => $projetNom,
                        'mode_paiement' => $request->mode_paiement,
                        'type_don' => $request->type_don
                    ]),
                    'user_id' => 1,
                    'admin_id' => 1,
                ]);
                \Log::info('DonController@store: Notification admin créée');
            } catch (\Exception $e) {
                \Log::error('DonController@store: Erreur création notification admin', ['error' => $e->getMessage()]);
            }

            // Envoi du reçu détaillé au donateur (avec copie cachée à l'adresse d'expéditeur)
            try {
                Mail::to($request->email_donateur)
                    ->bcc(config('mail.from.address'))
                    ->send(new RecuDonMail($don, $projetNom));
                \Log::info('DonController@store: Email reçu envoyé');
            } catch (\Throwable $e) {
                \Log::error('DonController@store: Erreur envoi email reçu', ['error' => $e->getMessage()]);
            }

            DB::commit();
            \Log::info('DonController@store: Succès complet');

            return redirect()->route('projets.don')
                ->with('success', "Votre don de " . number_format($montantFinal, 0, ',', ' ') . " F CFA a été enregistré avec succès ! Votre reçu vous a été envoyé par email.");

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('DonController@store: Erreur générale', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return redirect()->route('projets.don')
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du don. Veuillez réessayer.');
        }
    }
}

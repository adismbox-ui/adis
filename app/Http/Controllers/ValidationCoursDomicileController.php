<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\DemandeCoursMaison;

class ValidationCoursDomicileController extends Controller
{
    // Affiche la page de validation des cours à domicile
    public function index()
    {
        $user = auth()->user();
        $formateur = $user ? $user->formateur : null;
        $demandes = collect();
        if ($formateur) {
            $demandes = DemandeCoursMaison::whereIn('statut', [DemandeCoursMaison::STATUT_VALIDEE, DemandeCoursMaison::STATUT_EN_ATTENTE_FORMATEUR])
                ->where('formateur_id', $formateur->id)
                ->with('user')
                ->orderByDesc('created_at')
                ->get();
        }
        return view('validation_cours_domicile.index', compact('demandes'));
    }

    // Le formateur accepte la demande
    public function accepter($id)
    {
        $demande = DemandeCoursMaison::findOrFail($id);
        if ($demande->formateur_id == auth()->user()->formateur->id) {
            $demande->statut = DemandeCoursMaison::STATUT_ACCEPTEE_FORMATEUR;
            $demande->save();

            // Notifier l'admin
            \App\Models\Notification::createNotification([
                'type' => 'cours_domicile',
                'title' => 'Demande acceptée par le formateur',
                'message' => 'La demande #' . $demande->id . ' a été acceptée par le formateur.',
                'icon' => 'fas fa-home',
                'color' => 'success',
                'user_id' => auth()->id(),
                'admin_id' => null,
                'data' => [
                    'demande_id' => $demande->id,
                    'formateur_id' => $demande->formateur_id,
                    'user_id' => $demande->user_id,
                    'statut' => $demande->statut,
                ],
            ]);

            return back()->with('success', 'Vous avez accepté la demande.');
        }
        return back()->with('error', 'Action non autorisée.');
    }

    // Le formateur refuse la demande
    public function refuser($id)
    {
        $demande = DemandeCoursMaison::findOrFail($id);
        if ($demande->formateur_id == auth()->user()->formateur->id) {
            $demande->statut = DemandeCoursMaison::STATUT_REFUSEE_FORMATEUR;
            $demande->save();

            // Notifier l'admin
            \App\Models\Notification::createNotification([
                'type' => 'cours_domicile',
                'title' => 'Demande refusée par le formateur',
                'message' => 'La demande #' . $demande->id . ' a été refusée par le formateur.',
                'icon' => 'fas fa-home',
                'color' => 'danger',
                'user_id' => auth()->id(),
                'admin_id' => null,
                'data' => [
                    'demande_id' => $demande->id,
                    'formateur_id' => $demande->formateur_id,
                    'user_id' => $demande->user_id,
                    'statut' => $demande->statut,
                ],
            ]);

            return back()->with('success', 'Vous avez refusé la demande.');
        }
        return back()->with('error', 'Action non autorisée.');
    }

    // Valider un cours à domicile (exemple de méthode de traitement)
    public function valider(Request $request)
    {
        // Logique de validation à implémenter ici
        // ...
        return redirect()->route('validation_cours_domicile.index')->with('success', 'Cours validé avec succès !');
    }

    // Affiche l'historique des demandes du formateur
    public function historique()
    {
        $user = auth()->user();
        $formateur = $user ? $user->formateur : null;
        $demandes = collect();
        if ($formateur) {
            $demandes = DemandeCoursMaison::where('formateur_id', $formateur->id)
                ->with('user')
                ->orderByDesc('created_at')
                ->get();
        }
        return view('validation_cours_domicile.historique', compact('demandes'));
    }
}

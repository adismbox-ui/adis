<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;

class ValidationCoursDomicileController extends Controller
{
    // Affiche la page de validation des cours à domicile
    public function index()
    {
        $user = auth()->user();
        $formateur = $user ? $user->formateur : null;
        $demandes = collect();
        if ($formateur) {
            $demandes = \App\Models\DemandeCoursMaison::whereIn('statut', ['validee', 'en_attente_formateur'])
                ->where('formateur_id', $formateur->id)
                ->with('user')
                ->orderByDesc('created_at')
                ->get();
        }
        return response()->json(['demandes' => $demandes], 200);
    }

    // Le formateur accepte la demande
    public function accepter($id)
    {
        $demande = \App\Models\DemandeCoursMaison::findOrFail($id);
        if ($demande->formateur_id == auth()->user()->formateur->id) {
            $demande->statut = 'acceptee_formateur';
            $demande->save();
            return response()->json(['message' => 'Vous avez accepté la demande.'], 200);
        }
        return response()->json(['error' => 'Action non autorisée.'], 401);
    }

    // Le formateur refuse la demande
    public function refuser($id)
    {
        $demande = \App\Models\DemandeCoursMaison::findOrFail($id);
        if ($demande->formateur_id == auth()->user()->formateur->id) {
            $demande->statut = 'refusee_formateur';
            $demande->save();
            return response()->json(['message' => 'Vous avez refusé la demande.'], 200);
        }
        return response()->json(['error' => 'Action non autorisée.'], 401);
    }

    // Valider un cours à domicile (exemple de méthode de traitement)
    public function valider(Request $request)
    {
        // Logique de validation à implémenter ici
        // ...
        return response()->json(['message' => 'Cours validé avec succès !'], 201);
    }

    // Affiche l'historique des demandes du formateur
    public function historique()
    {
        $user = auth()->user();
        $formateur = $user ? $user->formateur : null;
        $demandes = collect();
        if ($formateur) {
            $demandes = \App\Models\DemandeCoursMaison::where('formateur_id', $formateur->id)
                ->with('user')
                ->orderByDesc('created_at')
                ->get();
        }
        return response()->json(['demandes' => $demandes], 200);
    }
}

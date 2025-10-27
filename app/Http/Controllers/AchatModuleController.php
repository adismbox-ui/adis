<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class AchatModuleController extends Controller
{
    // Affiche les modules du niveau de l'apprenant connecté
    public function showAchat()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        $modules = [];
        if ($apprenant && $apprenant->niveau_id) {
            $modules = Module::where('niveau_id', $apprenant->niveau_id)
                ->with(['formateur.utilisateur'])
                ->orderBy('date_debut')
                ->get();
            // Ajoute une propriété is_paye pour chaque module
            foreach ($modules as $m) {
                $m->is_paye = $apprenant->paiements()->where('module_id', $m->id)->where('statut', 'valide')->exists();
            }
        }
        return view('apprenants.achat-modules', compact('modules'));
    }

    // Simule le paiement d'un module (à adapter avec vrai paiement)
    public function payerModule(Request $request, $moduleId)
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        if (!$apprenant) return back()->with('error', 'Utilisateur non trouvé');
        // Enregistre un paiement simulé
        $apprenant->paiements()->create([
            'module_id' => $moduleId,
            'montant' => 0,
            'methode' => 'manuel',
            'reference' => 'test-'.uniqid(),
            'date_paiement' => now(),
            'statut' => 'valide',
        ]);
        return back()->with('success', 'Paiement enregistré !');
    }
}

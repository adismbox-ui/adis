<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formateur;
use App\Models\Module;
use App\Models\Inscription;
use App\Models\Apprenant;

class ApprenantsFormateurController extends Controller
{
    // Affiche la liste des apprenants inscrits aux modules du formateur connecté
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->formateur) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $formateur = $user->formateur;
        $modules = $formateur->modules()->with(['inscriptions.apprenant.utilisateur'])->get();
        return response()->json(['modules' => $modules], 200);
    }

    // Affiche le détail d'un apprenant (accessible depuis la liste)
    public function show($apprenant_id)
    {
        $apprenant = Apprenant::with('utilisateur')->find($apprenant_id);
        if (!$apprenant) {
            return response()->json(['error' => 'Apprenant non trouvé'], 404);
        }
        return response()->json(['apprenant' => $apprenant], 200);
    }
}

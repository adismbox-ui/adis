<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemandeCoursMaison;
use App\Models\Formateur;

class ValiderCoursApiController extends Controller
{
    // Page liste des demandes à valider
    public function index()
    {
        $demandes = DemandeCoursMaison::with('user')->latest()->get();
        $niveauxModules = [];
        foreach ($demandes as $demande) {
            $module = \App\Models\Module::where('titre', $demande->module)->with('niveau')->first();
            $niveauxModules[$demande->id] = $module && $module->niveau ? $module->niveau->nom : '-';
        }
        return response()->json([
            'demandes' => $demandes,
            'niveauxModules' => $niveauxModules
        ], 200);
    }

    // Voir une demande et formulaire d'assignation
    public function show($id)
    {
        $demande = DemandeCoursMaison::with('user')->findOrFail($id);
        $module = \App\Models\Module::where('titre', $demande->module)->first();
        $formateurs = collect();
        if ($module) {
            $formateurs = \App\Models\Formateur::with('utilisateur')
                ->where('id', $module->formateur_id)
                ->get();
        }
        return response()->json([
            'demande' => $demande,
            'formateurs' => $formateurs
        ], 200);
    }

    // Valider une demande et assigner un formateur
    public function valider(Request $request, $id)
    {
        $demande = DemandeCoursMaison::findOrFail($id);
        $demande->statut = 'validee';
        $demande->formateur_id = $request->formateur_id;
        $demande->save();
        return response()->json(['message' => 'Demande validée et formateur assigné.'], 201);
    }

    // Refuser une demande
    public function refuser($id)
    {
        $demande = DemandeCoursMaison::findOrFail($id);
        $demande->statut = 'refuse';
        $demande->save();
        return response()->json(['message' => 'Demande refusée.'], 200);
    }
}

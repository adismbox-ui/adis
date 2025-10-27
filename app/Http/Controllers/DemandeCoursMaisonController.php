<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\DemandeCoursMaison;

class DemandeCoursMaisonController extends Controller
{
    // Affiche la liste des demandes de l'utilisateur connecté
    public function index()
    {
        $user = Auth::user();
        $demandes = DemandeCoursMaison::where('user_id', $user->id)
            ->with(['formateur.utilisateur'])
            ->latest()
            ->get();
        return view('demandes-cours-maison.index', compact('demandes'));
    }

    // Enregistre une nouvelle demande
    public function store(Request $request)
    {
        $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'nombre_enfants' => 'required|integer|min:1|max:20',
            'ville' => 'required|string|max:100',
            'commune' => 'required|string|max:100',
            'quartier' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'message' => 'required|string|min:10|max:2000',
        ]);
        $user = Auth::user();
        $niveau = \App\Models\Niveau::find($request->niveau_id);
        DemandeCoursMaison::create([
            'user_id' => $user->id,
            'niveau_id' => $request->niveau_id,
            // Pour compatibilité avec la colonne non nulle 'module'
            'module' => $niveau ? $niveau->nom : '',
            'nombre_enfants' => $request->nombre_enfants,
            'ville' => $request->ville,
            'commune' => $request->commune,
            'quartier' => $request->quartier,
            'numero' => $request->numero,
            'message' => $request->message,
        ]);
        return back()->with('success', 'Votre demande de cours à domicile a bien été envoyée à l\'administrateur.');
    }

    // Affiche le formulaire d'édition d'une demande
    public function edit($id)
    {
        $user = Auth::user();
        $demande = DemandeCoursMaison::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('demandes-cours-maison.edit', compact('demande','niveaux'));
    }

    // Met à jour une demande
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $demande = DemandeCoursMaison::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
            'nombre_enfants' => 'required|integer|min:1|max:20',
            'ville' => 'required|string|max:100',
            'commune' => 'required|string|max:100',
            'quartier' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'message' => 'required|string|min:10|max:2000',
        ]);
        $niveau = \App\Models\Niveau::find($request->niveau_id);
        $demande->update([
            'niveau_id' => $request->niveau_id,
            // Garder la colonne 'module' cohérente
            'module' => $niveau ? $niveau->nom : $demande->module,
            'nombre_enfants' => $request->nombre_enfants,
            'ville' => $request->ville,
            'commune' => $request->commune,
            'quartier' => $request->quartier,
            'numero' => $request->numero,
            'message' => $request->message,
        ]);
        return redirect()->route('demandes.cours.maison.index')->with('success', 'Demande modifiée avec succès.');
    }

    // Supprime une demande de l'utilisateur connecté
    public function destroy($id)
    {
        $user = Auth::user();
        $demande = DemandeCoursMaison::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $demande->delete();
        return redirect()->route('demandes.cours.maison.index')->with('success', 'Demande supprimée avec succès.');
    }
}

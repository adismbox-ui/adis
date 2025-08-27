<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class AssistantVacancesController extends Controller
{
    public function index()
    {
        $vacances = \App\Models\Vacance::orderBy('created_at', 'desc')->get();
        return view('assistants.vacances.index', compact('vacances'));
    }

    public function create()
    {
        return view('assistants.vacances.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);
        
        $vacance = \App\Models\Vacance::create([
            'nom' => $request->nom,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
        ]);
        
        // Notifier l'admin avec le nouveau système
        NotificationService::notifyAssistantAction(
            'vacance',
            'Nouvelle période de vacances créée',
            "Une nouvelle période de vacances a été créée : {$vacance->nom} (du {$vacance->date_debut} au {$vacance->date_fin})",
            [
                'vacance_nom' => $vacance->nom,
                'vacance_debut' => $vacance->date_debut,
                'vacance_fin' => $vacance->date_fin,
                'vacance_id' => $vacance->id
            ]
        );
        
        return redirect()->route('assistant.vacances')->with('success', 'Période de vacances créée avec succès ! L\'admin a été notifié.');
    }

    public function edit($id)
    {
        $vacance = \App\Models\Vacance::findOrFail($id);
        return view('assistants.vacances.edit', compact('vacance'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $vacance = \App\Models\Vacance::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'actif' => 'required|boolean',
        ]);
        $vacance->update($request->only(['nom', 'date_debut', 'date_fin', 'actif']));
        return redirect()->route('assistant.vacances')->with('success', 'Période de vacances modifiée avec succès.');
    }

    public function destroy($id)
    {
        $vacance = \App\Models\Vacance::findOrFail($id);
        $vacance->delete();
        return redirect()->route('assistant.vacances')->with('success', 'Période de vacances supprimée avec succès.');
    }
}
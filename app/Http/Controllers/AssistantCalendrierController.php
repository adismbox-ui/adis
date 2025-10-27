<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantCalendrierController extends Controller
{
    public function index()
    {
        $sessions = \App\Models\SessionFormation::with('niveau')->orderBy('date_debut')->get();
        $vacances = \App\Models\Vacance::orderBy('date_debut')->get();
        return view('assistants.calendrier.index', compact('sessions', 'vacances'));
    }

    public function create()
    {
        return view('assistants.calendrier.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
        ]);
        // Ici, on simule la création d'un événement calendrier
        $admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'calendrier',
                'message' => 'Un nouvel événement calendrier a été créé par l\'assistant '.(auth()->user()->prenom ?? '').' '.(auth()->user()->nom ?? '').' : '.$request->titre,
            ]);
        }
        return redirect()->route('assistant.calendrier')->with('success', 'Événement créé et notification envoyée à l\'admin.');
    }

    public function edit($id)
    {
        $session = \App\Models\SessionFormation::findOrFail($id);
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('assistants.calendrier.edit', compact('session', 'niveaux'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $session = \App\Models\SessionFormation::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i',
            'places_max' => 'nullable|integer|min:1',
        ]);
        $session->update($request->only(['nom', 'niveau_id', 'date_debut', 'date_fin', 'heure_debut', 'heure_fin', 'places_max']));
        return redirect()->route('assistant.calendrier')->with('success', 'Session modifiée avec succès.');
    }
}
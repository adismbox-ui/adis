<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class AssistantSessionsController extends Controller
{
    public function index()
    {
        $sessions = \App\Models\SessionFormation::orderBy('created_at', 'desc')->get();
        return view('assistants.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $niveaux = \App\Models\Niveau::where('actif', true)->orderBy('ordre')->get();
        $vacances = \App\Models\Vacance::actives()->orderBy('date_debut')->get();
        $modules = \App\Models\Module::orderBy('titre')->get();
        return view('assistants.sessions.create', compact('niveaux', 'vacances', 'modules'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'jour_semaine' => 'nullable|string',
            'duree_seance_minutes' => 'required|integer|min:15|max:480',
            'prix' => 'nullable|numeric|min:0',
            'places_max' => 'nullable|integer|min:1',
            'actif' => 'boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'exists:modules,id',
        ]);

        // Vérifier les conflits avec les vacances
        $vacancesConflit = \App\Models\Vacance::actives()
            ->pourPeriode($request->date_debut, $request->date_fin)
            ->get();

        if ($vacancesConflit->count() > 0) {
            return back()->withInput()
                ->withErrors(['date_debut' => 'La période sélectionnée chevauche des vacances : ' . $vacancesConflit->pluck('nom')->implode(', ')]);
        }

        $session = \App\Models\SessionFormation::create($request->all());
        if ($request->has('modules')) {
            $session->modules()->sync($request->input('modules'));
        }

        // Notifier l'admin avec le nouveau système
        NotificationService::notifyAssistantAction(
            'session',
            'Nouvelle session créée',
            "Une nouvelle session de formation a été créée : {$session->nom}",
            [
                'session_name' => $session->nom,
                'session_id' => $session->id,
                'date_debut' => $session->date_debut
            ]
        );

        return redirect()->route('assistant.sessions')->with('success', 'Session créée avec succès ! L\'admin a été notifié.');
    }

    public function edit($id)
    {
        $session = \App\Models\SessionFormation::findOrFail($id);
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('assistants.sessions.edit', compact('session', 'niveaux'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $session = \App\Models\SessionFormation::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'places_max' => 'nullable|integer|min:1',
        ]);
        $session->update($request->only(['nom', 'description', 'niveau_id', 'date_debut', 'date_fin', 'places_max']));
        return redirect()->route('assistant.sessions')->with('success', 'Session modifiée avec succès.');
    }

    public function destroy($id)
    {
        $session = \App\Models\SessionFormation::findOrFail($id);
        // Optionnel : vérifier qu'il n'y a pas d'inscriptions avant suppression
        if ($session->inscriptions()->count() > 0) {
            return redirect()->route('assistant.sessions')->with('error', 'Impossible de supprimer cette session car elle a des inscriptions.');
        }
        $session->delete();
        return redirect()->route('assistant.sessions')->with('success', 'Session supprimée avec succès.');
    }

    public function show($id)
    {
        $session = \App\Models\SessionFormation::with(['niveau', 'module', 'formateur.utilisateur', 'inscriptions.apprenant.utilisateur'])->findOrFail($id);
        return view('assistants.sessions.show', compact('session'));
    }
} 
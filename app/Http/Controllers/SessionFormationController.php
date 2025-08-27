<?php

namespace App\Http\Controllers;

use App\Models\SessionFormation;
use App\Models\Niveau;
use App\Models\Vacance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SessionFormationController extends Controller
{
    public function index()
    {
        $sessions = SessionFormation::with('niveau')->orderBy('date_debut')->get();
        return view('admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $niveaux = Niveau::where('actif', true)->orderBy('ordre')->get();
        $vacances = Vacance::actives()->orderBy('date_debut')->get();
        $modules = \App\Models\Module::orderBy('titre')->get();
        return view('admin.sessions.create', compact('niveaux', 'vacances', 'modules'));
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
            'actif' => 'boolean'
        ]);

        // Vérifier les conflits avec les vacances
        $vacancesConflit = Vacance::actives()
            ->pourPeriode($request->date_debut, $request->date_fin)
            ->get();

        if ($vacancesConflit->count() > 0) {
            return back()->withInput()
                ->withErrors(['date_debut' => 'La période sélectionnée chevauche des vacances : ' . $vacancesConflit->pluck('nom')->implode(', ')]);
        }

        $session = SessionFormation::create($request->all());
        if ($request->has('modules')) {
            $session->modules()->sync($request->input('modules'));
        }
        // Notifier l'admin de la création d'une nouvelle session de formation
        \App\Models\Notification::notifyAdminOfAssistantAction(
            'session',
            'Nouvelle session créée',
            "Une nouvelle session de formation a été créée : {$session->nom}",
            auth()->id(),
            [
                'session_name' => $session->nom,
                'assistant_name' => auth()->user()->prenom . ' ' . auth()->user()->nom,
                'session_id' => $session->id
            ]
        );

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session créée avec succès.');
    }

    public function edit(SessionFormation $session)
    {
        $niveaux = Niveau::where('actif', true)->orderBy('ordre')->get();
        $vacances = Vacance::actives()->orderBy('date_debut')->get();
        $modules = \App\Models\Module::orderBy('titre')->get();
        return view('admin.sessions.edit', compact('session', 'niveaux', 'vacances', 'modules'));
    }

    public function update(Request $request, SessionFormation $session)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',

            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'jour_semaine' => 'nullable|string',
            'duree_seance_minutes' => 'required|integer|min:15|max:480',

            'prix' => 'nullable|numeric|min:0',
            'places_max' => 'nullable|integer|min:1',
            'actif' => 'boolean'
        ]);

        // Vérifier les conflits avec les vacances (sauf pour la session elle-même)
        $vacancesConflit = Vacance::actives()
            ->pourPeriode($request->date_debut, $request->date_fin)
            ->get();

        if ($vacancesConflit->count() > 0) {
            return back()->withInput()
                ->withErrors(['date_debut' => 'La période sélectionnée chevauche des vacances : ' . $vacancesConflit->pluck('nom')->implode(', ')]);
        }

        $session->update($request->all());
        if ($request->has('modules')) {
            $session->modules()->sync($request->input('modules'));
        } else {
            $session->modules()->detach();
        }

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session mise à jour avec succès.');
    }

    public function destroy(SessionFormation $session)
    {
        // Vérifier s'il y a des inscriptions
        if ($session->inscriptions()->count() > 0) {
            return redirect()->route('admin.sessions.index')
                ->with('error', 'Impossible de supprimer cette session car elle a des inscriptions.');
        }

        $session->delete();

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session supprimée avec succès.');
    }

    public function calendrier()
    {
        $sessions = SessionFormation::with('niveau')
            ->where('actif', true)
            ->orderBy('date_debut')
            ->get();

        $vacances = Vacance::actives()->orderBy('date_debut')->get();

        return view('admin.sessions.calendrier', compact('sessions', 'vacances'));
    }

    public function show(SessionFormation $session)
    {
        return view('admin.sessions.show', compact('session'));
    }
} 
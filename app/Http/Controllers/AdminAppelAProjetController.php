<?php

namespace App\Http\Controllers;

use App\Models\AppelAProjet;
use Illuminate\Http\Request;

class AdminAppelAProjetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appelsEnCours = AppelAProjet::enCours()->orderBy('date_limite_soumission', 'asc')->get();
        $appelsClotures = AppelAProjet::clotures()->orderBy('date_cloture', 'desc')->get();
        
        return view('admin.appels-a-projets.index', compact('appelsEnCours', 'appelsClotures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.appels-a-projets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'reference' => 'required|string|max:50|unique:appel_a_projets',
            'intitule' => 'required|string|max:255',
            'domaine' => 'required|string|max:100',
            'date_limite_soumission' => 'required|date|after:today',
            'etat' => 'required|in:ouvert,cloture',
            'details_offre' => 'nullable|string',
            'montant_estimatif' => 'nullable|numeric|min:0',
            'beneficiaires' => 'nullable|string|max:255',
            'date_cloture' => 'nullable|date|after:date_limite_soumission',
        ]);

        AppelAProjet::create($data);

        return redirect()->route('admin.appels-a-projets.index')
            ->with('success', 'Appel à projet créé avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(AppelAProjet $appelAProjet)
    {
        return view('admin.appels-a-projets.show', compact('appelAProjet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppelAProjet $appelAProjet)
    {
        return view('admin.appels-a-projets.edit', compact('appelAProjet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppelAProjet $appelAProjet)
    {
        $data = $request->validate([
            'reference' => 'required|string|max:50|unique:appel_a_projets,reference,' . $appelAProjet->id,
            'intitule' => 'required|string|max:255',
            'domaine' => 'required|string|max:100',
            'date_limite_soumission' => 'required|date',
            'etat' => 'required|in:ouvert,cloture',
            'details_offre' => 'nullable|string',
            'montant_estimatif' => 'nullable|numeric|min:0',
            'beneficiaires' => 'nullable|string|max:255',
            'date_cloture' => 'nullable|date',
        ]);

        $appelAProjet->update($data);

        return redirect()->route('admin.appels-a-projets.index')
            ->with('success', 'Appel à projet mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppelAProjet $appelAProjet)
    {
        $appelAProjet->delete();

        return redirect()->route('admin.appels-a-projets.index')
            ->with('success', 'Appel à projet supprimé avec succès !');
    }

    /**
     * Clôturer un appel à projet
     */
    public function cloturer(Request $request, AppelAProjet $appelAProjet)
    {
        $request->validate([
            'partenaire_retenu' => 'required|string|max:255',
            'montant_estimatif' => 'required|numeric|min:0',
            'beneficiaires' => 'required|string|max:255',
            'date_cloture' => 'required|date',
        ]);

        $appelAProjet->update([
            'etat' => 'cloture',
            'partenaire_retenu' => $request->partenaire_retenu,
            'montant_estimatif' => $request->montant_estimatif,
            'beneficiaires' => $request->beneficiaires,
            'date_cloture' => $request->date_cloture,
        ]);

        return redirect()->route('admin.appels-a-projets.index')
            ->with('success', 'Appel à projet clôturé avec succès !');
    }
} 
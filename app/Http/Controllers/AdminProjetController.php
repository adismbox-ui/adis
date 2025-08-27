<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projet;

class AdminProjetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projets = Projet::orderBy('created_at', 'desc')->get();
        return view('admin.projets.index', compact('projets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.projets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'intitule' => 'required|string|max:255',
            'beneficiaires' => 'required|string',
            'objectif' => 'required|string',
            'debut' => 'required|date',
            'fin_prevue' => 'required|date|after:debut',
            'taux_avancement' => 'required|integer|min:0|max:100',
            'responsable' => 'required|string|max:255',
            'statut' => 'required|in:en_cours,realise,a_financer,en_attente',
            'montant_total' => 'nullable|numeric|min:0',
            'montant_collecte' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Calculer le reste à financer
        if (isset($data['montant_total']) && isset($data['montant_collecte'])) {
            $data['reste_a_financer'] = $data['montant_total'] - $data['montant_collecte'];
        }

        Projet::create($data);

        return redirect()->route('admin.projets.index')
            ->with('success', 'Projet créé avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Projet $projet)
    {
        return view('admin.projets.show', compact('projet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Projet $projet)
    {
        return view('admin.projets.edit', compact('projet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Projet $projet)
    {
        $data = $request->validate([
            'intitule' => 'required|string|max:255',
            'beneficiaires' => 'required|string',
            'objectif' => 'required|string',
            'debut' => 'required|date',
            'fin_prevue' => 'required|date|after:debut',
            'taux_avancement' => 'required|integer|min:0|max:100',
            'responsable' => 'required|string|max:255',
            'statut' => 'required|in:en_cours,realise,a_financer,en_attente',
            'montant_total' => 'nullable|numeric|min:0',
            'montant_collecte' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Calculer le reste à financer
        if (isset($data['montant_total']) && isset($data['montant_collecte'])) {
            $data['reste_a_financer'] = $data['montant_total'] - $data['montant_collecte'];
        }

        $projet->update($data);

        return redirect()->route('admin.projets.index')
            ->with('success', 'Projet mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Projet $projet)
    {
        $projet->delete();

        return redirect()->route('admin.projets.index')
            ->with('success', 'Projet supprimé avec succès !');
    }
}

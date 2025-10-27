<?php

namespace App\Http\Controllers;

use App\Models\Vacance;
use Illuminate\Http\Request;

class VacanceController extends Controller
{
    public function index()
    {
        $vacances = Vacance::orderBy('date_debut')->get();
        return view('admin.vacances.index', compact('vacances'));
    }

    public function create()
    {
        return view('admin.vacances.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'actif' => 'boolean'
        ]);

        Vacance::create($request->all());

        return redirect()->route('admin.vacances.index')
            ->with('success', 'Vacance créée avec succès.');
    }

    public function edit(Vacance $vacance)
    {
        return view('admin.vacances.edit', compact('vacance'));
    }

    public function update(Request $request, Vacance $vacance)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'actif' => 'boolean'
        ]);

        $vacance->update($request->all());

        return redirect()->route('admin.vacances.index')
            ->with('success', 'Vacance mise à jour avec succès.');
    }

    public function destroy(Vacance $vacance)
    {
        $vacance->delete();

        return redirect()->route('admin.vacances.index')
            ->with('success', 'Vacance supprimée avec succès.');
    }
} 
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vacance;
use Illuminate\Http\Request;

class VacanceApiController extends Controller
{
    public function index()
    {
        $vacances = Vacance::orderBy('date_debut')->get();
        return response()->json([
            'success' => true,
            'vacances' => $vacances,
            'total' => $vacances->count()
        ], 200);
    }

    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Endpoint pour création de vacance'
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'actif' => 'boolean'
        ]);

        $vacance = Vacance::create($data);

        return response()->json([
            'success' => true,
            'vacance' => $vacance,
            'message' => 'Vacance créée avec succès.'
        ], 201);
    }

    public function show(Vacance $vacance)
    {
        return response()->json([
            'success' => true,
            'vacance' => $vacance
        ], 200);
    }

    public function edit(Vacance $vacance)
    {
        return response()->json([
            'success' => true,
            'vacance' => $vacance
        ], 200);
    }

    public function update(Request $request, Vacance $vacance)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'actif' => 'boolean'
        ]);

        $vacance->update($data);

        return response()->json([
            'success' => true,
            'vacance' => $vacance,
            'message' => 'Vacance mise à jour avec succès.'
        ], 200);
    }

    public function destroy(Vacance $vacance)
    {
        $vacance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vacance supprimée avec succès.'
        ], 200);
    }
} 
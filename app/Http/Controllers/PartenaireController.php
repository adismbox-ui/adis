<?php

namespace App\Http\Controllers;

use App\Models\Partenaire;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partenaires = \App\Models\Partenaire::all();
        return view('partenaires.index', compact('partenaires'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('partenaires.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:50',
            'site_web' => 'nullable|string|max:255',
        ]);

        \App\Models\Partenaire::create($data);

        return redirect()->route('partenaires.index')
            ->with('success', "Le partenaire a été enregistré avec succès.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Partenaire $partenaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partenaire $partenaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partenaire $partenaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partenaire $partenaire)
    {
        //
    }
}

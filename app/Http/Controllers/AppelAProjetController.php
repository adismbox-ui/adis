<?php

namespace App\Http\Controllers;

use App\Models\AppelAProjet;
use Illuminate\Http\Request;

class AppelAProjetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appelsEnCours = AppelAProjet::enCours()->orderBy('date_limite_soumission', 'asc')->get();
        $appelsClotures = AppelAProjet::clotures()->orderBy('date_cloture', 'desc')->get();
        
        return view('projets.appel-a-projets', compact('appelsEnCours', 'appelsClotures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AppelAProjet $appelAProjet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppelAProjet $appelAProjet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AppelAProjet $appelAProjet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppelAProjet $appelAProjet)
    {
        //
    }
}

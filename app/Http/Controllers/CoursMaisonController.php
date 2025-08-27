<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoursMaisonController extends Controller
{
    /**
     * Affiche la page Cours Maison
     */
    public function index()
    {
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('cours-maison', compact('niveaux'));
    }
}

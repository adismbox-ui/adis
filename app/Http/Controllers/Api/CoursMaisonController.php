<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoursMaisonController extends Controller
{
    /**
     * Affiche la page Cours Maison
     */
    public function index()
    {
        $modules = \App\Models\Module::with('niveau')->orderBy('titre')->get();
        return response()->json(['modules' => $modules], 200);
    }
}

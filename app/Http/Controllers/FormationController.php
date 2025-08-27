<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormationController extends Controller
{
    // Affiche la page principale Formation
    public function index()
    {
        return view('formation');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPartenaireController extends Controller
{
    public function index()
    {
        $partenaires = \App\Models\Partenaire::orderByDesc('created_at')->get();
        return view('admin/partenaires/index', compact('partenaires'));
    }

    public function create()
    {
        return view('admin/partenaires/create');
    }

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

        return redirect()->route('admin.partenaires.index')->with('success', 'Entreprise partenaire ajoutée.');
    }
}


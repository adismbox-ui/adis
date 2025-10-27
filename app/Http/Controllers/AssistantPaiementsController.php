<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantPaiementsController extends Controller
{
    public function index()
    {
        $paiements = \App\Models\Paiement::orderBy('created_at', 'desc')->get();
        return view('assistants.paiements.index', compact('paiements'));
    }

    public function create()
    {
        return view('assistants.paiements.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
        ]);
        $paiement = \App\Models\Paiement::create([
            'montant' => $request->montant,
        ]);
        $admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'paiement',
                'message' => 'Un nouveau paiement a été créé par l\'assistant '.(auth()->user()->prenom ?? '').' '.(auth()->user()->nom ?? '').' : '.$paiement->montant,
            ]);
        }
        return redirect()->route('assistant.paiements')->with('success', 'Paiement créé et notification envoyée à l\'admin.');
    }
    public function show($id)
    {
        $paiement = \App\Models\Paiement::with(['apprenant.utilisateur', 'module'])->findOrFail($id);
        return view('assistants.paiements.show', compact('paiement'));
    }

    public function edit($id)
    {
        $paiement = \App\Models\Paiement::with(['apprenant.utilisateur', 'module'])->findOrFail($id);
        return view('assistants.paiements.edit', compact('paiement'));
    }
} 
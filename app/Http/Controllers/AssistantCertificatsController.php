<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class AssistantCertificatsController extends Controller
{
    public function index()
    {
        $certificats = \App\Models\Certificat::orderBy('created_at', 'desc')->get();
        return view('assistants.certificats.index', compact('certificats'));
    }

    public function create()
    {
        return view('assistants.certificats.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'apprenant_id' => 'nullable|exists:utilisateurs,id',
        ]);
        
        $certificat = \App\Models\Certificat::create([
            'titre' => $request->titre,
            'type' => $request->type,
            'apprenant_id' => $request->apprenant_id,
        ]);
        
        // Notifier l'admin avec le nouveau système
        NotificationService::notifyAssistantAction(
            'certificat',
            'Nouveau certificat créé',
            "Un nouveau certificat a été généré : {$certificat->titre}",
            [
                'certificat_titre' => $certificat->titre,
                'certificat_type' => $certificat->type,
                'certificat_id' => $certificat->id
            ]
        );
        
        return redirect()->route('assistant.certificats')->with('success', 'Certificat créé avec succès ! L\'admin a été notifié.');
    }
    public function show($id)
    {
        $certificat = \App\Models\Certificat::with(['apprenant.utilisateur'])->findOrFail($id);
        return view('assistants.certificats.show', compact('certificat'));
    }

    public function edit($id)
    {
        $certificat = \App\Models\Certificat::with(['apprenant.utilisateur'])->findOrFail($id);
        return view('assistants.certificats.edit', compact('certificat'));
    }
} 
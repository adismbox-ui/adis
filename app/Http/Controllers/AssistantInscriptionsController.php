<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantInscriptionsController extends Controller
{
    public function show($id)
    {
        $inscription = \App\Models\Inscription::with(['apprenant.utilisateur', 'module', 'session'])->findOrFail($id);
        return view('assistants.inscriptions.show', compact('inscription'));
    }

    public function edit($id)
    {
        $inscription = \App\Models\Inscription::with(['apprenant.utilisateur', 'module', 'session'])->findOrFail($id);
        return view('assistants.inscriptions.edit', compact('inscription'));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formateur;
use Illuminate\Support\Facades\Auth;

class FormateurProfileController extends Controller
{
    // Affiche le profil du formateur connecté
    public function show()
    {
        $user = Auth::user();
        $formateur = $user ? $user->formateur()->with('utilisateur')->first() : null;
        if (!$formateur) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        return response()->json(['formateur' => $formateur], 200);
    }

    // Affiche le formulaire d'édition du profil
    public function edit()
    {
        $user = Auth::user();
        $formateur = $user ? $user->formateur()->with('utilisateur')->first() : null;
        if (!$formateur) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        return response()->json(['formateur' => $formateur], 200);
    }

    // Met à jour le profil du formateur
    public function update(Request $request)
    {
        $user = Auth::user();
        $formateur = $user ? $user->formateur()->with('utilisateur')->first() : null;
        if (!$formateur) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
        ]);
        $formateur->utilisateur->update($data);
        if ($request->has('date_naissance')) {
            $formateur->date_naissance = $request->input('date_naissance');
        }
        if ($request->has('adresse')) {
            $formateur->adresse = $request->input('adresse');
        }
        $formateur->save();
        return response()->json(['formateur' => $formateur, 'message' => 'Profil mis à jour avec succès !'], 200);
    }
}

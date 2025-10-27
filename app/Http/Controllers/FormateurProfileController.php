<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formateur;
use Illuminate\Support\Facades\Auth;

class FormateurProfileController extends Controller
{
    // Affiche le profil du formateur connecté
    public function show()
    {
        $user = Auth::user();
        $formateur = $user ? $user->formateur()->with(['utilisateur', 'niveaux'])->first() : null;
        if (!$formateur) {
            return redirect()->route('login');
        }
        return view('formateurs.profil', compact('formateur'));
    }

    // Affiche le formulaire d'édition du profil
    public function edit()
    {
        $user = Auth::user();
        $formateur = $user ? $user->formateur()->with('utilisateur')->first() : null;
        if (!$formateur) {
            return redirect()->route('login');
        }
        return view('formateurs.edit_profil', compact('formateur'));
    }

    // Met à jour le profil du formateur
    public function update(Request $request)
    {
        $user = Auth::user();
        $formateur = $user ? $user->formateur()->with('utilisateur')->first() : null;
        if (!$formateur) {
            return redirect()->route('login');
        }
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
        ]);
        // Mise à jour de la table utilisateurs liée
        $formateur->utilisateur->update($data);
        // Mise à jour des infos spécifiques formateur si besoin
        if ($request->has('date_naissance')) {
            $formateur->date_naissance = $request->input('date_naissance');
        }
        if ($request->has('adresse')) {
            $formateur->adresse = $request->input('adresse');
        }
        $formateur->save();
        return redirect()->route('formateurs.profil')->with('success', 'Profil mis à jour avec succès !');
    }
}

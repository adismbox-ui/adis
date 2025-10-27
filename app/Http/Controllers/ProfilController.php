<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    /**
     * Affiche la page de profil de l'apprenant.
     */
    public function index()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        return view('apprenants.profil', compact('user', 'apprenant'));
    }

    // Affiche la page de test du profil avec formulaire d'édition
    public function editTest()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        return view('prifil_test', compact('user', 'apprenant'));
    }

    // Met à jour les infos du profil test
    public function updateTest(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email',
            'telephone' => 'nullable|string|max:20',
        ]);
        $user->prenom = $data['prenom'];
        $user->nom = $data['nom'];
        $user->email = $data['email'];
        $user->telephone = $data['telephone'];
        $user->save();
        return redirect()->route('apprenants.prifil_test')->with('success', 'Profil mis à jour avec succès.');
    }

    // Met à jour le mot de passe de l'utilisateur depuis la page de test du profil
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        // Vérifier le mot de passe actuel
        if (!\Hash::check($request->current_password, $user->password)) {
            return redirect()->route('apprenants.prifil_test')->with('password_error', 'Le mot de passe actuel est incorrect.');
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return redirect()->route('apprenants.prifil_test')->with('password_success', 'Mot de passe modifié avec succès.');
    }
}

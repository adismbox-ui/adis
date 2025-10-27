<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParametreController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        return view('apprenants.parametres', compact('user', 'apprenant'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        $data = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email',
            'telephone' => 'nullable|string',
            'password' => 'nullable|confirmed|min:6',
        ]);
        $user->nom = $data['nom'];
        $user->prenom = $data['prenom'];
        $user->email = $data['email'];
        $user->telephone = $data['telephone'];
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();
        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }
}

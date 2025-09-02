<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfilApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        return response()->json(['user' => $user, 'apprenant' => $apprenant], 200);
    }

    public function editTest()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        return response()->json(['user' => $user, 'apprenant' => $apprenant], 200);
    }

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
        return response()->json(['user' => $user, 'message' => 'Profil mis à jour avec succès.'], 200);
    }

    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        if (!\Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Le mot de passe actuel est incorrect.'], 422);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json(['message' => 'Mot de passe modifié avec succès.'], 200);
    }
}

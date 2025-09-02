<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParametreController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apprenant = $user ? $user->apprenant : null;
        return response()->json(['user' => $user, 'apprenant' => $apprenant], 200);
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
        return response()->json(['user' => $user, 'message' => 'Paramètres mis à jour avec succès.'], 200);
    }
}

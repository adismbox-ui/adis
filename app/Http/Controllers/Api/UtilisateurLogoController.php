<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Utilisateur;

class UtilisateurLogoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
        }
        $logoPath = $request->file('logo')->store('sidebar_logos', 'public');
        $user->sidebar_logo = $logoPath;
        $user->save();
        return response()->json([
            'message' => 'Logo de sidebar mis à jour avec succès!',
            'logo' => $logoPath
        ], 201);
    }
}

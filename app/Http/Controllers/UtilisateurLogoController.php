<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UtilisateurLogoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'Utilisateur non authentifié.');
        }
        $logoPath = $request->file('logo')->store('sidebar_logos', 'public');
        $user->sidebar_logo = $logoPath;
        $user->save();
        return back()->with('success', 'Logo de sidebar mis à jour avec succès!');
    }
}

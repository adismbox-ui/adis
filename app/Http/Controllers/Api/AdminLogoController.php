<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class AdminLogoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $logoPath = $request->file('logo')->store('admin_logos', 'public');
        $setting = Setting::firstOrCreate([]);
        $setting->admin_logo = $logoPath;
        if ($setting->save()) {
            return response()->json([
                'message' => 'Logo mis à jour avec succès!',
                'logo' => $logoPath
            ], 201);
        } else {
            return response()->json(['error' => 'Erreur lors de la sauvegarde du logo.'], 500);
        }
    }
}

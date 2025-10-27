<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class AdminLogoController extends Controller
{
    public function edit()
    {
        $setting = \App\Models\Setting::first();
        return view('admin.logo.edit', ['adminLogo' => $setting ? $setting->admin_logo : null]);
    }
    public function upload(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $logoPath = $request->file('logo')->store('admin_logos', 'public');
        $setting = Setting::firstOrCreate([]);
        $setting->admin_logo = $logoPath;
        $setting->save();
        return back()->with('success', 'Logo mis à jour avec succès!');
    }
}

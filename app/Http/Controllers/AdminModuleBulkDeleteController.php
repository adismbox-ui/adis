<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;

class AdminModuleBulkDeleteController extends Controller
{
    // Affiche la page de sélection/suppression multiple
    public function showBulkDelete(Request $request)
    {
        $modules = Module::with('niveau', 'formateur.utilisateur')->orderBy('id', 'desc')->get();
        return view('admin.modules.bulk_delete', compact('modules'));
    }

    // Supprime les modules sélectionnés
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Module::whereIn('id', $ids)->delete();
            return redirect()->route('admin.modules.bulkDelete')->with('success', 'Modules supprimés avec succès.');
        }
        return redirect()->route('admin.modules.bulkDelete')->with('error', 'Aucun module sélectionné.');
    }
} 
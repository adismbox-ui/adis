<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Niveau;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $niveaux = collect();
        if ($user && $user->formateur) {
            $niveaux = $user->formateur->niveaux()->with(['modules', 'sessionFormation'])->get();
        }
        return view('modules.index', compact('niveaux'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formateurs = Formateur::with('utilisateur')->get(); // Afficher tous les formateurs
        $niveaux = Niveau::where('actif', true)->orderBy('ordre')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('modules.create', compact('formateurs', 'niveaux', 'sessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'discipline' => 'nullable|string|max:255',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'niveaux_ids' => 'nullable|array',
            'niveaux_ids.*' => 'exists:niveaux,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            // formateur_id devient optionnel: si absent, on prendra celui du niveau
            'formateur_id' => 'nullable|exists:formateurs,id',
            'lien' => 'nullable|string|max:255',
            'support' => 'nullable|file|mimes:pdf|max:10240',
            'prix' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        if ($request->hasFile('support')) {
            $data['support'] = $request->file('support')->store('supports', 'public');
        }
        // Si plusieurs niveaux sont sélectionnés, créer un module pour chaque niveau
        $niveaux = $request->input('niveaux_ids', []);
        if (is_array($niveaux) && count($niveaux) > 0) {
            foreach ($niveaux as $niveauId) {
                $moduleData = $data;
                $moduleData['niveau_id'] = $niveauId;
                unset($moduleData['niveaux_ids']);
                if (empty($moduleData['formateur_id'])) {
                    $niveau = Niveau::find($niveauId);
                    if ($niveau && $niveau->formateur_id) {
                        $moduleData['formateur_id'] = $niveau->formateur_id;
                    }
                }
                Module::create($moduleData);
            }
        } else {
            // Cas classique : un seul niveau
            if (isset($data['niveau_id']) && $data['niveau_id']) {
                if (empty($data['formateur_id'])) {
                    $niveau = Niveau::find($data['niveau_id']);
                    if ($niveau && $niveau->formateur_id) {
                        $data['formateur_id'] = $niveau->formateur_id;
                    }
                }
                Module::create($data);
            } else {
                return back()->withErrors(['niveau_id' => 'Veuillez sélectionner au moins un niveau.'])->withInput();
            }
        }
        return redirect()->route('admin.modules')->with('success', 'Module créé avec succès');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur validation module: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Erreur création module: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        return view('modules.edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $module->update($data);
        return redirect()->route('modules.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('modules.index');
    }

    /**
     * Remove multiple modules from storage.
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Module::whereIn('id', $ids)->delete();
            return redirect()->route('admin.modules')->with('success', 'Modules supprimés avec succès.');
        }
        return redirect()->route('admin.modules')->with('error', 'Aucun module sélectionné.');
    }

    /**
     * Show confirmation page for multiple deletion.
     */
    public function confirmDestroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        $modules = Module::whereIn('id', $ids)->get();
        return view('admin.modules.confirm_destroy_multiple', compact('modules', 'ids'));
    }
}

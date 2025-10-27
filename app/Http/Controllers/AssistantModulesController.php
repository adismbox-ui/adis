<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Formateur;
use App\Models\Notification;
use App\Models\Utilisateur;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class AssistantModulesController extends Controller
{
    /**
     * Afficher la liste des modules
     */
    public function index()
    {
        $modules = Module::with(['niveau', 'formateur.utilisateur'])->orderBy('created_at', 'desc')->get();
        return view('assistants.modules.index', compact('modules'));
    }

    /**
     * Afficher le détail d'un module
     */
    public function show($id)
    {
        $module = Module::with(['niveau', 'formateur.utilisateur'])->findOrFail($id);
        return view('assistants.modules.show', compact('module'));
    }

    public function create()
    {
        $niveaux = Niveau::orderBy('ordre')->get();
        $formateurs = Formateur::with('utilisateur')->get();
        return view('assistants.modules.create', compact('niveaux', 'formateurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            // formateur optionnel: on peut hériter de niveau
            'formateur_id' => 'nullable|exists:formateurs,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'support' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Récupérer tous les niveaux si le bouton "tous les niveaux" est utilisé, sinon ceux sélectionnés
        if ($request->has('tous_niveaux') || empty($request->niveaux_ids)) {
            $niveaux = \App\Models\Niveau::pluck('id');
        } else {
            $niveaux = $request->niveaux_ids;
        }

        // Stocker le support une seule fois si présent
        $data = $request->except(['niveaux_ids', 'tous_niveaux']);
        if ($request->hasFile('support')) {
            $data['support'] = $request->file('support')->store('supports', 'public');
        }

        $modulesCrees = 0;
        foreach ($niveaux as $niveau_id) {
            $data['niveau_id'] = $niveau_id;
            if (empty($data['formateur_id'])) {
                $niveau = \App\Models\Niveau::find($niveau_id);
                if ($niveau && $niveau->formateur_id) {
                    $data['formateur_id'] = $niveau->formateur_id;
                }
            }
            $module = \App\Models\Module::create($data);
            $modulesCrees++;
            // Notifier l'admin pour chaque module créé
            \App\Services\NotificationService::notifyModuleCreated([
                'nom' => $module->titre,
                'id' => $module->id
            ]);
        }

        return redirect()->route('assistant.modules')->with('success', $modulesCrees.' module(s) créé(s) avec succès !');
    }

    public function edit($id)
    {
        $module = Module::findOrFail($id);
        $niveaux = Niveau::orderBy('ordre')->get();
        return view('assistants.modules.edit', compact('module', 'niveaux'));
    }

    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'lien' => 'nullable|url',
        ]);
        $module->update($request->only(['nom', 'description', 'niveau_id', 'lien']));
        return redirect()->route('assistant.modules')->with('success', 'Module modifié avec succès.');
    }

    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();
        return redirect()->route('assistant.modules')->with('success', 'Module supprimé avec succès.');
    }
} 
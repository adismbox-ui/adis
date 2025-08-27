<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscription;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inscriptions = Inscription::all();
        return view('inscriptions.index', compact('inscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'mobile_money' => 'required|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'moyen_paiement' => 'nullable|string',
        ]);
        $user = auth()->user();
        $apprenant = $user->apprenant;
        if ($apprenant && isset($data['niveau_id'])) {
            $apprenant->niveau_id = $data['niveau_id'];
            $apprenant->save();
        }
        $data['apprenant_id'] = $user->id;
        $data['date_inscription'] = now();
        $data['statut'] = 'en_attente';
        // Simuler le paiement ici (à remplacer par une vraie intégration plus tard)
        \App\Models\Inscription::create($data);
        return redirect()->route('apprenants.dashboard')->with('success', "Votre demande d'inscription a été envoyée. Elle sera validée par l'administrateur après vérification du paiement.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Inscription $inscription)
    {
        return view('inscriptions.show', compact('inscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inscription $inscription)
    {
        return view('inscriptions.edit', compact('inscription'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inscription $inscription)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $inscription->update($data);
        return redirect()->route('inscriptions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscription $inscription)
    {
        $inscription->delete();
        return redirect()->route('inscriptions.index');
    }

    /**
     * Affiche le formulaire d'inscription à un module pour l'apprenant.
     */
    public function inscriptionModuleForm(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->withErrors(['auth' => 'Vous devez être connecté pour accéder à cette page.']);
        }
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        // On récupère les modules ouverts à l'inscription, enrichis
        $modules = \App\Models\Module::with('niveau')->orderBy('titre')->get();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $selectedModuleId = $request->query('module_id');
        return view('apprenants.inscription-module', compact('modules', 'user', 'apprenant', 'niveaux', 'selectedModuleId'));
    }
}

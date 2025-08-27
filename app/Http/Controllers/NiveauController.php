<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use App\Models\Formateur;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::with(['formateur.utilisateur'])->orderBy('ordre')->get();
        $formateurs = Formateur::with('utilisateur')->get();
        return view('admin.niveaux.index', compact('niveaux', 'formateurs'));
    }

    public function show(Niveau $niveau)
    {
        return view('admin.niveaux.show', compact('niveau'));
    }

    public function create()
    {
        $formateurs = Formateur::with('utilisateur')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('admin.niveaux.create', compact('formateurs', 'sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'required|integer|min:0',
            'actif' => 'boolean',
            'formateur_id' => 'nullable|exists:formateurs,id',
            'lien_meet' => 'nullable|string|max:255',
            'session_id' => 'nullable|exists:sessions_formation,id'
        ]);

        $payload = $request->all();
        if (empty($payload['lien_meet'])) {
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            $rand = function($n) use ($chars) {
                $s = '';
                for ($i = 0; $i < $n; $i++) { $s .= $chars[random_int(0, strlen($chars)-1)]; }
                return $s;
            };
            $payload['lien_meet'] = 'https://meet.google.com/' . $rand(3) . '-' . $rand(4) . '-' . $rand(3);
        }

        Niveau::create($payload);

        return redirect()->route('admin.niveaux.index')
            ->with('success', 'Niveau créé avec succès.');
    }

    public function edit(Niveau $niveau)
    {
        $formateurs = Formateur::with('utilisateur')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('admin.niveaux.edit', compact('niveau', 'formateurs', 'sessions'));
    }

    public function update(Request $request, Niveau $niveau)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'required|integer|min:0',
            'actif' => 'boolean',
            'formateur_id' => 'nullable|exists:formateurs,id',
            'lien_meet' => 'nullable|string|max:255',
            'session_id' => 'nullable|exists:sessions_formation,id'
        ]);

        $payload = $request->all();
        // Générer automatiquement un lien Meet s'il est vide
        if (empty($payload['lien_meet'])) {
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            $rand = function($n) use ($chars) {
                $s = '';
                for ($i = 0; $i < $n; $i++) { $s .= $chars[random_int(0, strlen($chars)-1)]; }
                return $s;
            };
            $payload['lien_meet'] = 'https://meet.google.com/' . $rand(3) . '-' . $rand(4) . '-' . $rand(3);
        }

        $niveau->update($payload);

        return redirect()->route('admin.niveaux.index')
            ->with('success', 'Niveau mis à jour avec succès.');
    }

    public function destroy(Niveau $niveau)
    {
        // Vérifier s'il y a des sessions liées
        if ($niveau->sessionsFormation()->count() > 0) {
            return redirect()->route('admin.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il est utilisé par des sessions.');
        }

        $niveau->delete();

        return redirect()->route('admin.niveaux.index')
            ->with('success', 'Niveau supprimé avec succès.');
    }

    /**
     * Affecter rapidement un formateur à un niveau depuis l'index
     */
    public function assignFormateur(Request $request, Niveau $niveau)
    {
        $data = $request->validate([
            'formateur_id' => 'nullable|exists:formateurs,id',
        ]);
        $niveau->update(['formateur_id' => $data['formateur_id'] ?? null]);
        return redirect()->route('admin.niveaux.index')->with('success', 'Formateur affecté au niveau avec succès.');
    }
} 
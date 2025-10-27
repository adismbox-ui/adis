<?php

namespace App\Http\Controllers;

use App\Models\Don;
use App\Models\Projet;
use Illuminate\Http\Request;

class AdminDonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dons = Don::with('projet')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.dons.index', compact('dons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projets = Projet::all();
        return view('admin.dons.create', compact('projets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_donateur' => 'required|string|max:255',
            'email_donateur' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'montant' => 'required|numeric|min:100',
            'type_don' => 'required|in:ponctuel,mensuel',
            'projet_id' => 'nullable|exists:projets,id',
            'mode_paiement' => 'required|in:carte,virement,mobile',
            'message' => 'nullable|string|max:1000',
            'recu_demande' => 'nullable|boolean',
        ]);

        try {
            $don = Don::create([
                'nom_donateur' => $request->nom_donateur,
                'email_donateur' => $request->email_donateur,
                'telephone' => $request->telephone,
                'montant' => $request->montant,
                'type_don' => $request->type_don,
                'projet_id' => $request->projet_id,
                'mode_paiement' => $request->mode_paiement,
                'message' => $request->message,
                'recu_demande' => $request->has('recu_demande'),
                'statut' => 'en_attente',
                'date_don' => now(),
                'numero_reference' => 'DON-' . date('Ymd') . '-' . strtoupper(uniqid()),
            ]);

            return redirect()->route('admin.dons.show', $don)
                            ->with('success', 'Don créé avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Une erreur est survenue lors de la création du don.')
                            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Don $don)
    {
        $don->load('projet');
        return view('admin.dons.show', compact('don'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Don $don)
    {
        $projets = Projet::all();
        return view('admin.dons.edit', compact('don', 'projets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Don $don)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,confirme,annule,refuse',
            'notes_admin' => 'nullable|string|max:1000'
        ]);

        $don->update([
            'statut' => $request->statut,
            'notes_admin' => $request->notes_admin,
            'date_confirmation' => $request->statut === 'confirme' ? now() : null,
            'paiement_confirme' => $request->statut === 'confirme'
        ]);

        return redirect()->route('admin.dons.show', $don)
                        ->with('success', 'Statut du don mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Don $don)
    {
        $don->delete();
        return redirect()->route('admin.dons.index')
                        ->with('success', 'Don supprimé avec succès.');
    }

    /**
     * Confirmer un don
     */
    public function confirmer(Don $don)
    {
        $don->update([
            'statut' => 'confirme',
            'paiement_confirme' => true,
            'date_confirmation' => now()
        ]);

        return redirect()->back()->with('success', 'Don confirmé avec succès.');
    }

    /**
     * Annuler un don
     */
    public function annuler(Don $don)
    {
        $don->update([
            'statut' => 'annule',
            'paiement_confirme' => false
        ]);

        return redirect()->back()->with('success', 'Don annulé avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paiement;

use App\Models\Module;

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paiements = Paiement::all();
        return view('paiements.index', compact('paiements'));
    }

    /**
     * Show the paiement page with history and form.
     */
    public function page()
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        $paiements = [];
        $modules = [];
        if ($apprenant) {
            $paiements = Paiement::with('module')->where('apprenant_id', $apprenant->id)->orderByDesc('date_paiement')->get();
            $modules = Module::all();
        }
        return view('paiement', compact('paiements', 'modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paiements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'montant' => 'required|numeric|min:0',
            'methode' => 'required|string',
            'reference' => 'nullable|string',
        ]);
        if (!$apprenant) {
            return redirect()->back()->with('error', 'Aucun apprenant connecté.');
        }
        $data['apprenant_id'] = $apprenant->id;
        $data['date_paiement'] = now();
        $data['statut'] = 'en_attente';
        Paiement::create($data);
        return redirect()->route('apprenants.dashboard')->with('success', 'Paiement enregistré, en attente de validation.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement)
    {
        return view('paiements.show', compact('paiement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paiement $paiement)
    {
        return view('paiements.edit', compact('paiement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paiement $paiement)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $paiement->update($data);
        return redirect()->route('paiements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement)
    {
        $paiement->delete();
        return redirect()->route('paiements.index');
    }
}

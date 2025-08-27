<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandeCoursMaison;
use App\Models\Formateur;

class ValiderCoursController extends Controller
{
    // Page liste des demandes à valider
    public function index()
    {
        // Récupérer les demandes qui nécessitent une action de l'admin
        // - en_attente : demandes initiales
        // - acceptee_formateur : demandes acceptées par les formateurs (à valider définitivement)
        $demandes = DemandeCoursMaison::whereIn('statut', [
            DemandeCoursMaison::STATUT_EN_ATTENTE,
            DemandeCoursMaison::STATUT_ACCEPTEE_FORMATEUR,
            DemandeCoursMaison::STATUT_VALIDEE
        ])
        ->with('user')
        ->latest()
        ->get();

        $niveauxModules = [];
        foreach ($demandes as $demande) {
            $module = \App\Models\Module::where('titre', $demande->module)->with('niveau')->first();
            $niveauxModules[$demande->id] = $module && $module->niveau ? $module->niveau->nom : '-';
        }
        return view('valider-cours.index', compact('demandes', 'niveauxModules'));
    }

    // Voir une demande et formulaire d'assignation
    public function show($id)
    {
        $demande = DemandeCoursMaison::with(['user','niveau'])->findOrFail($id);
        // Lister les formateurs pertinents: ceux qui enseignent des modules du niveau demandé, sinon tous
        $formateurs = Formateur::with(['utilisateur','modules'])
            ->when($demande->niveau_id, function($q) use ($demande) {
                $q->whereHas('modules', function($mq) use ($demande) {
                    $mq->where('niveau_id', $demande->niveau_id);
                });
            })
            ->orderByDesc('valide')
            ->get();
        // Fallback: si aucun formateur ne correspond au niveau, afficher tous les formateurs
        if ($formateurs->isEmpty()) {
            $formateurs = Formateur::with('utilisateur')->orderByDesc('valide')->get();
        }
        return view('valider-cours.show', compact('demande', 'formateurs'));
    }

    // Valider une demande et assigner un formateur (passe en attente de validation par le formateur)
    public function valider(Request $request, $id)
    {
        $demande = DemandeCoursMaison::findOrFail($id);
        // Validation immédiate par l'admin avec assignation du formateur
        $demande->formateur_id = $request->formateur_id;
        $demande->statut = DemandeCoursMaison::STATUT_VALIDEE;
        $demande->save();
        return redirect()->route('valider.cours.index')->with('success', 'Demande validée et formateur assigné.');
    }

    // Refuser une demande
    public function refuser($id)
    {
        $demande = DemandeCoursMaison::findOrFail($id);
        $demande->statut = DemandeCoursMaison::STATUT_REFUSEE;
        $demande->save();
        return redirect()->route('valider.cours.index')->with('success', 'Demande refusée.');
    }
}

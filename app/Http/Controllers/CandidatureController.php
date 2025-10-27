<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidatureController extends Controller
{
    /**
     * Store a newly created candidature.
     */
    public function store(Request $request)
    {
        $request->validate([
            'raison_sociale' => 'required|string|max:255',
            'nom_responsable' => 'required|string|max:255',
            'statut_juridique' => 'required|string|max:255',
            'rccm' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'site_web' => 'nullable|url|max:255',
            'reference_appel' => 'required|string|max:50',
            'offre_technique' => 'required|file|mimes:pdf|max:10240',
            'offre_financiere' => 'required|file|mimes:pdf|max:10240',
            'justificatif_paiement' => 'required|file|mimes:pdf|max:10240',
            'references' => 'nullable|file|mimes:pdf|max:10240',
            'declaration_honneur' => 'required|accepted',
        ]);

        try {
            // Stocker les fichiers
            $offreTechniquePath = $request->file('offre_technique')->store('candidatures/offres_techniques', 'public');
            $offreFinancierePath = $request->file('offre_financiere')->store('candidatures/offres_financieres', 'public');
            $justificatifPath = $request->file('justificatif_paiement')->store('candidatures/justificatifs', 'public');
            
            $referencesPath = null;
            if ($request->hasFile('references')) {
                $referencesPath = $request->file('references')->store('candidatures/references', 'public');
            }

            // Sauvegarder la candidature dans la base de données
            Candidature::create([
                'raison_sociale' => $request->raison_sociale,
                'nom_responsable' => $request->nom_responsable,
                'statut_juridique' => $request->statut_juridique,
                'rccm' => $request->rccm,
                'contact' => $request->contact,
                'site_web' => $request->site_web,
                'reference_appel' => $request->reference_appel,
                'offre_technique_path' => $offreTechniquePath,
                'offre_financiere_path' => $offreFinancierePath,
                'justificatif_paiement_path' => $justificatifPath,
                'references_path' => $referencesPath,
                'declaration_honneur' => true,
                'statut' => 'en_attente'
            ]);
            
            return redirect()->route('projets.appel-a-projets.index')
                ->with('success', 'Votre candidature a été soumise avec succès ! Nous vous contacterons dans les plus brefs délais.');

        } catch (\Exception $e) {
            // En cas d'erreur, supprimer les fichiers déjà uploadés
            if (isset($offreTechniquePath)) Storage::disk('public')->delete($offreTechniquePath);
            if (isset($offreFinancierePath)) Storage::disk('public')->delete($offreFinancierePath);
            if (isset($justificatifPath)) Storage::disk('public')->delete($justificatifPath);
            if (isset($referencesPath)) Storage::disk('public')->delete($referencesPath);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la soumission de votre candidature. Veuillez réessayer.']);
        }
    }
} 
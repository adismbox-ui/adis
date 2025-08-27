<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCandidatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $candidaturesEnAttente = Candidature::enAttente()->orderBy('created_at', 'desc')->get();
        $candidaturesAcceptees = Candidature::acceptees()->orderBy('created_at', 'desc')->get();
        $candidaturesRefusees = Candidature::refusees()->orderBy('created_at', 'desc')->get();
        
        return view('admin.candidatures.index', compact('candidaturesEnAttente', 'candidaturesAcceptees', 'candidaturesRefusees'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidature $candidature)
    {
        return view('admin.candidatures.show', compact('candidature'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidature $candidature)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee',
            'notes_admin' => 'nullable|string'
        ]);

        $candidature->update([
            'statut' => $request->statut,
            'notes_admin' => $request->notes_admin
        ]);

        return redirect()->route('admin.candidatures.index')
            ->with('success', 'Statut de la candidature mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidature $candidature)
    {
        // Supprimer les fichiers associés
        if ($candidature->offre_technique_path) {
            Storage::disk('public')->delete($candidature->offre_technique_path);
        }
        if ($candidature->offre_financiere_path) {
            Storage::disk('public')->delete($candidature->offre_financiere_path);
        }
        if ($candidature->justificatif_paiement_path) {
            Storage::disk('public')->delete($candidature->justificatif_paiement_path);
        }
        if ($candidature->references_path) {
            Storage::disk('public')->delete($candidature->references_path);
        }

        $candidature->delete();

        return redirect()->route('admin.candidatures.index')
            ->with('success', 'Candidature supprimée avec succès !');
    }

    /**
     * Télécharger un fichier de candidature
     */
    public function downloadFile(Candidature $candidature, $fileType)
    {
        $filePath = match($fileType) {
            'offre_technique' => $candidature->offre_technique_path,
            'offre_financiere' => $candidature->offre_financiere_path,
            'justificatif_paiement' => $candidature->justificatif_paiement_path,
            'references' => $candidature->references_path,
            default => null
        };

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Fichier non trouvé');
        }

        return Storage::disk('public')->download($filePath);
    }
} 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PresenceRequest;
use App\Models\PresenceMark;
use App\Models\Apprenant;
use Barryvdh\DomPDF\Facade\Pdf;

class PresenceController extends Controller
{
    // Formateur: page to open a presence request
    public function formateurIndex()
    {
        $user = Auth::user();
        $formateur = $user->formateur ?? null;
        $requests = PresenceRequest::where('formateur_id', $formateur?->id)->latest()->limit(20)->get();
        return view('formateurs.presence.index', compact('requests'));
    }

    public function formateurOpen(Request $request)
    {
        $user = Auth::user();
        $formateur = $user->formateur ?? null;

        // Fallback: si l'utilisateur est assistant lié à un formateur, l'utiliser
        if (!$formateur) {
            $assistant = \App\Models\Assistant::where('utilisateur_id', $user->id)->first();
            if ($assistant && $assistant->formateur) {
                $formateur = $assistant->formateur;
            }
        }

        // Ultime fallback: créer un formateur minimal pour cet utilisateur
        if (!$formateur) {
            $formateur = \App\Models\Formateur::create([
                'utilisateur_id' => $user->id,
                'valide' => false,
            ]);
        }

        $data = $request->validate([
            'nom' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string|max:2000',
            'nom_formateur' => 'nullable|string|max:255',
            'module' => 'nullable|string|max:255',
        ]);

        $nomSeance = $data['nom'] ?? null;
        $commentaire = $data['commentaire'] ?? null;
        if (!empty($data['module'])) {
            $commentaire = trim('Module: '.$data['module'].(empty($commentaire) ? '' : ' | '.$commentaire));
        }
        if (!$nomSeance && !empty($data['nom_formateur'])) {
            $nomSeance = 'Présence de '.$data['nom_formateur'];
        }

        $presence = PresenceRequest::create([
            'formateur_id' => $formateur->id,
            'nom' => $nomSeance,
            'commentaire' => $commentaire,
            'is_open' => true,
        ]);

        return redirect()->route('formateurs.presence.index')->with('success', 'Présence ouverte.');
    }

    public function formateurClose(PresenceRequest $presenceRequest)
    {
        $this->authorizeOwner($presenceRequest);
        $presenceRequest->update(['is_open' => false]);
        return redirect()->route('formateurs.presence.index')->with('success', 'Présence fermée.');
    }

    // Apprenant: page to mark presence on latest open request
    public function apprenantIndex(Request $request)
    {
        $targetId = $request->query('request');
        if ($targetId) {
            $latestOpen = PresenceRequest::where('id', $targetId)->where('is_open', true)->first();
        } else {
            $latestOpen = PresenceRequest::where('is_open', true)->latest()->first();
        }
        return view('apprenants.presence.index', compact('latestOpen'));
    }

    public function apprenantMark(Request $request)
    {
        $request->validate([
            'presence_request_id' => 'required|exists:presence_requests,id',
        ]);

        $presenceRequest = PresenceRequest::findOrFail($request->input('presence_request_id'));
        if (!$presenceRequest->is_open) {
            return back()->withErrors(['presence_request_id' => 'Cette présence est fermée.']);
        }

        $user = Auth::user();
        $apprenant = $user->apprenant ?? null;
        if (!$apprenant) {
            return back()->withErrors(['user' => 'Aucun apprenant associé.']);
        }

        PresenceMark::firstOrCreate([
            'presence_request_id' => $presenceRequest->id,
            'apprenant_id' => $apprenant->id,
        ], [
            'present_at' => now(),
        ]);

        return back()->with('success', 'Présence marquée.');
    }

    // Admin: list of open presence and attendees
    public function adminIndex()
    {
        $openRequests = PresenceRequest::with(['formateur', 'marks.apprenant'])->orderByDesc('created_at')->get();
        return view('admin.presence.index', compact('openRequests'));
    }

    // Admin: download PDF for a presence request (present and absent)
    public function adminPdf(PresenceRequest $presenceRequest)
    {
        $presenceRequest->load(['formateur', 'marks.apprenant.utilisateur']);
        $allApprenants = Apprenant::with('utilisateur')->get();
        $presentIds = $presenceRequest->marks->pluck('apprenant_id')->all();
        $presents = $presenceRequest->marks->map(function ($mark) {
            return $mark->apprenant;
        })->filter()->sortBy(function ($a) {
            $nom = trim(($a->nom ?? '').' '.($a->prenom ?? ''));
            if ($nom === '' && $a->utilisateur) {
                $nom = trim(($a->utilisateur->nom ?? '').' '.($a->utilisateur->prenom ?? ''));
            }
            return mb_strtolower($nom);
        })->values();
        $absents = $allApprenants->whereNotIn('id', $presentIds)->values()->sortBy(function ($a) {
            $nom = trim(($a->nom ?? '').' '.($a->prenom ?? ''));
            if ($nom === '' && $a->utilisateur) {
                $nom = trim(($a->utilisateur->nom ?? '').' '.($a->utilisateur->prenom ?? ''));
            }
            return mb_strtolower($nom);
        })->values();

        $pdf = Pdf::loadView('admin.presence.pdf', [
            'presence' => $presenceRequest,
            'presents' => $presents,
            'absents' => $absents,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $fileName = 'presence_'.$presenceRequest->id.'.pdf';
        return $pdf->download($fileName);
    }

    public function formateurSheet(PresenceRequest $presenceRequest)
    {
        $this->authorizeOwner($presenceRequest);
        $presenceRequest->load(['formateur', 'marks.apprenant']);
        return view('formateurs.presence.feuille', [
            'presence' => $presenceRequest,
            'generatedAt' => now(),
        ]);
    }

    public function presentFormat()
    {
        // Simple gabarit imprimable indépendant d'une séance
        return view('formateurs.presence.present-format', [
            'generatedAt' => now(),
        ]);
    }

    public function formateurDebug(PresenceRequest $presenceRequest)
    {
        $this->authorizeOwner($presenceRequest);
        $presenceRequest->load(['marks.apprenant.utilisateur']);
        $names = $presenceRequest->marks->map(function($m){
            $apprenant = $m->apprenant;
            if (!$apprenant) return null;
            $nom = trim(($apprenant->nom ?? '').' '.($apprenant->prenom ?? ''));
            if ($nom === '' && $apprenant->utilisateur) {
                $nom = trim(($apprenant->utilisateur->nom ?? '').' '.($apprenant->utilisateur->prenom ?? ''));
            }
            return $nom !== '' ? $nom : null;
        })->filter()->values();
        return response()->json([
            'id' => $presenceRequest->id,
            'count' => $names->count(),
            'names' => $names,
        ]);
    }

    private function authorizeOwner(PresenceRequest $presenceRequest): void
    {
        $user = Auth::user();
        $formateurId = $user->formateur->id ?? null;
        abort_unless($formateurId && $presenceRequest->formateur_id === $formateurId, 403);
    }
}


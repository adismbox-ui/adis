<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Niveau;
use App\Models\Notification;
use App\Models\Utilisateur;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class AssistantNiveauxController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::orderBy('ordre')->get();
        return view('assistants.niveaux.index', compact('niveaux'));
    }

    public function create()
    {
        $formateurs = \App\Models\Formateur::with('utilisateur')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('assistants.niveaux.create', compact('formateurs', 'sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'required|integer|min:0',
            'actif' => 'boolean',
            'formateur_id' => 'nullable|exists:formateurs,id',
            'session_id' => 'nullable|exists:sessions_formation,id',
            'lien_meet' => 'nullable|string',
        ]);

        // Auto-générer un lien Meet si vide
        if (!$request->filled('lien_meet')) {
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            $rand = function ($n) use ($chars) { $s=''; for($i=0;$i<$n;$i++){$s.=$chars[random_int(0, strlen($chars)-1)];} return $s; };
            $request->merge(['lien_meet' => "https://meet.google.com/{$rand(3)}-{$rand(4)}-{$rand(3)}"]);
        }

        $niveau = Niveau::create($request->all());

        // Notifier l'admin avec le nouveau système
        NotificationService::notifyAssistantAction(
            'niveau',
            'Nouveau niveau créé',
            "Un nouveau niveau a été créé : {$niveau->nom}",
            [
                'niveau_nom' => $niveau->nom,
                'niveau_ordre' => $niveau->ordre,
                'niveau_id' => $niveau->id
            ]
        );
        
        return redirect()->route('assistant.niveaux')->with('success', 'Niveau créé avec succès ! L\'admin a été notifié.');
    }
} 
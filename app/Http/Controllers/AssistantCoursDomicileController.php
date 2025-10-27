<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandeCoursMaison;
use App\Models\Notification;
use App\Models\Utilisateur;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class AssistantCoursDomicileController extends Controller
{
    public function index()
    {
        $cours = \App\Models\DemandeCoursMaison::with(['user', 'module.niveau'])->orderBy('created_at', 'desc')->get();
        return view('assistants.cours_domicile.index', compact('cours'));
    }

    public function create()
    {
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return view('assistants.cours_domicile.create', compact('niveaux'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'date' => 'required|date',
            'niveau_id' => 'required|exists:niveaux,id',
        ]);
        $cours = \App\Models\DemandeCoursMaison::create([
            'titre' => $request->titre,
            'date' => $request->date,
            'niveau_id' => $request->niveau_id,
            'user_id' => auth()->id(),
        ]);
        $admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'cours_domicile',
                'message' => 'Un nouveau cours à domicile a été créé par l\'assistant '.(auth()->user()->prenom ?? '').' '.(auth()->user()->nom ?? '').' : '.$cours->titre,
            ]);
        }
        return redirect()->route('assistant.cours_domicile')->with('success', 'Cours à domicile créé et notification envoyée à l\'admin.');
    }

    public function valider($id)
    {
        $demande = \App\Models\DemandeCoursMaison::findOrFail($id);
        $demande->statut = 'validee';
        $demande->save();
        $admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'cours_domicile',
                'title' => 'Cours à domicile validé',
                'message' => 'La demande de cours à domicile #'.$demande->id.' a été validée par l\'assistant.',
            ]);
        }
        return redirect()->route('assistant.cours_domicile')->with('success', 'Demande validée et notification envoyée à l\'admin.');
    }

    public function refuser($id)
    {
        $demande = \App\Models\DemandeCoursMaison::findOrFail($id);
        $demande->statut = 'refusee';
        $demande->save();
        $admin = \App\Models\Utilisateur::where('type_compte', 'admin')->first();
        if ($admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'cours_domicile',
                'title' => 'Cours à domicile refusé',
                'message' => 'La demande de cours à domicile #'.$demande->id.' a été refusée par l\'assistant.',
            ]);
        }
        return redirect()->route('assistant.cours_domicile')->with('success', 'Demande refusée et notification envoyée à l\'admin.');
    }
} 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $utilisateurs = Utilisateur::all();
        return view('utilisateurs.index', compact('utilisateurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('utilisateurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $current = auth()->user();
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:Homme,Femme',
            'categorie' => 'required|in:Enfant,Etudiant,Professionnel,Enseignant',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:utilisateurs,email',
            'mot_de_passe' => 'required|string|min:6',
            'type_compte' => 'required|in:admin,assistant,formateur,apprenant',
        ]);

        // Restreindre les types de compte selon le profil connecté
        if ($current && $current->type_compte === 'admin') {
            if (!in_array($data['type_compte'], ['formateur', 'apprenant'], true)) {
                return back()->withErrors(['type_compte' => 'En tant qu\'admin, vous pouvez créer un formateur ou un apprenant.'])->withInput();
            }
        } elseif ($current && $current->type_compte === 'formateur') {
            // Un formateur ne peut créer qu'un assistant (pas d'apprenant)
            if (!in_array($data['type_compte'], ['assistant'], true)) {
                return back()->withErrors(['type_compte' => 'En tant que formateur, vous pouvez uniquement créer un assistant.'])->withInput();
            }
        }

        // Si l'admin crée un apprenant avec un email déjà lié à un formateur, c'est autorisé
        // (le but est de permettre à l'admin d'activer "apprenant" pour un formateur existant)

        // Hash the password before saving
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        
        // Set default values
        $data['actif'] = true;
        $data['email_verified_at'] = now();

        Utilisateur::create($data);
        
        // Si c'est un formateur, créer aussi l'entrée dans la table formateurs
        if ($data['type_compte'] === 'formateur') {
            $utilisateur = Utilisateur::where('email', $data['email'])->first();
            if ($utilisateur) {
                \App\Models\Formateur::firstOrCreate([
                    'utilisateur_id' => $utilisateur->id
                ]);
            }
        }
        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Utilisateur $utilisateur)
    {
        return view('utilisateurs.show', compact('utilisateur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Utilisateur $utilisateur)
    {
        return view('utilisateurs.edit', compact('utilisateur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Utilisateur $utilisateur)
    {
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:Homme,Femme',
            'categorie' => 'required|in:Enfant,Etudiant,Professionnel,Enseignant',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:utilisateurs,email,' . $utilisateur->id,
            'mot_de_passe' => 'nullable|string|min:6',
            'type_compte' => 'required|in:admin,assistant,formateur,apprenant',
        ]);

        // Hash the password if provided
        if (!empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        } else {
            unset($data['mot_de_passe']);
        }

        $utilisateur->update($data);
        // Ajout : création automatique d'un formateur si le type_compte devient formateur
        if ($utilisateur->type_compte === 'formateur' && !$utilisateur->formateur) {
            \App\Models\Formateur::create([
                'utilisateur_id' => $utilisateur->id,
                'valide' => false,
            ]);
        }
        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Utilisateur $utilisateur)
    {
        $utilisateur->delete();
        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur supprimé avec succès!');
    }
}

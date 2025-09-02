<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Utilisateur;

class UtilisateurApiController extends Controller
{
    
    public function index()
    {
        $utilisateurs = Utilisateur::all();
        return response()->json(['utilisateurs' => $utilisateurs], 200);
    }

    public function create()
    {
        return response()->json(['message' => 'Endpoint pour création d\'utilisateur'], 200);
    }

    public function store(Request $request)
    {
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
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        $data['actif'] = true;
        $data['email_verified_at'] = now();
        $utilisateur = Utilisateur::create($data);
        if ($data['type_compte'] === 'formateur') {
            \App\Models\Formateur::firstOrCreate([
                'utilisateur_id' => $utilisateur->id
            ]);
        }
        return response()->json(['utilisateur' => $utilisateur, 'message' => 'Utilisateur créé avec succès!'], 201);
    }

    public function storeAdmin(Request $request)
    {
        $user = auth()->user();
        // Optionnel : vérifier que c'est un admin
        // if (!$user || $user->type_compte !== 'admin') {
        //     return response()->json(['error' => 'Non autorisé'], 403);
        // }

        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:Homme,Femme',
            'categorie' => 'nullable|string',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:utilisateurs,email',
            'mot_de_passe' => 'required|string|min:6',
        ]);
        $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        $data['type_compte'] = 'admin';
        $data['actif'] = true;
        $data['email_verified_at'] = now();
        $admin = \App\Models\Utilisateur::create($data);
        return response()->json(['admin' => $admin, 'message' => 'Admin créé avec succès!'], 201);
    }

    public function show(Utilisateur $utilisateur)
    {
        return response()->json(['utilisateur' => $utilisateur], 200);
    }

    public function edit(Utilisateur $utilisateur)
    {
        return response()->json(['utilisateur' => $utilisateur], 200);
    }

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
        if (!empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
        } else {
            unset($data['mot_de_passe']);
        }
        $utilisateur->update($data);
        if ($utilisateur->type_compte === 'formateur' && !$utilisateur->formateur) {
            \App\Models\Formateur::create([
                'utilisateur_id' => $utilisateur->id,
                'valide' => false,
            ]);
        }
        return response()->json(['utilisateur' => $utilisateur, 'message' => 'Utilisateur mis à jour avec succès!'], 200);
    }

    public function destroy(Utilisateur $utilisateur)
    {
        $utilisateur->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès!'], 204);
    }

    public function listeUtilisateursParType()
    {
        $user = auth()->user();
        // Optionnel : vérifier que c'est un admin
        // if (!$user || $user->type_compte !== 'admin') {
        //     return response()->json(['error' => 'Non autorisé'], 403);
        // }

        // Admins
        $admins = \App\Models\Utilisateur::where('type_compte', 'admin')->get()->map(function($admin) {
            return [
                'nom' => $admin->nom ?? null,
                'prenom' => $admin->prenom ?? null,
                'email' => $admin->email ?? null,
                'telephone' => $admin->telephone ?? null,
                'type_compte' => 'admin',
            ];
        });

        // Apprenants
        $apprenants = \App\Models\Utilisateur::where('type_compte', 'apprenant')->get()->map(function($apprenant) {
            return [
                'nom' => $apprenant->nom ?? null,
                'prenom' => $apprenant->prenom ?? null,
                'email' => $apprenant->email ?? null,
                'telephone' => $apprenant->telephone ?? null,
                'type_compte' => 'apprenant',
            ];
        });

        // Formateurs valides
        $formateurs = \App\Models\Formateur::with('utilisateur')->where('valide', true)->get()->map(function($formateur) {
            return [
                'nom' => $formateur->utilisateur->nom ?? null,
                'prenom' => $formateur->utilisateur->prenom ?? null,
                'email' => $formateur->utilisateur->email ?? null,
                'telephone' => $formateur->utilisateur->telephone ?? null,
                'type_compte' => 'formateur',
            ];
        });

        // Liste complète de tous les utilisateurs
        $tousUtilisateurs = \App\Models\Utilisateur::all()->map(function($utilisateur) {
            return [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom ?? null,
                'prenom' => $utilisateur->prenom ?? null,
                'email' => $utilisateur->email ?? null,
                'telephone' => $utilisateur->telephone ?? null,
                'sexe' => $utilisateur->sexe ?? null,
                'categorie' => $utilisateur->categorie ?? null,
                'type_compte' => $utilisateur->type_compte,
                'actif' => $utilisateur->actif,
                'email_verified_at' => $utilisateur->email_verified_at,
                'created_at' => $utilisateur->created_at,
                'updated_at' => $utilisateur->updated_at
            ];
        });

        return response()->json([
            'admins' => $admins,
            'apprenants' => $apprenants,
            'formateurs_valides' => $formateurs,
            'tous_utilisateurs' => $tousUtilisateurs,
            'total_utilisateurs' => $tousUtilisateurs->count(),
            'statistiques' => [
                'total_admins' => $admins->count(),
                'total_apprenants' => $apprenants->count(),
                'total_formateurs_valides' => $formateurs->count(),
                'total_assistants' => \App\Models\Utilisateur::where('type_compte', 'assistant')->count()
            ]
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ApiAuthController extends Controller
{
    /**
     * Login API
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $utilisateur = Utilisateur::where('email', $credentials['email'])->first();

        // Log pour débogage
        \Log::info('API Login attempt', [
            'email' => $credentials['email'],
            'user_found' => $utilisateur ? true : false,
            'user_id' => $utilisateur?->id,
            'user_active' => $utilisateur?->actif,
            'user_type' => $utilisateur?->type_compte,
        ]);

        if (!$utilisateur) {
            \Log::warning('API Login: User not found', ['email' => $credentials['email']]);
            return response()->json([
                'success' => false,
                'error' => 'Identifiants invalides'
            ], 401);
        }

        if (!Hash::check($credentials['password'], $utilisateur->mot_de_passe)) {
            \Log::warning('API Login: Invalid password', ['email' => $credentials['email'], 'user_id' => $utilisateur->id]);
            return response()->json([
                'success' => false,
                'error' => 'Identifiants invalides'
            ], 401);
        }

        if (!$utilisateur->actif) {
            return response()->json([
                'success' => false,
                'error' => 'Votre compte est désactivé.'
            ], 403);
        }

        if ($utilisateur->type_compte === 'apprenant' && empty($utilisateur->email_verified_at)) {
            return response()->json([
                'success' => false,
                'error' => 'Veuillez vérifier votre adresse email pour activer votre compte.'
            ], 403);
        }

        // Vérifier si formateur est validé
        if ($utilisateur->type_compte === 'formateur') {
            $formateur = $utilisateur->formateur;
            if ($formateur && isset($formateur->valide) && !$formateur->valide) {
                \Log::info('API Login: Formateur not validated', ['user_id' => $utilisateur->id]);
                return response()->json([
                    'success' => false,
                    'error' => "Votre compte formateur n'a pas encore été validé par l'administrateur."
                ], 403);
            }
        }

        // Créer un token Sanctum
        $token = $utilisateur->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_compte' => $utilisateur->type_compte,
                'sexe' => $utilisateur->sexe,
                'telephone' => $utilisateur->telephone,
            ],
            'type_compte' => $utilisateur->type_compte,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => null, // Sanctum tokens n'expirent pas par défaut
        ], 200);
    }

    /**
     * Register API
     */
    public function register(Request $request)
    {
        $rules = [
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6|confirmed',
            'sexe' => 'required|in:Homme,Femme',
            'type_compte' => 'required|in:admin,assistant,formateur,apprenant',
            'telephone' => 'nullable|string|max:20',
        ];

        if ($request->type_compte === 'apprenant' || $request->type_compte === 'formateur') {
            $rules['categorie'] = 'required|in:Enfant,Etudiant,Professionnel';
        }

        $data = $request->validate($rules);

        // Générer un token de vérification pour apprenant
        $verification_token = null;
        if ($request->type_compte === 'apprenant') {
            $verification_token = Str::random(48);
        }

        $utilisateur = Utilisateur::create([
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'email' => $data['email'],
            'mot_de_passe' => Hash::make($data['password']),
            'type_compte' => $data['type_compte'],
            'sexe' => $data['sexe'],
            'categorie' => $data['categorie'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'actif' => in_array($request->type_compte, ['apprenant', 'formateur'], true) ? false : true,
            'email_verified_at' => ($request->type_compte === 'apprenant') ? null : now(),
            'verification_token' => $verification_token,
        ]);

        // Créer le profil associé
        if ($request->type_compte === 'formateur') {
            \App\Models\Formateur::create([
                'utilisateur_id' => $utilisateur->id,
                'valide' => false,
            ]);
        } elseif ($request->type_compte === 'apprenant') {
            \App\Models\Apprenant::create([
                'utilisateur_id' => $utilisateur->id,
                'niveau_id' => $request->niveau_id ?? null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'user' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_compte' => $utilisateur->type_compte,
            ],
        ], 201);
    }

    /**
     * Logout API
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ], 200);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $utilisateur = $request->user();
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_compte' => $utilisateur->type_compte,
                'sexe' => $utilisateur->sexe,
                'telephone' => $utilisateur->telephone,
            ],
        ], 200);
    }
}


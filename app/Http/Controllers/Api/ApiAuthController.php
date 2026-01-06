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
        try {
            // Nettoyer les données reçues (trim pour éviter les espaces)
            $email = trim($request->input('email', ''));
            $password = $request->input('password', '');
            
            $credentials = [
                'email' => $email,
                'password' => $password,
            ];
            
            // Validation
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $utilisateur = Utilisateur::where('email', $credentials['email'])->first();

        // Log détaillé pour débogage
        \Log::info('API Login attempt', [
            'email' => $credentials['email'],
            'password_length' => strlen($credentials['password']),
            'password_first_chars' => substr($credentials['password'], 0, 3) . '...',
            'user_found' => $utilisateur ? true : false,
            'user_id' => $utilisateur?->id,
            'user_active' => $utilisateur?->actif,
            'user_type' => $utilisateur?->type_compte,
            'has_password_hash' => $utilisateur ? !empty($utilisateur->mot_de_passe) : false,
            'password_hash_length' => $utilisateur ? strlen($utilisateur->mot_de_passe) : 0,
        ]);

        if (!$utilisateur) {
            \Log::warning('API Login: User not found', ['email' => $credentials['email']]);
            return response()->json([
                'success' => false,
                'error' => 'Identifiants invalides'
            ], 401);
        }

        // Test du mot de passe avec plusieurs variantes (au cas où il y aurait des espaces)
        $passwordCheck = Hash::check($credentials['password'], $utilisateur->mot_de_passe);
        $passwordCheckTrimmed = Hash::check(trim($credentials['password']), $utilisateur->mot_de_passe);
        
        if (!$passwordCheck && !$passwordCheckTrimmed) {
            \Log::warning('API Login: Invalid password', [
                'email' => $credentials['email'], 
                'user_id' => $utilisateur->id,
                'password_received_length' => strlen($credentials['password']),
                'password_hash_starts_with' => substr($utilisateur->mot_de_passe, 0, 7),
            ]);
            
            // Test supplémentaire : vérifier si le mot de passe en base est bien hashé
            if (!str_starts_with($utilisateur->mot_de_passe, '$2y$') && !str_starts_with($utilisateur->mot_de_passe, '$2a$')) {
                \Log::error('API Login: Password not properly hashed in database', [
                    'user_id' => $utilisateur->id,
                    'password_field' => substr($utilisateur->mot_de_passe, 0, 20),
                ]);
            }
            
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
            try {
                $token = $utilisateur->createToken('mobile-app')->plainTextToken;
            } catch (\Exception $e) {
                \Log::error('API Login: Token creation failed', [
                    'user_id' => $utilisateur->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                // Vérifier si la table personal_access_tokens existe
                try {
                    \DB::table('personal_access_tokens')->limit(1)->get();
                } catch (\Exception $dbError) {
                    \Log::error('API Login: personal_access_tokens table missing', [
                        'error' => $dbError->getMessage(),
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'Erreur de configuration serveur. Veuillez contacter l\'administrateur.',
                        'details' => 'Table personal_access_tokens manquante. Exécutez: php artisan migrate'
                    ], 500);
                }
                
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors de la création du token. Veuillez réessayer.'
                ], 500);
            }

            \Log::info('API Login: Success', [
                'user_id' => $utilisateur->id,
                'type_compte' => $utilisateur->type_compte,
            ]);

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
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('API Login: Validation error', [
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('API Login: Unexpected error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur. Veuillez réessayer plus tard.',
                'message' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Register API
     */
    public function register(Request $request)
    {
        try {
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
            try {
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
            } catch (\Exception $e) {
                \Log::error('API Register: Error creating profile', [
                    'user_id' => $utilisateur->id,
                    'type_compte' => $request->type_compte,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Continue anyway, the user is created
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('API Register: Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'details' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('API Register: Unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur (500): ' . $e->getMessage(),
                'details' => 'Un problème inattendu est survenu lors de l\'inscription. Veuillez réessayer ou contacter l\'administrateur.'
            ], 500);
        }
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


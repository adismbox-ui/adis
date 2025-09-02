<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Utilisateur;
use Illuminate\Support\Str;


class AuthApiController extends Controller
{
public function register(Request $request)
{
    // Vérifier unicité admin (un seul admin autorisé)
    if ($request->type_compte === 'admin' && \App\Models\Utilisateur::where('type_compte', 'admin')->exists()) {
        return response()->json(['error' => 'Un administrateur existe déjà.'], 422);
    }
    
    // RESTRICTION : Empêcher l'inscription des apprenants si une session a commencé
    if ($request->type_compte === 'apprenant') {
        // Vérifier si une session de formation a déjà commencé
        $sessionCommencee = \App\Models\SessionFormation::where('actif', true)
            ->where('date_debut', '<=', now())
            ->exists();
            
        if ($sessionCommencee) {
            return response()->json([
                'error' => 'Les inscriptions des apprenants sont fermées. Une session de formation a déjà commencé.',
                'details' => 'Vous ne pouvez plus vous inscrire en tant qu\'apprenant car une session est en cours. Contactez l\'administration pour plus d\'informations.'
            ], 403);
        }
    }
    
    // Permettre plusieurs assistants (suppression de la limitation)
    // if ($request->type_compte === 'assistant' && \App\Models\Utilisateur::where('type_compte', 'assistant')->exists()) {
    //     return response()->json(['error' => 'Un assistant existe déjà.'], 422);
    // }

    // Validation de base
    $rules = [
        'prenom' => 'required|string|max:255',
        'nom' => 'required|string|max:255',
        'email' => 'required|email|unique:utilisateurs,email',
        'password' => 'required|string|min:4|confirmed',
        'sexe' => 'required|in:Homme,Femme',
        'type_compte' => 'required|in:admin,assistant,formateur,apprenant',
        'telephone' => 'nullable|string|max:20',
    ];

    // Champs dynamiques selon le type de compte
    $extra = [];
    $cheminReligieux = null;
    $cheminGeneral = null;

    if ($request->type_compte === 'apprenant') {
        $rules = array_merge($rules, [
            'categorie' => 'required|in:Enfant,Etudiant,Professionnel',
        ]);
        $extra = [
            'connaissance_adis' => $request->connaissance_adis_apprenant,
            'formation_adis' => $request->formation_adis_apprenant,
            'formation_autre' => $request->formation_autre_apprenant,
            'niveau_coranique' => $request->niveau_coranique_apprenant,
            'niveau_arabe' => $request->niveau_arabe_apprenant,
            'tomes_medine' => $request->tomes_medine_apprenant,
            'tomes_etudies' => $request->tomes_etudies_apprenant ?? [],
            'disciplines' => $request->disciplines_apprenant ?? [],
            'attentes' => $request->attentes_apprenant ?? [],
            'formateur_domicile' => $request->formateur_domicile_apprenant,
            'categorie' => $request->categorie,
        ];
        $categorie = $request->categorie;
    } elseif ($request->type_compte === 'formateur') {
        $rules = array_merge($rules, [
            'categorie' => 'required|in:Enfant,Etudiant,Professionnel',
            'fichier_diplome_religieux' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'fichier_diplome_general' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);
        // Gestion upload fichiers (obligatoires pour les formateurs)
        if (!$request->hasFile('fichier_diplome_religieux')) {
            return response()->json(['error' => 'Le fichier de diplôme religieux est obligatoire pour les formateurs.'], 422);
        }
        if (!$request->hasFile('fichier_diplome_general')) {
            return response()->json(['error' => 'Le fichier de diplôme général est obligatoire pour les formateurs.'], 422);
        }
        
        $cheminReligieux = $request->file('fichier_diplome_religieux')->store('diplomes/religieux', 'public');
        $cheminGeneral = $request->file('fichier_diplome_general')->store('diplomes/general', 'public');
        $extra = [
            'connaissance_adis' => $request->connaissance_adis_formateur,
            'formation_adis' => $request->formation_adis_formateur,
            'formation_autre' => $request->formation_autre_formateur,
            'niveau_coranique' => $request->niveau_coranique_formateur,
            'niveau_arabe' => $request->niveau_arabe_formateur,
            'niveau_francais' => $request->niveau_francais_formateur,
            'diplome_religieux' => $request->diplome_religieux_formateur,
            'diplome_general' => $request->diplome_general_formateur,
            'categorie' => $request->categorie,
            'fichier_diplome_religieux' => $cheminReligieux,
            'fichier_diplome_general' => $cheminGeneral,
        ];
        $categorie = $request->categorie;
    } else {
        // Pour admin/assistant, catégorie non requise
        $categorie = null;
    }

    $data = $request->validate($rules);

    // Générer un token de vérification pour apprenant uniquement
    $verification_token = null;
    if ($request->type_compte === 'apprenant') {
        $verification_token = \Illuminate\Support\Str::random(48);
    }

    // Création de l'utilisateur
    $utilisateur = Utilisateur::create([
        'prenom' => $data['prenom'],
        'nom' => $data['nom'],
        'email' => $data['email'],
        'mot_de_passe' => bcrypt($data['password']),
        'type_compte' => $data['type_compte'],
        'sexe' => $data['sexe'],
        'categorie' => $categorie,
        'telephone' => $data['telephone'] ?? null,
        'actif' => true,
        'email_verified_at' => (in_array($request->type_compte, ['apprenant', 'formateur'])) ? null : now(),
        'infos_complementaires' => !empty($extra) ? json_encode($extra) : null,
        'verification_token' => $verification_token,
    ]);

    // Création automatique du formateur si type_compte = formateur
    if ($request->type_compte === 'formateur') {
        \App\Models\Formateur::create([
            'utilisateur_id' => $utilisateur->id,
            'valide' => false,
            'connaissance_adis' => $request->connaissance_adis_formateur,
            'formation_adis' => $request->formation_adis_formateur === 'Oui',
            'formation_autre' => $request->formation_autre_formateur === 'Oui',
            'niveau_coran' => $request->niveau_coranique_formateur,
            'niveau_arabe' => $request->niveau_arabe_formateur,
            'niveau_francais' => $request->niveau_francais_formateur,
            'diplome_religieux' => $request->diplome_religieux_formateur,
            'diplome_general' => $request->diplome_general_formateur,
            'fichier_diplome_religieux' => $cheminReligieux,
            'fichier_diplome_general' => $cheminGeneral,
            'ville' => $request->ville_formateur ?? null,
            'commune' => $request->commune_formateur ?? null,
            'quartier' => $request->quartier_formateur ?? null,
        ]);
        // Retourner un message spécifique pour les formateurs
        return response()->json([
            'message' => 'Inscription formateur réussie. Votre compte sera validé par un administrateur. Vous recevrez un email de validation une fois votre compte approuvé.',
            'type_compte' => 'formateur',
            'status' => 'en_attente_validation_admin'
        ], 201);
    }
    
    // Création automatique de l'apprenant si type_compte = apprenant
    if ($request->type_compte === 'apprenant') {
        \App\Models\Apprenant::create([
            'utilisateur_id' => $utilisateur->id,
            'niveau_id' => $request->niveau_id ?? null,
            'connaissance_adis' => $request->connaissance_adis_apprenant,
            'formation_adis' => $request->formation_adis_apprenant === 'Oui',
            'formation_autre' => $request->formation_autre_apprenant === 'Oui',
            'niveau_coran' => $request->niveau_coranique_apprenant,
            'niveau_arabe' => $request->niveau_arabe_apprenant,
            'connaissance_tomes_medine' => $request->tomes_medine_apprenant === 'Oui',
            'tomes_medine_etudies' => $request->tomes_etudies_apprenant, // Déjà JSON
            'disciplines_souhaitees' => $request->disciplines_apprenant, // Déjà JSON
            'attentes' => $request->attentes_apprenant, // Déjà JSON
            'formateur_domicile' => $request->formateur_domicile_apprenant === 'Oui',
        ]);
    }

    // Création automatique de l'assistant si type_compte = assistant
    if ($request->type_compte === 'assistant') {
        // L'assistant est directement un utilisateur avec type_compte = 'assistant'
        // Pas besoin de créer un modèle séparé
        return response()->json(['message' => 'Inscription assistant réussie.'], 201);
    }

    // Envoi de l'email de validation pour apprenant uniquement
    if ($request->type_compte === 'apprenant') {
        // URLs de vérification utilisant la configuration
        $baseUrl = config('app_urls.base_url');
        $mobileScheme = config('app_urls.mobile_scheme');
        $webPath = config('app_urls.web_verification_path');
        $mobilePath = config('app_urls.mobile_verification_path');
        
        $mobileVerificationUrl = "{$mobileScheme}://{$mobilePath}/{$verification_token}";
        $webVerificationUrl = "{$baseUrl}{$webPath}/{$verification_token}";
        
        Mail::send('emails.email_verification', [
            'prenom' => $utilisateur->prenom,
            'mobileVerificationUrl' => $mobileVerificationUrl,
            'webVerificationUrl' => $webVerificationUrl,
            'type_compte' => 'apprenant'
        ],
            function ($message) use ($utilisateur) {
                $message->to($utilisateur->email)
                        ->subject('Validation de votre inscription ADIS');
            }
        );
        $message = ($request->type_compte === 'apprenant') ? 'Inscription apprenant réussie. Vérifiez votre email.' : 'Inscription formateur réussie. Vérifiez votre email.';
        return response()->json(['message' => $message], 201);
    } else {
        // Pour admin/assistant : inscription directe
        if ($request->type_compte === 'admin' || $request->type_compte === 'assistant') {
            $message = 'Inscription ' . $request->type_compte . ' réussie. Vous pouvez vous connecter.';
            return response()->json(['message' => $message], 201);
        }
    }
}

    public function showRegisterForm()
    {
        $adminExists = \App\Models\Utilisateur::where('type_compte', 'admin')->exists();
        $assistantExists = \App\Models\Utilisateur::where('type_compte', 'assistant')->exists();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        return response()->json([
            'adminExists' => $adminExists,
            'assistantExists' => $assistantExists,
            'niveaux' => $niveaux
        ], 200);
    }

//    public function login(Request $request)
// {
//     $credentials = $request->validate([
//         'email' => 'required|email',
//         'password' => 'required|string',
//     ]);

//     // Authentification avec le guard JWT
//     if (!$token = auth('api')->attempt($credentials)) {
//         return response()->json(['error' => 'Identifiants invalides'], 401);
//     }

//     $utilisateur = auth('api')->user();

//     // Vérifier si le compte est actif
//     if (!$utilisateur->actif) {
//         auth('api')->logout();
//         return response()->json(['error' => 'Votre compte n\'est pas encore activé.'], 401);
//     }

//     // Vérifier si l'email est vérifié (uniquement pour apprenant et formateur)
//     if (in_array($utilisateur->type_compte, ['apprenant', 'formateur']) && !$utilisateur->email_verified_at) {
//         auth('api')->logout();
//         return response()->json(['error' => 'Votre email n\'est pas encore vérifié. Veuillez vérifier votre boîte mail et cliquer sur le lien de validation.'], 401);
//     }

//     return response()->json([
//         'message' => 'Connexion réussie',
//         'access_token' => $token,
//         'token_type' => 'Bearer',
//         'expires_in' => auth('api')->factory()->getTTL() * 60,
//         'user' => $utilisateur,
//         'type_compte' => $utilisateur->type_compte
//     ]);
// }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Authentification avec le guard JWT
    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json(['error' => 'Identifiants invalides'], 401);
    }

    $utilisateur = auth('api')->user();

    // Vérifier si le compte est actif
    if (!$utilisateur->actif) {
        auth('api')->logout();
        return response()->json(['error' => 'Votre compte n\'est pas encore activé.'], 401);
    }

    // Vérifier si l'email est vérifié (pour apprenant et formateur)
    if (in_array($utilisateur->type_compte, ['apprenant', 'formateur']) && !$utilisateur->email_verified_at) {
        auth('api')->logout();
        return response()->json(['error' => 'Votre email n\'est pas encore vérifié. Veuillez vérifier votre boîte mail et cliquer sur le lien de validation.'], 401);
    }

    // Vérifier que le formateur est validé par l'admin
    if ($utilisateur->type_compte === 'formateur') {
        $formateur = $utilisateur->formateur;
        if (!$formateur || !$formateur->valide) {
            auth('api')->logout();
            return response()->json(['error' => 'Votre compte formateur n\'est pas encore validé par l\'administration. Vous recevrez un email de validation une fois votre compte approuvé.'], 401);
        }
    }

    // Préparer la réponse de base
    $response = [
        'message' => 'Connexion réussie',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
        'user' => $utilisateur,
        'type_compte' => $utilisateur->type_compte,
    ];

    // Vérifier si l'utilisateur a des profils multiples
    $profilsMultiples = $this->detecterProfilsMultiples($utilisateur);
    
    if ($profilsMultiples) {
        $response['profils_multiples'] = $profilsMultiples;
        $response['message'] = 'Connexion réussie - Profils multiples détectés';
    }

    return response()->json($response);
}

/**
 * Détecter si l'utilisateur a des profils multiples
 */
private function detecterProfilsMultiples($utilisateur)
{
    $profils = [];

    // Vérifier le profil formateur
    if ($utilisateur->type_compte === 'formateur') {
        $formateur = $utilisateur->formateur;
        if ($formateur) {
            $profils['formateur'] = [
                'id' => $formateur->id,
                'valide' => $formateur->valide,
                'specialite' => $formateur->specialite,
                'connaissance_adis' => $formateur->connaissance_adis,
                'niveau_coran' => $formateur->niveau_coran,
                'niveau_arabe' => $formateur->niveau_francais,
                'diplome_religieux' => $formateur->diplome_religieux,
                'diplome_general' => $formateur->diplome_general,
                'ville' => $formateur->ville,
                'commune' => $formateur->commune,
                'quartier' => $formateur->quartier
            ];

            // Vérifier si ce formateur a aussi un profil apprenant
            $profilApprenant = \App\Models\Apprenant::where('utilisateur_id', $utilisateur->id)->first();
            if ($profilApprenant) {
                $profils['apprenant'] = [
                    'id' => $profilApprenant->id,
                    'niveau_id' => $profilApprenant->niveau_id,
                    'connaissance_adis' => $profilApprenant->connaissance_adis,
                    'formation_adis' => $profilApprenant->formation_adis,
                    'formation_autre' => $profilApprenant->formation_autre,
                    'niveau_coran' => $profilApprenant->niveau_coran,
                    'niveau_arabe' => $profilApprenant->niveau_arabe,
                    'connaissance_tomes_medine' => $profilApprenant->connaissance_tomes_medine,
                    'tomes_medine_etudies' => $profilApprenant->tomes_medine_etudies ? json_decode($profilApprenant->tomes_medine_etudies) : [],
                    'disciplines_souhaitees' => $profilApprenant->disciplines_souhaitees ? json_decode($profilApprenant->disciplines_souhaitees) : [],
                    'attentes' => $profilApprenant->attentes ? json_decode($profilApprenant->attentes) : [],
                    'formateur_domicile' => $profilApprenant->formateur_domicile
                ];

                // Charger les informations du niveau
                if ($profilApprenant->niveau) {
                    $profils['apprenant']['niveau'] = [
                        'id' => $profilApprenant->niveau->id,
                        'nom' => $profilApprenant->niveau->nom,
                        'description' => $profilApprenant->niveau->description,
                        'ordre' => $profilApprenant->niveau->ordre
                    ];
                }
            }

            // Vérifier si ce formateur a aussi un profil assistant
            $profilAssistant = \App\Models\Assistant::where('utilisateur_id', $utilisateur->id)->first();
            if ($profilAssistant) {
                $profils['assistant'] = [
                    'id' => $profilAssistant->id,
                    'bio' => $profilAssistant->bio,
                    'actif' => $profilAssistant->actif,
                    'date_creation' => $profilAssistant->created_at->format('Y-m-d H:i:s'),
                    'derniere_mise_a_jour' => $profilAssistant->updated_at->format('Y-m-d H:i:s'),
                    'permissions' => [
                        'peut_former' => true,
                        'peut_assister' => $profilAssistant->actif,
                        'peut_gerer_contenu' => true
                    ]
                ];
            }
        }
    }

    // Vérifier le profil apprenant
    if ($utilisateur->type_compte === 'apprenant') {
        $apprenant = $utilisateur->apprenant;
        if ($apprenant) {
            $profils['apprenant'] = [
                'id' => $apprenant->id,
                'niveau_id' => $apprenant->niveau_id,
                'connaissance_adis' => $apprenant->connaissance_adis,
                'formation_adis' => $apprenant->formation_adis,
                'formation_autre' => $apprenant->formation_autre,
                'niveau_coran' => $apprenant->niveau_coran,
                'niveau_arabe' => $apprenant->niveau_arabe,
                'connaissance_tomes_medine' => $apprenant->connaissance_tomes_medine,
                'tomes_medine_etudies' => $apprenant->tomes_medine_etudies ? json_decode($apprenant->tomes_medine_etudies) : [],
                'disciplines_souhaitees' => $apprenant->disciplines_souhaitees ? json_decode($apprenant->disciplines_souhaitees) : [],
                'attentes' => $apprenant->attentes ? json_decode($apprenant->attentes) : [],
                'formateur_domicile' => $apprenant->formateur_domicile
            ];

            // Charger les informations du niveau
            if ($apprenant->niveau) {
                $profils['apprenant']['niveau'] = [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                    'description' => $apprenant->niveau->description,
                    'ordre' => $apprenant->niveau->ordre
                ];
            }
        }
    }

    // Retourner les profils s'il y en a plusieurs
    if (count($profils) > 1) {
        return [
            'has_multiple_profiles' => true,
            'profils' => $profils,
            'contextes_disponibles' => array_keys($profils),
            'contexte_principal' => $utilisateur->type_compte
        ];
    }

    // Retourner null si pas de profils multiples
    return null;
}

public function profile(Request $request)
{
    // Récupère l'utilisateur connecté via le token JWT
    $user = Auth::guard('api')->user();

    if (!$user) {
        return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
    }

    // Charger les relations selon le type de compte
    switch ($user->type_compte) {
        case 'formateur':
            $user->load('formateur');
            break;
        case 'apprenant':
            $user->load('apprenant');
            break;
        case 'assistant':
            $user->load('assistant');
            break;
        case 'admin':
            // Pas de relation spécifique à charger
            break;
        default:
            return response()->json(['error' => 'Type de compte inconnu.'], 403);
    }

    return response()->json([
        'message' => 'Profil de l’utilisateur connecté',
        'user' => $user,
        'type_compte' => $user->type_compte
    ]);
}


    public function verifyEmail($token)
    {
        $utilisateur = Utilisateur::where('verification_token', $token)->first();
        if (!$utilisateur) {
            return response()->json(['error' => 'Lien de vérification invalide ou expiré.'], 404);
        }
        $utilisateur->email_verified_at = now();
        $utilisateur->verification_token = null;
        $utilisateur->actif = true;
        $utilisateur->save();
        Auth::login($utilisateur);
        return response()->json(['message' => 'Email vérifié et utilisateur connecté.', 'user' => $utilisateur], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Déconnexion réussie.'], 200);
    }

    // ===== MÉTHODES ADMIN =====
    
    /**
     * Vérifier si l'utilisateur connecté est admin
     */
    private function checkAdmin()
    {
        $user = auth('api')->user();
        
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json([
                'error' => 'Accès réservé aux administrateurs.',
                'debug' => [
                    'user_exists' => $user ? true : false,
                    'type_compte' => $user ? $user->type_compte : null,
                    'auth_guard' => 'api'
                ]
            ], 403);
        }
        return null;
    }

    /**
     * Afficher tous les apprenants (admin uniquement)
     */
    public function getApprenants()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        $apprenants = \App\Models\Apprenant::with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Liste des apprenants récupérée avec succès',
            'apprenants' => $apprenants,
            'total' => $apprenants->total()
        ], 200);
    }

    /**
     * Afficher tous les formateurs (admin uniquement)
     */
    public function getFormateurs()
    {
        // Vérifier si l'utilisateur est admin ou assistant
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }
        
        if ($user->type_compte !== 'admin' && $user->type_compte !== 'assistant') {
            return response()->json(['error' => 'Accès réservé aux administrateurs et assistants.'], 403);
        }

        $formateurs = \App\Models\Formateur::with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Liste des formateurs récupérée avec succès',
            'formateurs' => $formateurs,
            'total' => $formateurs->total()
        ], 200);
    }

    /**
     * Afficher tous les assistants (admin uniquement)
     */
    public function getAssistants()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        $assistants = \App\Models\Utilisateur::where('type_compte', 'assistant')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Liste des assistants récupérée avec succès',
            'assistants' => $assistants,
            'total' => $assistants->total()
        ], 200);
    }

    /**
     * Supprimer un apprenant (admin uniquement)
     */
    public function deleteApprenant($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $apprenant = \App\Models\Apprenant::findOrFail($id);
            $utilisateur = $apprenant->utilisateur;
            
            // Supprimer d'abord l'apprenant, puis l'utilisateur
            $apprenant->delete();
            $utilisateur->delete();

            return response()->json([
                'success' => true,
                'message' => 'Apprenant supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un formateur (admin uniquement)
     */
    public function deleteFormateur($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $formateur = \App\Models\Formateur::findOrFail($id);
            $utilisateur = $formateur->utilisateur;
            
            // Supprimer d'abord le formateur, puis l'utilisateur
            $formateur->delete();
            $utilisateur->delete();

            return response()->json([
                'success' => true,
                'message' => 'Formateur supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver un apprenant (admin uniquement)
     */
    public function desactiverApprenant($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $apprenant = \App\Models\Apprenant::findOrFail($id);
            $apprenant->utilisateur->update(['actif' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Apprenant désactivé avec succès',
                'apprenant' => [
                    'id' => $apprenant->id,
                    'utilisateur' => [
                        'id' => $apprenant->utilisateur->id,
                        'nom' => $apprenant->utilisateur->nom,
                        'prenom' => $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'actif' => false
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la désactivation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver un formateur (admin uniquement)
     */
    public function desactiverFormateur($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $formateur = \App\Models\Formateur::findOrFail($id);
            $formateur->utilisateur->update(['actif' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Formateur désactivé avec succès',
                'formateur' => [
                    'id' => $formateur->id,
                    'utilisateur' => [
                        'id' => $formateur->utilisateur->id,
                        'nom' => $formateur->utilisateur->nom,
                        'prenom' => $formateur->utilisateur->prenom,
                        'email' => $formateur->utilisateur->email,
                        'actif' => false
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la désactivation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Réactiver un utilisateur (admin uniquement)
     */
    public function reactiverUtilisateur($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $utilisateur = \App\Models\Utilisateur::findOrFail($id);
            $utilisateur->update(['actif' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur réactivé avec succès',
                'utilisateur' => [
                    'id' => $utilisateur->id,
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->nom,
                    'email' => $utilisateur->email,
                    'type_compte' => $utilisateur->type_compte,
                    'actif' => true
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la réactivation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver/Réactiver un utilisateur (admin uniquement)
     * Méthode générique pour tous les types d'utilisateurs
     */
    public function toggleUtilisateur($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $utilisateur = \App\Models\Utilisateur::findOrFail($id);
            
            // Vérifier que l'admin ne peut pas se désactiver lui-même
            if ($utilisateur->id === auth('api')->id() && $utilisateur->type_compte === 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Un administrateur ne peut pas se désactiver lui-même'
                ], 400);
            }

            // Inverser le statut actif
            $nouveauStatut = !$utilisateur->actif;
            $utilisateur->update(['actif' => $nouveauStatut]);

            $action = $nouveauStatut ? 'réactivé' : 'désactivé';
            $message = "Utilisateur {$action} avec succès";

            // Récupérer les informations complètes selon le type de compte
            $utilisateurInfo = [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_compte' => $utilisateur->type_compte,
                'actif' => $nouveauStatut,
                'telephone' => $utilisateur->telephone,
                'sexe' => $utilisateur->sexe,
                'categorie' => $utilisateur->categorie,
                'created_at' => $utilisateur->created_at,
                'updated_at' => $utilisateur->updated_at
            ];

            // Ajouter des informations spécifiques selon le type de compte
            switch ($utilisateur->type_compte) {
                case 'apprenant':
                    if ($utilisateur->apprenant) {
                        $utilisateurInfo['apprenant'] = [
                            'id' => $utilisateur->apprenant->id,
                            'niveau_id' => $utilisateur->apprenant->niveau_id,
                            'niveau_coran' => $utilisateur->apprenant->niveau_coran,
                            'niveau_arabe' => $utilisateur->apprenant->niveau_arabe
                        ];
                    }
                    break;
                    
                case 'formateur':
                    if ($utilisateur->formateur) {
                        $utilisateurInfo['formateur'] = [
                            'id' => $utilisateur->formateur->id,
                            'valide' => $utilisateur->formateur->valide,
                            'specialite' => $utilisateur->formateur->specialite,
                            'niveau_coran' => $utilisateur->formateur->niveau_coran,
                            'niveau_arabe' => $utilisateur->formateur->niveau_arabe
                        ];
                    }
                    break;
                    
                case 'assistant':
                    // Les assistants n'ont pas de modèle séparé
                    break;
                    
                case 'admin':
                    // Les admins n'ont pas de modèle séparé
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'action' => $nouveauStatut ? 'reactivation' : 'desactivation',
                'utilisateur' => $utilisateurInfo,
                'statut_precedent' => !$nouveauStatut,
                'statut_actuel' => $nouveauStatut
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la modification du statut: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir le statut actuel d'un utilisateur (admin uniquement)
     */
    public function getStatutUtilisateur($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $utilisateur = \App\Models\Utilisateur::with(['apprenant', 'formateur'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'utilisateur' => [
                    'id' => $utilisateur->id,
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                    'email' => $utilisateur->email,
                    'type_compte' => $utilisateur->type_compte,
                    'actif' => $utilisateur->actif,
                    'statut' => $utilisateur->actif ? 'Actif' : 'Inactif',
                    'derniere_modification' => $utilisateur->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du statut: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier le mot de passe de l'utilisateur connecté (tous types de comptes)
     */
    public function changePassword(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est connecté
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Non authentifié'
                ], 401);
            }

            // Validation des données
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ], [
                'current_password.required' => 'Le mot de passe actuel est requis',
                'new_password.required' => 'Le nouveau mot de passe est requis',
                'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 6 caractères',
                'new_password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas'
            ]);

            // Vérifier que l'ancien mot de passe est correct
            if (!Hash::check($request->current_password, $user->mot_de_passe)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Le mot de passe actuel est incorrect'
                ], 422);
            }

            // Vérifier que le nouveau mot de passe est différent de l'ancien
            if (Hash::check($request->new_password, $user->mot_de_passe)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Le nouveau mot de passe doit être différent de l\'ancien'
                ], 422);
            }

            // Mettre à jour le mot de passe
            $user->mot_de_passe = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès',
                'user' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'type_compte' => $user->type_compte,
                    'derniere_modification' => $user->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la modification du mot de passe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier le profil de l'utilisateur connecté (tous types de comptes)
     */
    public function updateProfile(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est connecté
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Non authentifié'
                ], 401);
            }

            // Validation des données communes à tous les utilisateurs
            $utilisateurData = $request->validate([
                'nom' => 'nullable|string|max:255',
                'prenom' => 'nullable|string|max:255',
                'telephone' => 'nullable|string|max:20',
                'sexe' => 'nullable|in:Homme,Femme',
                'categorie' => 'nullable|string|max:100',
            ], [
                'nom.max' => 'Le nom ne peut pas dépasser 255 caractères',
                'prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères',
                'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
                'sexe.in' => 'Le sexe doit être Homme ou Femme',
                'categorie.max' => 'La catégorie ne peut pas dépasser 100 caractères'
            ]);

            // Sauvegarder les anciennes valeurs pour comparaison
            $anciennesValeurs = [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'telephone' => $user->telephone,
                'sexe' => $user->sexe,
                'categorie' => $user->categorie
            ];

            // Filtrer les champs vides
            $utilisateurData = array_filter($utilisateurData, function($value) {
                return $value !== null && $value !== '';
            });

            // Mettre à jour l'utilisateur si des données sont fournies
            if (!empty($utilisateurData)) {
                $user->update($utilisateurData);
            }

            // Charger les relations selon le type de compte pour la réponse
            switch ($user->type_compte) {
                case 'formateur':
                    $user->load('formateur');
                    break;
                case 'apprenant':
                    $user->load('apprenant');
                    break;
                case 'assistant':
                    $user->load('assistant');
                    break;
                case 'admin':
                    // Pas de relation spécifique à charger
                    break;
            }

            // Préparer la réponse avec les informations mises à jour
            $response = [
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'user' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'telephone' => $user->telephone,
                    'sexe' => $user->sexe,
                    'categorie' => $user->categorie,
                    'type_compte' => $user->type_compte,
                    'actif' => $user->actif,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ],
                'modifications' => [
                    'nom' => $anciennesValeurs['nom'] !== $user->nom,
                    'prenom' => $anciennesValeurs['prenom'] !== $user->prenom,
                    'telephone' => $anciennesValeurs['telephone'] !== $user->telephone,
                    'sexe' => $anciennesValeurs['sexe'] !== $user->sexe,
                    'categorie' => $anciennesValeurs['categorie'] !== $user->categorie
                ]
            ];

            // Ajouter les informations spécifiques selon le type de compte
            switch ($user->type_compte) {
                case 'formateur':
                    if ($user->formateur) {
                        $response['formateur'] = [
                            'id' => $user->formateur->id,
                            'specialite' => $user->formateur->specialite,
                            'valide' => $user->formateur->valide,
                            'niveau_coran' => $user->formateur->niveau_coran,
                            'niveau_arabe' => $user->formateur->niveau_arabe,
                            'niveau_francais' => $user->formateur->niveau_francais,
                            'ville' => $user->formateur->ville,
                            'commune' => $user->formateur->commune,
                            'quartier' => $user->formateur->quartier
                        ];
                    }
                    break;
                    
                case 'apprenant':
                    if ($user->apprenant) {
                        $response['apprenant'] = [
                            'id' => $user->apprenant->id,
                            'niveau_id' => $user->apprenant->niveau_id,
                            'niveau_coranique' => $user->apprenant->niveau_coranique,
                            'niveau_arabe' => $user->apprenant->niveau_arabe,
                            'connaissance_adis' => $user->apprenant->connaissance_adis,
                            'formation_adis' => $user->apprenant->formation_adis,
                            'formation_autre' => $user->apprenant->formation_autre
                        ];
                    }
                    break;
                    
                case 'assistant':
                    if ($user->assistant) {
                        $response['assistant'] = [
                            'id' => $user->assistant->id,
                            'bio' => $user->assistant->bio,
                            'actif' => $user->assistant->actif,
                            'formateur_id' => $user->assistant->formateur_id
                        ];
                    }
                    break;
            }

            return response()->json($response, 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour du profil: ' . $e->getMessage()
            ], 500);
        }
    }
} 
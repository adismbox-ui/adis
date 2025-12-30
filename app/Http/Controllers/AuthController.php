<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $current = Auth::user();
        $allowedTypes = null;
        if ($current && $current->type_compte === 'admin') {
            $allowedTypes = ['formateur', 'apprenant'];
        } elseif ($current && $current->type_compte === 'formateur') {
            // Un formateur connecté ne peut pas créer d'apprenant (réservé à l'admin)
            $allowedTypes = ['assistant'];
        }

        if ($allowedTypes && !in_array($request->type_compte, $allowedTypes, true)) {
            return back()->withErrors(['type_compte' => 'Type de compte non autorisé pour votre profil.'])->withInput();
        }

        // Bloquer l'inscription apprenant si l'email appartient déjà à un formateur existant
        if ($request->type_compte === 'apprenant' && !empty($request->email)) {
            $existing = \App\Models\Utilisateur::where('email', $request->email)->first();
            if ($existing && $existing->formateur) {
                return back()->withErrors(['email' => 'Cet email est déjà utilisé par un formateur. Demandez à l\'administrateur de vous activer en tant qu\'apprenant.'])->withInput();
            }
        }
        // Vérifier unicité admin/assistant
        if ($request->type_compte === 'admin' && \App\Models\Utilisateur::where('type_compte', 'admin')->exists()) {
            return back()->withErrors(['type_compte' => 'Un administrateur existe déjà.'])->withInput();
        }
        if ($request->type_compte === 'assistant' && \App\Models\Utilisateur::where('type_compte', 'assistant')->exists()) {
            return back()->withErrors(['type_compte' => 'Un assistant existe déjà.'])->withInput();
        }

        // Validation de base
        $rules = [
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6|confirmed',
            'sexe' => 'required|in:Homme,Femme',
            'type_compte' => 'required|in:admin,assistant,formateur,apprenant',
            'telephone' => 'nullable|string|max:20',
        ];

        // Champs dynamiques selon le type de compte
        $extra = [];
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
            ]);
            // Gérer les diplômes "AUTRE" : utiliser le champ texte si "AUTRE" est sélectionné
            $diplomeReligieux = $request->diplome_religieux_formateur;
            if ($diplomeReligieux === 'AUTRE' && $request->diplome_religieux_autre_formateur) {
                $diplomeReligieux = $request->diplome_religieux_autre_formateur;
            }
            
            $diplomeGeneral = $request->diplome_general_formateur;
            if ($diplomeGeneral === 'AUTRE' && $request->diplome_general_autre_formateur) {
                $diplomeGeneral = $request->diplome_general_autre_formateur;
            }
            
            $extra = [
                'disciplines' => $request->disciplines_formateur ?? [],
                'niveau_arabe' => $request->niveau_arabe_formateur,
                'niveau_francais' => $request->niveau_francais_formateur,
                'deja_enseigne_adis' => $request->deja_enseigne_adis_formateur,
                'enseignement_domicile' => $request->enseignement_domicile_formateur,
                'diplome_religieux' => $diplomeReligieux,
                'diplome_general' => $diplomeGeneral,
                'categorie' => $request->categorie,
            ];
            $categorie = $request->categorie;
        } else {
            // Pour admin/assistant, catégorie non requise
            $categorie = null;
        }

        $data = $request->validate($rules);

        // Générer un token de vérification uniquement pour apprenant
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
            // Pour les apprenants et formateurs, le compte reste inactif jusqu'à activation (email pour apprenant, admin pour formateur)
            'actif' => in_array($request->type_compte, ['apprenant', 'formateur'], true) ? false : true,
            // Les formateurs n'ont pas besoin de vérification email, mais restent inactifs jusqu'à validation admin
            'email_verified_at' => ($request->type_compte === 'apprenant') ? null : now(),
            'infos_complementaires' => !empty($extra) ? json_encode($extra) : null,
            'verification_token' => $verification_token,
        ]);

        // Création automatique du formateur si type_compte = formateur
        if ($request->type_compte === 'formateur') {
            \App\Models\Formateur::create([
                'utilisateur_id' => $utilisateur->id,
                'valide' => false,
                // Ajoute ici d'autres champs si besoin
            ]);
            // Rediriger vers une page de confirmation spéciale formateur
            return redirect()->route('register.formateur_confirmation');
        }
        // Création automatique de l'apprenant si type_compte = apprenant
        if ($request->type_compte === 'apprenant') {
            \App\Models\Apprenant::create([
                'utilisateur_id' => $utilisateur->id,
                'niveau_id' => $request->niveau_id ?? null
            ]);
        }

        // Envoi de l'email de validation uniquement pour apprenant
        if ($request->type_compte === 'apprenant') {
            $verificationUrl = url('/verify-email/' . $verification_token);
            \Mail::raw(
                "Bonjour " . $utilisateur->prenom . ",\n\nMerci de vous être inscrit sur ADIS.\nVeuillez cliquer sur le lien ci-dessous pour valider votre adresse email et activer votre compte :\n\n" . $verificationUrl . "\n\nSi vous n'êtes pas à l'origine de cette inscription, ignorez ce message.\n\nL'équipe ADIS.",
                function (
                    $message
                ) use ($utilisateur) {
                    $message->to($utilisateur->email)
                            ->subject('Validation de votre inscription ADIS');
                }
            );
            return redirect()->route('register.confirmation');
        } else {
            // Pour admin/assistant/formateur : inscription directe
            return redirect()->route('login')->with('success', 'Inscription réussie ! Vous pouvez vous connecter.');
        }
    }

    public function showRegisterForm()
    {
        $adminExists = \App\Models\Utilisateur::where('type_compte', 'admin')->exists();
        $assistantExists = \App\Models\Utilisateur::where('type_compte', 'assistant')->exists();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $current = Auth::user();
        $allowedTypes = null;
        if ($current && $current->type_compte === 'admin') {
            $allowedTypes = ['formateur', 'apprenant'];
        } elseif ($current && $current->type_compte === 'formateur') {
            $allowedTypes = ['assistant', 'apprenant'];
        }
        return view('auth.register', compact('adminExists', 'assistantExists', 'niveaux', 'allowedTypes'));
    }

    public function login(Request $request)
    {
        // Validation d'abord
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Nettoyer uniquement l'email (pas le mot de passe, car il peut contenir des espaces intentionnels)
        $email = trim($validated['email']);
        $password = $validated['password']; // Ne pas trimmer le mot de passe

        // Authentification manuelle car le mot de passe est dans 'mot_de_passe'
        $utilisateur = \App\Models\Utilisateur::where('email', $email)->first();
        
        // Log pour débogage
        \Log::info('Web Login attempt', [
            'email' => $email,
            'password_length' => strlen($password),
            'user_found' => $utilisateur ? true : false,
            'user_id' => $utilisateur?->id,
            'user_type' => $utilisateur?->type_compte,
            'user_active' => $utilisateur?->actif,
        ]);
        
        if (!$utilisateur) {
            \Log::warning('Web Login: User not found', ['email' => $email]);
            return back()->withErrors(['email' => 'Identifiants invalides'])->withInput();
        }
        
        // Vérifier le mot de passe
        $passwordCheck = \Hash::check($password, $utilisateur->mot_de_passe);
        
        \Log::info('Web Login: Password check', [
            'user_id' => $utilisateur->id,
            'password_check' => $passwordCheck,
            'password_hash_starts_with' => substr($utilisateur->mot_de_passe, 0, 7),
        ]);
        
        if ($passwordCheck) {
            if (!$utilisateur->actif) {
                \Log::warning('Web Login: Account disabled', ['user_id' => $utilisateur->id]);
                return back()->withErrors(['email' => 'Votre compte est désactivé.'])->withInput();
            }
            
            // Pour admin et assistant, pas de vérification d'email nécessaire
            // Bloquer uniquement les apprenants non vérifiés par email
            if ($utilisateur->type_compte === 'apprenant' && empty($utilisateur->email_verified_at)) {
                \Log::warning('Web Login: Apprenant email not verified', ['user_id' => $utilisateur->id]);
                return back()->withErrors(['email' => 'Veuillez vérifier votre adresse email pour activer votre compte.'])->withInput();
            }
            
            // Connexion réussie
            \Auth::login($utilisateur);
            
            \Log::info('Web Login: Success', [
                'user_id' => $utilisateur->id,
                'type_compte' => $utilisateur->type_compte,
            ]);
            
            switch ($utilisateur->type_compte) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'assistant':
                    return redirect('/assistant/dashboard');
                case 'formateur':
                    // Si formateur non validé par admin, bloquer
                    if ($utilisateur->formateur && isset($utilisateur->formateur->valide) && !$utilisateur->formateur->valide) {
                        \Auth::logout();
                        \Log::warning('Web Login: Formateur not validated', ['user_id' => $utilisateur->id]);
                        return back()->withErrors(['email' => "Votre compte formateur n'a pas encore été validé par l'administrateur."])->withInput();
                    }
                    return redirect('/formateurs/dashboard');
                case 'apprenant':
                    return redirect('/apprenants/dashboard');
                default:
                    return redirect('/');
            }
        }
        
        // Mot de passe incorrect
        \Log::warning('Web Login: Invalid password', [
            'user_id' => $utilisateur->id,
            'email' => $email,
        ]);
        return back()->withErrors(['email' => 'Identifiants invalides'])->withInput();
    }

    public function verifyEmail($token)
    {
        $utilisateur = \App\Models\Utilisateur::where('verification_token', $token)->first();
        if (!$utilisateur) {
            return redirect()->route('login')->withErrors(['email' => 'Lien de vérification invalide ou expiré.']);
        }
        $utilisateur->email_verified_at = now();
        $utilisateur->verification_token = null;
        $utilisateur->actif = true;
        $utilisateur->save();
        \Auth::login($utilisateur);
        // Après vérification, rediriger vers la page d'accueil
        return redirect('/')->with('success', 'Votre email a été vérifié.');
    }

    public function logout(Request $request)
    {
        \Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
} 
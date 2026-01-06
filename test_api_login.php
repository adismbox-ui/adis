<?php

/**
 * Script de test pour vérifier l'authentification API
 * Usage: php test_api_login.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

echo "=== Test d'authentification API ===\n\n";

// Email et mot de passe à tester
$email = 'adis.mbox@gmail.com';
$password = 'adis2025'; // Essayez aussi 'adls2025' si nécessaire

echo "Email: $email\n";
echo "Password: $password\n\n";

// Rechercher l'utilisateur
$utilisateur = Utilisateur::where('email', $email)->first();

if (!$utilisateur) {
    echo "❌ ERREUR: Utilisateur non trouvé dans la base de données\n";
    exit(1);
}

echo "✅ Utilisateur trouvé:\n";
echo "   ID: {$utilisateur->id}\n";
echo "   Nom: {$utilisateur->nom}\n";
echo "   Prénom: {$utilisateur->prenom}\n";
echo "   Type compte: {$utilisateur->type_compte}\n";
echo "   Actif: " . ($utilisateur->actif ? 'Oui' : 'Non') . "\n";
echo "   Email vérifié: " . ($utilisateur->email_verified_at ? 'Oui' : 'Non') . "\n";
echo "   Hash mot de passe: " . substr($utilisateur->mot_de_passe, 0, 20) . "...\n";
echo "   Longueur hash: " . strlen($utilisateur->mot_de_passe) . " caractères\n\n";

// Vérifier le format du hash
if (str_starts_with($utilisateur->mot_de_passe, '$2y$') || str_starts_with($utilisateur->mot_de_passe, '$2a$')) {
    echo "✅ Le mot de passe est correctement hashé (bcrypt)\n\n";
} else {
    echo "❌ ATTENTION: Le mot de passe ne semble pas être hashé avec bcrypt!\n";
    echo "   Format actuel: " . substr($utilisateur->mot_de_passe, 0, 20) . "...\n\n";
}

// Tester le mot de passe
echo "Test du mot de passe:\n";
$passwordCheck = Hash::check($password, $utilisateur->mot_de_passe);
echo "   Hash::check('$password', hash): " . ($passwordCheck ? '✅ VALIDE' : '❌ INVALIDE') . "\n";

// Tester avec trim
$passwordTrimmed = trim($password);
if ($passwordTrimmed !== $password) {
    $passwordCheckTrimmed = Hash::check($passwordTrimmed, $utilisateur->mot_de_passe);
    echo "   Hash::check(trim('$password'), hash): " . ($passwordCheckTrimmed ? '✅ VALIDE' : '❌ INVALIDE') . "\n";
}

// Vérifier les conditions de connexion
echo "\nVérification des conditions de connexion:\n";

if (!$utilisateur->actif) {
    echo "   ❌ Compte désactivé (actif = 0)\n";
} else {
    echo "   ✅ Compte actif\n";
}

if ($utilisateur->type_compte === 'apprenant' && empty($utilisateur->email_verified_at)) {
    echo "   ❌ Email non vérifié (apprenant)\n";
} else {
    echo "   ✅ Email vérifié ou type de compte différent\n";
}

if ($utilisateur->type_compte === 'formateur') {
    $formateur = $utilisateur->formateur;
    if ($formateur && isset($formateur->valide) && !$formateur->valide) {
        echo "   ❌ Formateur non validé par admin\n";
    } else {
        echo "   ✅ Formateur validé ou pas de profil formateur\n";
    }
}

// Résultat final
echo "\n=== RÉSULTAT FINAL ===\n";
if ($passwordCheck && $utilisateur->actif) {
    if ($utilisateur->type_compte === 'apprenant' && empty($utilisateur->email_verified_at)) {
        echo "❌ Connexion bloquée: Email non vérifié\n";
    } elseif ($utilisateur->type_compte === 'formateur' && $utilisateur->formateur && isset($utilisateur->formateur->valide) && !$utilisateur->formateur->valide) {
        echo "❌ Connexion bloquée: Formateur non validé\n";
    } else {
        echo "✅ Connexion devrait fonctionner!\n";
        echo "\nSi l'API retourne toujours une erreur, vérifiez:\n";
        echo "1. Les logs Laravel: storage/logs/laravel.log\n";
        echo "2. Que Sanctum est bien installé: composer show laravel/sanctum\n";
        echo "3. Que les migrations sont exécutées: php artisan migrate\n";
    }
} else {
    echo "❌ Connexion échouera: ";
    if (!$passwordCheck) {
        echo "Mot de passe incorrect";
    }
    if (!$utilisateur->actif) {
        echo " ou compte désactivé";
    }
    echo "\n";
    
    echo "\nSolutions:\n";
    if (!$passwordCheck) {
        echo "1. Réinitialiser le mot de passe:\n";
        echo "   php artisan tinker\n";
        echo "   >>> \$user = \\App\\Models\\Utilisateur::where('email', '$email')->first();\n";
        echo "   >>> \$user->mot_de_passe = \\Hash::make('$password');\n";
        echo "   >>> \$user->save();\n";
    }
    if (!$utilisateur->actif) {
        echo "2. Activer le compte:\n";
        echo "   UPDATE utilisateurs SET actif = 1 WHERE email = '$email';\n";
    }
}

echo "\n";









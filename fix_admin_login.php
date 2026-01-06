<?php

/**
 * Script pour vérifier et corriger le compte admin
 * Usage: php fix_admin_login.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

echo "=== Vérification et Correction du Compte Admin ===\n\n";

// Rechercher tous les comptes admin
$admins = Utilisateur::where('type_compte', 'admin')->get();

if ($admins->isEmpty()) {
    echo "❌ Aucun compte admin trouvé!\n";
    echo "\nVoulez-vous créer un compte admin? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) === 'y') {
        echo "\nEmail: ";
        $email = trim(fgets(STDIN));
        echo "Mot de passe: ";
        $password = trim(fgets(STDIN));
        echo "Prénom: ";
        $prenom = trim(fgets(STDIN));
        echo "Nom: ";
        $nom = trim(fgets(STDIN));
        
        $admin = Utilisateur::create([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe' => Hash::make($password),
            'type_compte' => 'admin',
            'sexe' => 'Homme',
            'actif' => true,
            'email_verified_at' => now(),
        ]);
        
        echo "\n✅ Compte admin créé avec succès!\n";
        echo "   ID: {$admin->id}\n";
        echo "   Email: {$admin->email}\n";
    }
    exit(0);
}

echo "Comptes admin trouvés: " . $admins->count() . "\n\n";

foreach ($admins as $admin) {
    echo "=== Compte Admin #{$admin->id} ===\n";
    echo "Email: {$admin->email}\n";
    echo "Nom: {$admin->prenom} {$admin->nom}\n";
    echo "Actif: " . ($admin->actif ? '✅ Oui' : '❌ Non') . "\n";
    echo "Email vérifié: " . ($admin->email_verified_at ? '✅ Oui' : '❌ Non') . "\n";
    echo "Hash mot de passe: " . substr($admin->mot_de_passe, 0, 20) . "...\n";
    echo "Longueur hash: " . strlen($admin->mot_de_passe) . " caractères\n";
    
    // Vérifier le format du hash
    if (str_starts_with($admin->mot_de_passe, '$2y$') || str_starts_with($admin->mot_de_passe, '$2a$')) {
        echo "Format hash: ✅ Correct (bcrypt)\n";
    } else {
        echo "Format hash: ❌ Incorrect!\n";
    }
    
    // Proposer de corriger
    if (!$admin->actif) {
        echo "\n⚠️  Le compte est désactivé!\n";
        echo "Voulez-vous l'activer? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) === 'y') {
            $admin->actif = true;
            $admin->save();
            echo "✅ Compte activé!\n";
        }
    }
    
    if (!$admin->email_verified_at) {
        echo "\n⚠️  L'email n'est pas vérifié!\n";
        echo "Voulez-vous le vérifier? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) === 'y') {
            $admin->email_verified_at = now();
            $admin->save();
            echo "✅ Email vérifié!\n";
        }
    }
    
    echo "\nVoulez-vous réinitialiser le mot de passe? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) === 'y') {
        echo "Nouveau mot de passe: ";
        $newPassword = trim(fgets(STDIN));
        $admin->mot_de_passe = Hash::make($newPassword);
        $admin->save();
        echo "✅ Mot de passe réinitialisé!\n";
    }
    
    echo "\n";
}

echo "=== Test de Connexion ===\n";
echo "Pour tester la connexion, utilisez:\n";
echo "1. Allez sur: https://adis-ci.net/login\n";
echo "2. Entrez l'email et le mot de passe\n";
echo "3. Vérifiez les logs: storage/logs/laravel.log\n";
echo "\n";









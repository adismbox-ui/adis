<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Utilisateur;
use App\Models\Formateur;

// Vérifier si un admin existe déjà
$adminExists = Utilisateur::where('type_compte', 'admin')->exists();

if ($adminExists) {
    echo "Un administrateur existe déjà.\n";
    $admin = Utilisateur::where('type_compte', 'admin')->first();
    echo "Email: " . $admin->email . "\n";
    echo "Nom: " . $admin->nom . " " . $admin->prenom . "\n";
} else {
    // Créer un utilisateur admin
    $admin = Utilisateur::create([
        'prenom' => 'Admin',
        'nom' => 'ADIS',
        'email' => 'admin@adis.com',
        'mot_de_passe' => bcrypt('password'),
        'type_compte' => 'admin',
        'sexe' => 'Homme',
        'actif' => true,
        'email_verified_at' => now(),
    ]);
    
    echo "Administrateur créé avec succès!\n";
    echo "Email: admin@adis.com\n";
    echo "Mot de passe: password\n";
}

// Vérifier si un assistant existe déjà
$assistantExists = Utilisateur::where('type_compte', 'assistant')->exists();

if ($assistantExists) {
    echo "Un assistant existe déjà.\n";
} else {
    // Créer un utilisateur assistant
    $assistant = Utilisateur::create([
        'prenom' => 'Assistant',
        'nom' => 'ADIS',
        'email' => 'assistant@adis.com',
        'mot_de_passe' => bcrypt('password'),
        'type_compte' => 'assistant',
        'sexe' => 'Homme',
        'actif' => true,
        'email_verified_at' => now(),
    ]);
    
    echo "Assistant créé avec succès!\n";
    echo "Email: assistant@adis.com\n";
    echo "Mot de passe: password\n";
}

// Script de migration des utilisateurs de type formateur vers la table formateurs
// Récupérer tous les utilisateurs de type formateur
$utilisateursFormateurs = Utilisateur::where('type_compte', 'formateur')->get();

foreach ($utilisateursFormateurs as $utilisateur) {
    Formateur::firstOrCreate([
        'utilisateur_id' => $utilisateur->id
    ]);
}
echo "Migration des formateurs terminée.\n";

echo "\nScript terminé.\n"; 
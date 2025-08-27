<?php

use App\Models\Utilisateur;
use App\Models\Formateur;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Boot Laravel (si nécessaire)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = Utilisateur::where('type_compte', 'formateur')->get();

foreach ($users as $user) {
    // Vérifie si le formateur existe déjà
    if (!Formateur::where('utilisateur_id', $user->id)->exists()) {
        Formateur::create([
            'utilisateur_id' => $user->id,
            'valide' => false,
            // Ajoute d'autres champs si besoin (ex: specialite, connaissance_adis, etc.)
        ]);
        echo "Formateur ajouté : {$user->nom} {$user->prenom}\n";
    } else {
        echo "Déjà présent : {$user->nom} {$user->prenom}\n";
    }
}

echo "\nSynchronisation terminée.\n"; 
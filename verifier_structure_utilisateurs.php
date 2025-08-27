<?php

require_once 'vendor/autoload.php';

use App\Models\Utilisateur;
use App\Models\Apprenant;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VÉRIFICATION STRUCTURE UTILISATEURS ===\n\n";

// 1. Vérifier la structure de la table utilisateurs
echo "1. Structure de la table utilisateurs :\n";
try {
    $result = \DB::select("DESCRIBE utilisateurs");
    foreach ($result as $column) {
        echo "  - {$column->Field} : {$column->Type}\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de la vérification de la structure : " . $e->getMessage() . "\n";
}

// 2. Vérifier les utilisateurs existants
echo "\n2. Utilisateurs existants :\n";
try {
    $utilisateurs = Utilisateur::limit(5)->get();
    foreach ($utilisateurs as $utilisateur) {
        echo "  - ID {$utilisateur->id} : ";
        // Essayer différentes colonnes possibles
        if (isset($utilisateur->nom)) {
            echo "nom = '{$utilisateur->nom}'";
        } elseif (isset($utilisateur->name)) {
            echo "name = '{$utilisateur->name}'";
        } elseif (isset($utilisateur->email)) {
            echo "email = '{$utilisateur->email}'";
        } else {
            echo "données : " . json_encode($utilisateur->toArray());
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de la récupération des utilisateurs : " . $e->getMessage() . "\n";
}

// 3. Vérifier les apprenants
echo "\n3. Apprenants existants :\n";
try {
    $apprenants = Apprenant::with(['utilisateur'])->limit(5)->get();
    foreach ($apprenants as $apprenant) {
        echo "  - ID {$apprenant->id} : ";
        if ($apprenant->utilisateur) {
            if (isset($apprenant->utilisateur->nom)) {
                echo "utilisateur = '{$apprenant->utilisateur->nom}'";
            } elseif (isset($apprenant->utilisateur->name)) {
                echo "utilisateur = '{$apprenant->utilisateur->name}'";
            } else {
                echo "utilisateur ID = {$apprenant->utilisateur->id}";
            }
        } else {
            echo "pas d'utilisateur associé";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de la récupération des apprenants : " . $e->getMessage() . "\n";
}

echo "\n=== VÉRIFICATION TERMINÉE ===\n"; 
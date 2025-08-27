<?php

require_once 'vendor/autoload.php';

use App\Models\Inscription;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VÉRIFICATION STRUCTURE INSCRIPTIONS ===\n\n";

// 1. Vérifier les valeurs actuelles du statut
echo "1. Valeurs actuelles du statut :\n";
$inscriptions = Inscription::select('statut')->distinct()->get();

foreach ($inscriptions as $inscription) {
    echo "  - '{$inscription->statut}'\n";
}

// 2. Vérifier la structure de la table
echo "\n2. Structure de la table inscriptions :\n";
try {
    $result = \DB::select("DESCRIBE inscriptions");
    foreach ($result as $column) {
        echo "  - {$column->Field} : {$column->Type}\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de la vérification de la structure : " . $e->getMessage() . "\n";
}

// 3. Vérifier les inscriptions existantes
echo "\n3. Inscriptions existantes :\n";
$inscriptions = Inscription::limit(5)->get();

foreach ($inscriptions as $inscription) {
    echo "  - ID {$inscription->id} : Apprenant {$inscription->apprenant_id}, Module {$inscription->module_id}, Statut '{$inscription->statut}'\n";
}

echo "\n=== VÉRIFICATION TERMINÉE ===\n"; 
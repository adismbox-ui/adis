<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VÉRIFICATION DE LA STRUCTURE DE LA TABLE QUESTIONNAIRES ===\n\n";

try {
    $columns = DB::select('DESCRIBE questionnaires');
    
    echo "Colonnes de la table questionnaires :\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
    
    echo "\n=== VÉRIFICATION DES MIGRATIONS ===\n";
    
    // Vérifier si la migration a été exécutée
    $migrationExists = DB::table('migrations')
        ->where('migration', '2025_08_04_011002_add_scheduling_fields_to_questionnaires_and_documents_tables')
        ->exists();
    
    if ($migrationExists) {
        echo "✅ Migration de programmation automatique exécutée\n";
    } else {
        echo "❌ Migration de programmation automatique NON exécutée\n";
        echo "Exécutez : php artisan migrate\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA VÉRIFICATION ===\n"; 
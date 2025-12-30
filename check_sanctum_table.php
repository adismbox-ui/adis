<?php

/**
 * Script pour vérifier si la table personal_access_tokens existe
 * Usage: php check_sanctum_table.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Vérification Table Sanctum ===\n\n";

$currentDb = DB::connection()->getDatabaseName();
echo "Base de données actuelle : $currentDb\n\n";

// Vérifier si la table personal_access_tokens existe
try {
    $tables = DB::select("SHOW TABLES LIKE 'personal_access_tokens'");
    
    if (!empty($tables)) {
        echo "✅ La table 'personal_access_tokens' EXISTE !\n\n";
        
        // Compter les tokens
        $count = DB::table('personal_access_tokens')->count();
        echo "Nombre de tokens : $count\n\n";
        
        // Vérifier la structure
        $columns = DB::select("DESCRIBE personal_access_tokens");
        echo "Structure de la table :\n";
        foreach ($columns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
    } else {
        echo "❌ La table 'personal_access_tokens' N'EXISTE PAS !\n\n";
        echo "💡 Solution : Exécutez les migrations\n";
        echo "   php artisan migrate\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n\n";
}

// Vérifier Sanctum
echo "=== Vérification Sanctum ===\n";
try {
    $sanctumInstalled = class_exists('Laravel\Sanctum\Sanctum');
    echo "Sanctum installé : " . ($sanctumInstalled ? "✅ Oui" : "❌ Non") . "\n";
    
    if ($sanctumInstalled) {
        $config = config('sanctum');
        echo "Configuration Sanctum :\n";
        echo "  - Guard : " . ($config['guard'][0] ?? 'default') . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n";


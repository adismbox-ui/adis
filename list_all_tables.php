<?php

/**
 * Script pour lister toutes les tables de toutes les bases de données
 * Usage: php list_all_tables.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Liste de Toutes les Tables ===\n\n";

// Configuration actuelle
echo "Configuration actuelle :\n";
echo "  DB_HOST = " . config('database.connections.mysql.host') . "\n";
echo "  DB_DATABASE = " . config('database.connections.mysql.database') . "\n";
echo "  Base connectée = " . DB::connection()->getDatabaseName() . "\n\n";

// Lister toutes les bases de données
echo "=== Toutes les Bases de Données ===\n";
try {
    $databases = DB::select("
        SELECT SCHEMA_NAME 
        FROM INFORMATION_SCHEMA.SCHEMATA 
        WHERE SCHEMA_NAME NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
        ORDER BY SCHEMA_NAME
    ");
    
    if (empty($databases)) {
        echo "Aucune base de données trouvée (sauf les bases système).\n\n";
    } else {
        foreach ($databases as $db) {
            $dbName = $db->SCHEMA_NAME;
            echo "\n📁 Base : $dbName\n";
            echo str_repeat("-", 50) . "\n";
            
            // Lister toutes les tables dans cette base
            try {
                $tables = DB::select("
                    SELECT TABLE_NAME, TABLE_ROWS 
                    FROM INFORMATION_SCHEMA.TABLES 
                    WHERE TABLE_SCHEMA = ? 
                    ORDER BY TABLE_NAME
                ", [$dbName]);
                
                if (!empty($tables)) {
                    echo "Tables (" . count($tables) . ") :\n";
                    foreach ($tables as $table) {
                        $tableName = $table->TABLE_NAME;
                        $rows = $table->TABLE_ROWS ?? 'N/A';
                        echo "  ✓ $tableName ($rows lignes)\n";
                    }
                } else {
                    echo "  (Aucune table)\n";
                }
            } catch (\Exception $e) {
                echo "  ❌ Erreur : " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Résumé : chercher les tables d'utilisateurs
    echo "\n\n=== Recherche Tables d'Utilisateurs ===\n";
    $userTables = DB::select("
        SELECT TABLE_SCHEMA, TABLE_NAME, TABLE_ROWS
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_NAME IN ('utilisateurs', 'users', 'user')
        AND TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
    ");
    
    if (!empty($userTables)) {
        echo "✅ Tables d'utilisateurs trouvées :\n";
        foreach ($userTables as $ut) {
            echo "  Base : {$ut->TABLE_SCHEMA}\n";
            echo "  Table : {$ut->TABLE_NAME}\n";
            echo "  Lignes : " . ($ut->TABLE_ROWS ?? 'N/A') . "\n";
            echo "  → Utilisez : DB_DATABASE={$ut->TABLE_SCHEMA}\n\n";
        }
    } else {
        echo "❌ Aucune table d'utilisateurs trouvée.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "\nVérifiez la connexion à la base de données.\n";
}

// Lister les tables de la base actuellement connectée
echo "\n=== Tables de la Base Actuellement Connectée ===\n";
try {
    $currentDb = DB::connection()->getDatabaseName();
    echo "Base : $currentDb\n";
    
    $tables = DB::select("SHOW TABLES");
    if (!empty($tables)) {
        $tableKey = "Tables_in_$currentDb";
        echo "Tables (" . count($tables) . ") :\n";
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            echo "  - $tableName\n";
        }
    } else {
        echo "  (Aucune table)\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n";


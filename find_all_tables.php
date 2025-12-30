<?php

/**
 * Script pour trouver toutes les bases de données et leurs tables
 * Usage: php find_all_tables.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Recherche de Toutes les Bases de Données ===\n\n";

// Lister toutes les bases de données (sauf les bases système)
try {
    $databases = DB::select("
        SELECT SCHEMA_NAME 
        FROM INFORMATION_SCHEMA.SCHEMATA 
        WHERE SCHEMA_NAME NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
        ORDER BY SCHEMA_NAME
    ");
    
    echo "Bases de données trouvées :\n";
    foreach ($databases as $db) {
        $dbName = $db->SCHEMA_NAME;
        echo "\n📁 Base : $dbName\n";
        
        // Lister les tables dans cette base
        try {
            $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?", [$dbName]);
            
            if (!empty($tables)) {
                echo "   Tables :\n";
                foreach ($tables as $table) {
                    $tableName = $table->TABLE_NAME;
                    echo "   - $tableName\n";
                    
                    // Vérifier si c'est une table d'utilisateurs
                    if (in_array(strtolower($tableName), ['utilisateurs', 'users', 'user'])) {
                        echo "     ⭐ TABLE D'UTILISATEURS TROUVÉE !\n";
                    }
                }
            } else {
                echo "   (Aucune table)\n";
            }
        } catch (\Exception $e) {
            echo "   Erreur : " . $e->getMessage() . "\n";
        }
    }
    
    // Chercher spécifiquement les tables d'utilisateurs
    echo "\n\n=== Recherche de Tables d'Utilisateurs ===\n";
    $userTables = DB::select("
        SELECT TABLE_SCHEMA, TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_NAME IN ('utilisateurs', 'users', 'user')
        AND TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
    ");
    
    if (!empty($userTables)) {
        echo "✅ Tables d'utilisateurs trouvées :\n";
        foreach ($userTables as $ut) {
            echo "   Base : {$ut->TABLE_SCHEMA}, Table : {$ut->TABLE_NAME}\n";
            echo "   → DB_DATABASE={$ut->TABLE_SCHEMA}\n";
        }
    } else {
        echo "❌ Aucune table d'utilisateurs trouvée.\n";
        echo "\n💡 Vous devrez peut-être créer la base de données et exécuter les migrations.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "\nVérifiez la connexion à la base de données.\n";
}

echo "\n";


<?php

/**
 * Script pour trouver la base de données utilisée par l'application
 * Usage: php find_database_tinker.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Recherche de la Base de Données ===\n\n";

// Méthode 1 : Vérifier la configuration actuelle
echo "1. Configuration actuelle :\n";
echo "   DB_DATABASE = " . config('database.connections.mysql.database') . "\n";
echo "   DB_HOST = " . config('database.connections.mysql.host') . "\n\n";

// Méthode 2 : Vérifier la base de données connectée
try {
    $currentDb = DB::connection()->getDatabaseName();
    echo "2. Base de données actuellement connectée : $currentDb\n\n";
} catch (\Exception $e) {
    echo "2. Erreur de connexion : " . $e->getMessage() . "\n\n";
}

// Méthode 3 : Chercher toutes les bases de données
echo "3. Recherche de la base contenant la table 'utilisateurs'...\n";
try {
    $result = DB::select("
        SELECT DISTINCT TABLE_SCHEMA 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_NAME = 'utilisateurs' 
        AND TABLE_SCHEMA NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
        LIMIT 1
    ");
    
    if (!empty($result)) {
        $dbName = $result[0]->TABLE_SCHEMA;
        echo "   ✅ Base de données trouvée : $dbName\n\n";
        
        // Vérifier les tables dans cette base
        echo "4. Tables dans '$dbName' :\n";
        $tables = DB::select("SHOW TABLES FROM `$dbName`");
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "   - $tableName\n";
        }
        
        echo "\n5. ✅ Solution :\n";
        echo "   Dans Dokploy → Environment, modifiez :\n";
        echo "   DB_DATABASE=$dbName\n";
    } else {
        echo "   ❌ Aucune base de données trouvée avec la table 'utilisateurs'\n";
        echo "\n   💡 Vous devrez peut-être créer la base de données et exécuter les migrations.\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Erreur : " . $e->getMessage() . "\n";
    echo "\n   💡 Essayez de vous connecter à la base de données avec les identifiants fournis.\n";
}

echo "\n";









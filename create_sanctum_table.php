<?php

/**
 * Script pour créer la table personal_access_tokens si elle n'existe pas
 * Usage: php create_sanctum_table.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Création Table Sanctum ===\n\n";

$currentDb = DB::connection()->getDatabaseName();
echo "Base de données actuelle : $currentDb\n\n";

// Vérifier si la table existe
$tableExists = Schema::hasTable('personal_access_tokens');

if ($tableExists) {
    echo "✅ La table 'personal_access_tokens' existe déjà !\n\n";
    
    // Compter les tokens
    $count = DB::table('personal_access_tokens')->count();
    echo "Nombre de tokens : $count\n\n";
    
    // Vérifier la structure
    $columns = DB::select("DESCRIBE personal_access_tokens");
    echo "Structure de la table :\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    exit(0);
}

echo "❌ La table 'personal_access_tokens' n'existe pas.\n";
echo "Création de la table...\n\n";

// SQL pour créer la table personal_access_tokens (structure standard de Sanctum)
$sql = "
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    DB::statement($sql);
    echo "✅ Table 'personal_access_tokens' créée avec succès !\n\n";
    
    // Vérifier la structure
    $columns = DB::select("DESCRIBE personal_access_tokens");
    echo "Structure de la table créée :\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n✅ La table est prête à être utilisée par Sanctum !\n";
} catch (\Exception $e) {
    echo "❌ Erreur lors de la création de la table : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";






<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Exécution de la commande d'envoi automatique ===\n\n";

// Exécuter la commande d'envoi automatique
$command = new \App\Console\Commands\SendScheduledContent();
$command->handle();

echo "\n=== Commande terminée ===\n"; 
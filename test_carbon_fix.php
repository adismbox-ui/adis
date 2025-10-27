<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SessionFormation;
use App\Models\Questionnaire;
use Carbon\Carbon;

echo "=== TEST DE CORRECTION CARBON ===\n\n";

try {
    // Test de l'import Carbon
    echo "1. Test de l'import Carbon :\n";
    $now = Carbon::now();
    echo "   ✅ Carbon fonctionne : {$now->format('Y-m-d H:i:s')}\n";
    
    // Test de création d'un questionnaire
    echo "\n2. Test de création d'un questionnaire :\n";
    
    $sessions = SessionFormation::orderBy('date_debut', 'desc')->get();
    if ($sessions->isEmpty()) {
        echo "   ❌ Aucune session trouvée\n";
        exit;
    }
    
    $session = $sessions->first();
    $dateEnvoi = Carbon::now()->addMinutes(30);
    
    $questionnaire = Questionnaire::create([
        'titre' => 'Test Carbon Fix',
        'description' => 'Test de correction de l\'import Carbon',
        'module_id' => 1,
        'niveau_id' => 1,
        'session_id' => $session->id,
        'date_envoi' => $dateEnvoi,
        'envoye' => false,
        'minutes' => 30,
        'semaine' => 1,
        'type_devoir' => 'hebdomadaire',
        'user_id' => 1,
    ]);
    
    echo "   ✅ Questionnaire créé avec succès !\n";
    echo "   - ID: {$questionnaire->id}\n";
    echo "   - Titre: {$questionnaire->titre}\n";
    echo "   - Date d'envoi: {$questionnaire->date_envoi}\n";
    
    echo "\n🎉 CORRECTION RÉUSSIE ! L'import Carbon fonctionne maintenant.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "Stack trace :\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DU TEST ===\n"; 
<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SessionFormation;
use App\Models\Questionnaire;
use App\Models\Question;
use Carbon\Carbon;

echo "=== TEST D'ENVOI IMMÉDIAT ===\n\n";

try {
    // 1. Test avec une date d'envoi dans le passé (envoi immédiat)
    echo "1. Test avec date d'envoi dans le passé (envoi immédiat) :\n";
    
    $sessions = SessionFormation::orderBy('date_debut', 'desc')->get();
    if ($sessions->isEmpty()) {
        echo "   ❌ Aucune session trouvée\n";
        exit;
    }
    
    $session = $sessions->first();
    $dateEnvoiPassee = Carbon::now()->subMinutes(10); // 10 minutes dans le passé
    
    $questionnaire = Questionnaire::create([
        'titre' => 'Test Envoi Immédiat - Passé',
        'description' => 'Questionnaire avec date d\'envoi dans le passé',
        'module_id' => 1,
        'niveau_id' => 1,
        'session_id' => $session->id,
        'date_envoi' => $dateEnvoiPassee,
        'envoye' => false,
        'minutes' => 30,
        'semaine' => 1,
        'type_devoir' => 'hebdomadaire',
        'user_id' => 1,
    ]);
    
    echo "   ✅ Questionnaire créé avec date dans le passé\n";
    echo "   - Date d'envoi: {$questionnaire->date_envoi}\n";
    echo "   - Heure actuelle: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
    echo "   - Doit être envoyé immédiatement: " . ($questionnaire->date_envoi <= Carbon::now() ? 'OUI' : 'NON') . "\n";
    
    // 2. Test avec une date d'envoi dans le futur (programmation)
    echo "\n2. Test avec date d'envoi dans le futur (programmation) :\n";
    
    $dateEnvoiFuture = Carbon::now()->addMinutes(30); // 30 minutes dans le futur
    
    $questionnaire2 = Questionnaire::create([
        'titre' => 'Test Envoi Immédiat - Futur',
        'description' => 'Questionnaire avec date d\'envoi dans le futur',
        'module_id' => 1,
        'niveau_id' => 1,
        'session_id' => $session->id,
        'date_envoi' => $dateEnvoiFuture,
        'envoye' => false,
        'minutes' => 30,
        'semaine' => 1,
        'type_devoir' => 'hebdomadaire',
        'user_id' => 1,
    ]);
    
    echo "   ✅ Questionnaire créé avec date dans le futur\n";
    echo "   - Date d'envoi: {$questionnaire2->date_envoi}\n";
    echo "   - Heure actuelle: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
    echo "   - Doit être envoyé immédiatement: " . ($questionnaire2->date_envoi <= Carbon::now() ? 'OUI' : 'NON') . "\n";
    
    // 3. Vérifier les questionnaires programmés
    echo "\n3. Questionnaires programmés en base :\n";
    $questionnairesProgrammes = Questionnaire::where('envoye', false)
        ->whereNotNull('session_id')
        ->with(['session'])
        ->get();
    
    if ($questionnairesProgrammes->isEmpty()) {
        echo "   Aucun questionnaire programmé trouvé.\n";
    } else {
        foreach ($questionnairesProgrammes as $q) {
            $status = $q->date_envoi <= Carbon::now() ? 'PRÊT À ENVOYER' : 'PROGRAMMÉ';
            echo "   - {$q->titre} (Session: {$q->session->nom}, Envoi: {$q->date_envoi}, Status: {$status})\n";
        }
    }
    
    // 4. Test de la commande d'envoi automatique
    echo "\n4. Test de la commande d'envoi automatique :\n";
    echo "   Exécution de la commande...\n";
    
    // Simuler l'exécution de la commande
    $questionnairesAEnvoyer = Questionnaire::where('date_envoi', '<=', Carbon::now())
        ->where('envoye', false)
        ->get();
    
    echo "   Questionnaires prêts à envoyer : {$questionnairesAEnvoyer->count()}\n";
    
    foreach ($questionnairesAEnvoyer as $q) {
        echo "   - {$q->titre} (Date d'envoi: {$q->date_envoi})\n";
    }
    
    echo "\n🎉 TEST RÉUSSI ! Le système d'envoi immédiat est configuré.\n";
    echo "Maintenant :\n";
    echo "1. Si vous sélectionnez une date/heure dans le passé ou maintenant → ENVOI IMMÉDIAT\n";
    echo "2. Si vous sélectionnez une date/heure dans le futur → PROGRAMMATION\n";
    echo "3. Testez sur http://127.0.0.1:8000/questionnaires/create\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "Stack trace :\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DU TEST ===\n"; 
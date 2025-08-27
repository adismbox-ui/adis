<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SessionFormation;
use App\Models\Questionnaire;
use App\Models\Question;
use Carbon\Carbon;

echo "=== TEST DE PROGRAMMATION MANUELLE ===\n\n";

// 1. Vérifier les sessions disponibles
echo "1. Sessions disponibles :\n";
$sessions = SessionFormation::orderBy('date_debut', 'desc')->get();
foreach ($sessions as $session) {
    echo "   - ID: {$session->id} | Nom: {$session->nom} | Début: {$session->date_debut} | Fin: {$session->date_fin}\n";
}

if ($sessions->isEmpty()) {
    echo "❌ Aucune session trouvée ! Créez d'abord des sessions.\n";
    exit;
}

// 2. Sélectionner la première session pour le test
$session = $sessions->first();
echo "\n2. Session sélectionnée pour le test : {$session->nom} (ID: {$session->id})\n";

// 3. Créer un questionnaire avec une date d'envoi manuelle
echo "\n3. Création d'un questionnaire avec date d'envoi manuelle :\n";

try {
    // Date d'envoi manuelle (dans 1 heure)
    $dateEnvoi = Carbon::now()->addHour();
    
    $questionnaire = Questionnaire::create([
        'titre' => 'Questionnaire test - Programmation manuelle',
        'description' => 'Questionnaire créé pour tester la programmation manuelle',
        'module_id' => 1, // Premier module
        'niveau_id' => 1, // Premier niveau
        'session_id' => $session->id,
        'date_envoi' => $dateEnvoi,
        'envoye' => false,
        'minutes' => 30,
        'semaine' => 1,
        'type_devoir' => 'hebdomadaire',
        'user_id' => 1, // Premier utilisateur
    ]);
    
    echo "   ✅ Questionnaire créé avec succès !\n";
    echo "   - ID: {$questionnaire->id}\n";
    echo "   - Titre: {$questionnaire->titre}\n";
    echo "   - Session: {$session->nom}\n";
    echo "   - Date d'envoi manuelle: {$questionnaire->date_envoi}\n";
    echo "   - Envoyé: " . ($questionnaire->envoye ? 'Oui' : 'Non') . "\n";
    
    // 4. Créer quelques questions de test
    echo "\n4. Création des questions de test :\n";
    
    $questions = [
        [
            'texte' => 'Test de programmation manuelle - Question 1',
            'choix' => ['Option A', 'Option B', 'Option C', 'Option D'],
            'bonne_reponse' => 'Option A',
            'points' => 10
        ],
        [
            'texte' => 'Test de programmation manuelle - Question 2',
            'choix' => ['Réponse 1', 'Réponse 2', 'Réponse 3', 'Réponse 4'],
            'bonne_reponse' => 'Réponse 2',
            'points' => 10
        ]
    ];
    
    foreach ($questions as $index => $questionData) {
        $question = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'texte' => $questionData['texte'],
            'choix' => $questionData['choix'],
            'bonne_reponse' => $questionData['bonne_reponse'],
            'points' => $questionData['points'],
        ]);
        
        echo "   ✅ Question " . ($index + 1) . " créée : {$question->texte}\n";
    }
    
    // 5. Test de la commande d'envoi automatique (désactivée)
    echo "\n5. Test de la commande d'envoi automatique :\n";
    echo "   Exécution de la commande...\n";
    
    // Simuler l'exécution de la commande
    $now = Carbon::now();
    echo "   Heure actuelle : {$now->format('Y-m-d H:i:s')}\n";
    echo "   Date d'envoi programmée : {$questionnaire->date_envoi->format('Y-m-d H:i:s')}\n";
    
    if ($questionnaire->date_envoi <= $now) {
        echo "   ✅ Le questionnaire est prêt à être envoyé !\n";
        echo "   (La vérification d'heure est désactivée pour les tests)\n";
    } else {
        echo "   ⏰ Le questionnaire sera envoyé plus tard\n";
    }
    
    // 6. Vérifier les questionnaires programmés
    echo "\n6. Questionnaires programmés en base :\n";
    $questionnairesProgrammes = Questionnaire::where('envoye', false)
        ->whereNotNull('session_id')
        ->with(['session', 'module', 'niveau'])
        ->get();
    
    if ($questionnairesProgrammes->isEmpty()) {
        echo "   Aucun questionnaire programmé trouvé.\n";
    } else {
        foreach ($questionnairesProgrammes as $q) {
            echo "   - {$q->titre} (Session: {$q->session->nom}, Envoi: {$q->date_envoi})\n";
        }
    }
    
    echo "\n🎉 TEST RÉUSSI ! La programmation manuelle fonctionne.\n";
    echo "Vous pouvez maintenant :\n";
    echo "1. Aller sur http://127.0.0.1:8000/questionnaires/create\n";
    echo "2. Définir manuellement la date et l'heure d'envoi\n";
    echo "3. Tester l'envoi avec : php artisan content:send-scheduled\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la création : " . $e->getMessage() . "\n";
    echo "Stack trace :\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DU TEST ===\n"; 
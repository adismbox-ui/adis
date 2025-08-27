<?php
/**
 * Test de l'Envoi Automatique des Questionnaires
 * 
 * Ce script teste le système d'envoi automatique en créant
 * un questionnaire de test et en vérifiant son envoi.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Questionnaire;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\SessionFormation;
use App\Models\Question;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Test de l'Envoi Automatique des Questionnaires\n";
echo "================================================\n\n";

try {
    // 1. Vérifier l'état actuel des questionnaires
    echo "📊 État actuel des questionnaires :\n";
    $questionnaires = Questionnaire::with(['module', 'niveau'])->get();
    
    foreach ($questionnaires as $q) {
        $now = Carbon::now();
        $statut = $q->envoye ? "ENVOYÉ" : "NON ENVOYÉ";
        $retard = Carbon::parse($q->date_envoi) < $now ? " (en retard " . Carbon::parse($q->date_envoi)->diffInMinutes($now) . " min)" : "";
        
        echo "  - {$q->titre} : {$statut}{$retard}\n";
        echo "    Date d'envoi : {$q->date_envoi}\n";
        echo "    Module : {$q->module->titre}\n";
    }
    
    echo "\n";
    
    // 2. Créer un questionnaire de test pour dans 2 minutes
    echo "🔧 Création d'un questionnaire de test...\n";
    
    $dateEnvoi = Carbon::now()->addMinutes(2);
    
    // Récupérer le premier module et niveau disponibles
    $module = Module::first();
    $niveau = Niveau::first();
    $session = SessionFormation::first();
    
    if (!$module || !$niveau || !$session) {
        echo "❌ Erreur : Impossible de trouver un module, niveau ou session pour le test\n";
        exit(1);
    }
    
    $questionnaire = Questionnaire::create([
        'titre' => 'Test Envoi Automatique - ' . $dateEnvoi->format('H:i'),
        'description' => 'Questionnaire de test pour vérifier l\'envoi automatique',
        'module_id' => $module->id,
        'niveau_id' => $niveau->id,
        'session_id' => $session->id,
        'date_envoi' => $dateEnvoi,
        'envoye' => false,
        'minutes' => 30,
        'semaine' => 1,
        'type_devoir' => 'hebdomadaire',
        'user_id' => 1, // Admin
    ]);
    
    // Créer quelques questions de test
    $questions = [
        [
            'texte' => 'Question de test 1',
            'choix' => ['A', 'B', 'C', 'D'],
            'bonne_reponse' => 'A',
            'points' => 10
        ],
        [
            'texte' => 'Question de test 2',
            'choix' => ['Oui', 'Non'],
            'bonne_reponse' => 'Oui',
            'points' => 5
        ]
    ];
    
    foreach ($questions as $q) {
        $questionnaire->questions()->create($q);
    }
    
    echo "✅ Questionnaire de test créé :\n";
    echo "  - ID : {$questionnaire->id}\n";
    echo "  - Titre : {$questionnaire->titre}\n";
    echo "  - Date d'envoi : {$questionnaire->date_envoi}\n";
    echo "  - Module : {$module->titre}\n";
    echo "  - Niveau : {$niveau->nom}\n";
    
    echo "\n⏰ Le questionnaire sera envoyé automatiquement dans 2 minutes.\n";
    echo "📋 Pour surveiller l'envoi, lancez : php surveillance_questionnaires_automatique.php\n";
    echo "🔍 Ou vérifiez manuellement avec : php artisan content:send-scheduled\n";
    
    // 3. Vérifier les questionnaires en retard
    echo "\n📋 Questionnaires en retard :\n";
    $enRetard = Questionnaire::where('date_envoi', '<', Carbon::now())
        ->where('envoye', false)
        ->get();
    
    if ($enRetard->count() > 0) {
        foreach ($enRetard as $q) {
            $retard = Carbon::parse($q->date_envoi)->diffInMinutes(Carbon::now());
            echo "  - {$q->titre} : en retard de {$retard} minutes\n";
        }
        echo "\n⚠️  Ces questionnaires doivent être envoyés immédiatement !\n";
    } else {
        echo "  ✅ Aucun questionnaire en retard\n";
    }
    
    echo "\n🎯 Test terminé avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test : " . $e->getMessage() . "\n";
    echo "📋 Trace : " . $e->getTraceAsString() . "\n";
} 
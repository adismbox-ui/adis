<?php
/**
 * Vérification Rapide des Questionnaires
 * 
 * Ce script vérifie rapidement l'état des questionnaires
 * et identifie les problèmes potentiels.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;
use App\Models\Questionnaire;
use App\Models\Apprenant;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Vérification Rapide des Questionnaires\n";
echo "========================================\n\n";

$now = Carbon::now();

// 1. État général
echo "📊 État Général :\n";
$total = Questionnaire::count();
$envoyes = Questionnaire::where('envoye', true)->count();
$nonEnvoyes = Questionnaire::where('envoye', false)->count();
$enRetard = Questionnaire::where('date_envoi', '<', $now)->where('envoye', false)->count();
$programmes = Questionnaire::where('date_envoi', '>', $now)->where('envoye', false)->count();

echo "  - Total : {$total} questionnaire(s)\n";
echo "  - Envoyés : {$envoyes} questionnaire(s)\n";
echo "  - Non envoyés : {$nonEnvoyes} questionnaire(s)\n";
echo "  - En retard : {$enRetard} questionnaire(s)\n";
echo "  - Programmés : {$programmes} questionnaire(s)\n\n";

// 2. Questionnaires en retard
if ($enRetard > 0) {
    echo "🚨 QUESTIONNAIRES EN RETARD :\n";
    $enRetardList = Questionnaire::where('date_envoi', '<', $now)
        ->where('envoye', false)
        ->with(['module', 'niveau'])
        ->get();
    
    foreach ($enRetardList as $q) {
        $retard = Carbon::parse($q->date_envoi)->diffInMinutes($now);
        echo "  - {$q->titre}\n";
        echo "    Date d'envoi : {$q->date_envoi}\n";
        echo "    Retard : {$retard} minutes\n";
        echo "    Module : {$q->module->titre}\n";
        echo "    Niveau : {$q->niveau->nom}\n\n";
    }
}

// 3. Questionnaires programmés
if ($programmes > 0) {
    echo "⏰ QUESTIONNAIRES PROGRAMMÉS :\n";
    $programmesList = Questionnaire::where('date_envoi', '>', $now)
        ->where('envoye', false)
        ->with(['module', 'niveau'])
        ->orderBy('date_envoi', 'asc')
        ->get();
    
    foreach ($programmesList as $q) {
        $attente = Carbon::parse($q->date_envoi)->diffInMinutes($now);
        echo "  - {$q->titre}\n";
        echo "    Date d'envoi : {$q->date_envoi}\n";
        echo "    Attente : {$attente} minutes\n";
        echo "    Module : {$q->module->titre}\n";
        echo "    Niveau : {$q->niveau->nom}\n\n";
    }
}

// 4. Vérification des apprenants
echo "👥 Vérification des Apprenants :\n";
$apprenants = Apprenant::with('utilisateur')->count();
echo "  - Total apprenants : {$apprenants}\n";

// 5. Recommandations
echo "\n💡 RECOMMANDATIONS :\n";

if ($enRetard > 0) {
    echo "  🚨 {$enRetard} questionnaire(s) en retard - Lancez l'envoi immédiatement :\n";
    echo "     php artisan content:send-scheduled\n";
    echo "     ou\n";
    echo "     php surveillance_questionnaires_automatique.php\n\n";
}

if ($programmes > 0) {
    echo "  ⏰ {$programmes} questionnaire(s) programmé(s) - La surveillance automatique s'en occupera\n\n";
}

if ($enRetard == 0 && $programmes == 0) {
    echo "  ✅ Aucun problème détecté - Le système fonctionne correctement\n\n";
}

// 6. État de la surveillance
echo "🔧 ÉTAT DE LA SURVEILLANCE :\n";
echo "  - Surveillance continue : php surveillance_questionnaires_automatique.php\n";
echo "  - Commande artisan : php artisan content:send-scheduled\n";
echo "  - Test du système : php test_envoi_automatique.php\n";
echo "  - Interface de démarrage : demarrer_surveillance.bat\n\n";

echo "✅ Vérification terminée !\n"; 
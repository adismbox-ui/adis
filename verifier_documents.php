<?php
/**
 * Vérification Rapide des Documents
 * 
 * Ce script vérifie rapidement l'état des documents
 * et identifie les problèmes potentiels.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;
use App\Models\Document;
use App\Models\Apprenant;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Vérification Rapide des Documents\n";
echo "===================================\n\n";

$now = Carbon::now();

// 1. État général
echo "📊 État Général :\n";
$total = Document::count();
$envoyes = Document::where('envoye', true)->count();
$nonEnvoyes = Document::where('envoye', false)->count();
$enRetard = Document::where('date_envoi', '<', $now)->where('envoye', false)->count();
$programmes = Document::where('date_envoi', '>', $now)->where('envoye', false)->count();

echo "  - Total : {$total} document(s)\n";
echo "  - Envoyés : {$envoyes} document(s)\n";
echo "  - Non envoyés : {$nonEnvoyes} document(s)\n";
echo "  - En retard : {$enRetard} document(s)\n";
echo "  - Programmés : {$programmes} document(s)\n\n";

// 2. Documents en retard
if ($enRetard > 0) {
    echo "🚨 DOCUMENTS EN RETARD :\n";
    $enRetardList = Document::where('date_envoi', '<', $now)
        ->where('envoye', false)
        ->with(['module', 'niveau'])
        ->get();
    
    foreach ($enRetardList as $d) {
        $retard = Carbon::parse($d->date_envoi)->diffInMinutes($now);
        echo "  - {$d->titre}\n";
        echo "    Date d'envoi : {$d->date_envoi}\n";
        echo "    Retard : {$retard} minutes\n";
        echo "    Module : " . ($d->module ? $d->module->titre : 'Général') . "\n";
        echo "    Niveau : " . ($d->niveau ? $d->niveau->nom : 'Non spécifié') . "\n\n";
    }
}

// 3. Documents programmés
if ($programmes > 0) {
    echo "⏰ DOCUMENTS PROGRAMMÉS :\n";
    $programmesList = Document::where('date_envoi', '>', $now)
        ->where('envoye', false)
        ->with(['module', 'niveau'])
        ->orderBy('date_envoi', 'asc')
        ->get();
    
    foreach ($programmesList as $d) {
        $attente = Carbon::parse($d->date_envoi)->diffInMinutes($now);
        echo "  - {$d->titre}\n";
        echo "    Date d'envoi : {$d->date_envoi}\n";
        echo "    Attente : {$attente} minutes\n";
        echo "    Module : " . ($d->module ? $d->module->titre : 'Général') . "\n";
        echo "    Niveau : " . ($d->niveau ? $d->niveau->nom : 'Non spécifié') . "\n\n";
    }
}

// 4. Vérification des apprenants
echo "👥 Vérification des Apprenants :\n";
$apprenants = Apprenant::with('utilisateur')->count();
echo "  - Total apprenants : {$apprenants}\n";

// 5. Recommandations
echo "\n💡 RECOMMANDATIONS :\n";

if ($enRetard > 0) {
    echo "  🚨 {$enRetard} document(s) en retard - Lancez l'envoi immédiatement :\n";
    echo "     php artisan content:send-scheduled\n";
    echo "     ou\n";
    echo "     php surveillance_documents_automatique.php\n\n";
}

if ($programmes > 0) {
    echo "  ⏰ {$programmes} document(s) programmé(s) - La surveillance automatique s'en occupera\n\n";
}

if ($enRetard == 0 && $programmes == 0) {
    echo "  ✅ Aucun problème détecté - Le système fonctionne correctement\n\n";
}

// 6. État de la surveillance
echo "🔧 ÉTAT DE LA SURVEILLANCE :\n";
echo "  - Surveillance continue : php surveillance_documents_automatique.php\n";
echo "  - Commande artisan : php artisan content:send-scheduled\n";
echo "  - Test du système : php test_envoi_documents_automatique.php\n";
echo "  - Interface de démarrage : demarrer_surveillance_documents.bat\n\n";

echo "✅ Vérification terminée !\n"; 
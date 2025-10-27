<?php
/**
 * Affichage des Statuts des Documents
 * 
 * Ce script affiche un résumé visuel de tous les documents
 * avec leurs dates d'envoi et statuts.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;
use App\Models\Document;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📊 AFFICHAGE DES STATUTS DES DOCUMENTS\n";
echo "=====================================\n\n";

$now = Carbon::now();
$documents = Document::with(['module', 'niveau'])->orderBy('date_envoi', 'desc')->get();

if ($documents->count() === 0) {
    echo "❌ Aucun document trouvé.\n";
    exit;
}

echo "📋 Résumé des documents :\n";
echo str_repeat("=", 80) . "\n";

foreach ($documents as $document) {
    $dateEnvoi = Carbon::parse($document->date_envoi);
    $isLate = $dateEnvoi < $now && !$document->envoye;
    $isWaiting = $dateEnvoi > $now && !$document->envoye;
    $isSent = $document->envoye;
    
    // Déterminer le statut
    if ($isSent) {
        $statut = "✅ ENVOYÉ";
        $couleur = "\033[32m"; // Vert
    } elseif ($isLate) {
        $retard = $dateEnvoi->diffInMinutes($now);
        $statut = "🚨 EN RETARD ({$retard} min)";
        $couleur = "\033[31m"; // Rouge
    } elseif ($isWaiting) {
        $attente = $dateEnvoi->diffInMinutes($now);
        $statut = "⏰ EN ATTENTE (dans {$attente} min)";
        $couleur = "\033[33m"; // Jaune
    } else {
        $statut = "❓ NON DÉFINI";
        $couleur = "\033[37m"; // Gris
    }
    
    echo "\n📄 Document #{$document->id} : {$document->titre}\n";
    echo "   📅 Date d'envoi : {$dateEnvoi->format('d/m/Y à H:i')}\n";
    echo "   📚 Module : " . ($document->module ? $document->module->titre : 'Général') . "\n";
    echo "   🎓 Niveau : " . ($document->niveau ? $document->niveau->nom : 'Non spécifié') . "\n";
    echo "   📊 Statut : {$couleur}{$statut}\033[0m\n";
    
    if ($document->semaine) {
        echo "   📅 Semaine : {$document->semaine}\n";
    }
    
    echo str_repeat("-", 60) . "\n";
}

// Statistiques
$total = $documents->count();
$envoyes = $documents->where('envoye', true)->count();
$enRetard = $documents->filter(function($d) use ($now) {
    return Carbon::parse($d->date_envoi) < $now && !$d->envoye;
})->count();
$enAttente = $documents->filter(function($d) use ($now) {
    return Carbon::parse($d->date_envoi) > $now && !$d->envoye;
})->count();

echo "\n📈 STATISTIQUES :\n";
echo str_repeat("=", 30) . "\n";
echo "   📊 Total : {$total} document(s)\n";
echo "   ✅ Envoyés : {$envoyes} document(s)\n";
echo "   🚨 En retard : {$enRetard} document(s)\n";
echo "   ⏰ En attente : {$enAttente} document(s)\n";

if ($enRetard > 0) {
    echo "\n⚠️  RECOMMANDATION :\n";
    echo "   Lancez l'envoi automatique pour traiter les documents en retard :\n";
    echo "   php artisan content:send-scheduled\n";
    echo "   ou\n";
    echo "   php surveillance_documents_automatique.php\n";
}

echo "\n🎯 Affichage terminé !\n"; 
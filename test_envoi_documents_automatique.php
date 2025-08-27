<?php
/**
 * Test de l'Envoi Automatique des Documents
 * 
 * Ce script teste le système d'envoi automatique en créant
 * un document de test et en vérifiant son envoi.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Document;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\SessionFormation;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Test de l'Envoi Automatique des Documents\n";
echo "============================================\n\n";

try {
    // 1. Vérifier l'état actuel des documents
    echo "📊 État actuel des documents :\n";
    $documents = Document::with(['module', 'niveau'])->get();
    
    foreach ($documents as $d) {
        $now = Carbon::now();
        $statut = $d->envoye ? "ENVOYÉ" : "NON ENVOYÉ";
        $retard = Carbon::parse($d->date_envoi) < $now ? " (en retard " . Carbon::parse($d->date_envoi)->diffInMinutes($now) . " min)" : "";
        
        echo "  - {$d->titre} : {$statut}{$retard}\n";
        echo "    Date d'envoi : {$d->date_envoi}\n";
        echo "    Module : " . ($d->module ? $d->module->titre : 'Général') . "\n";
    }
    
    echo "\n";
    
    // 2. Créer un document de test pour dans 2 minutes
    echo "🔧 Création d'un document de test...\n";
    
    $dateEnvoi = Carbon::now()->addMinutes(2);
    
    // Récupérer le premier module et niveau disponibles
    $module = Module::first();
    $niveau = Niveau::first();
    $session = SessionFormation::first();
    
    if (!$module || !$niveau || !$session) {
        echo "❌ Erreur : Impossible de trouver un module, niveau ou session pour le test\n";
        exit(1);
    }
    
    $document = Document::create([
        'titre' => 'Test Envoi Automatique Document - ' . $dateEnvoi->format('H:i'),
        'type' => 'pdf',
        'fichier' => 'documents/test-document.pdf',
        'module_id' => $module->id,
        'niveau_id' => $niveau->id,
        'session_id' => $session->id,
        'date_envoi' => $dateEnvoi,
        'envoye' => false,
        'semaine' => 1,
        'created_by_admin' => true,
    ]);
    
    echo "✅ Document de test créé :\n";
    echo "  - ID : {$document->id}\n";
    echo "  - Titre : {$document->titre}\n";
    echo "  - Date d'envoi : {$document->date_envoi}\n";
    echo "  - Module : {$module->titre}\n";
    echo "  - Niveau : {$niveau->nom}\n";
    
    echo "\n⏰ Le document sera envoyé automatiquement dans 2 minutes.\n";
    echo "📋 Pour surveiller l'envoi, lancez : php surveillance_documents_automatique.php\n";
    echo "🔍 Ou vérifiez manuellement avec : php artisan content:send-scheduled\n";
    
    // 3. Vérifier les documents en retard
    echo "\n📋 Documents en retard :\n";
    $enRetard = Document::where('date_envoi', '<', Carbon::now())
        ->where('envoye', false)
        ->get();
    
    if ($enRetard->count() > 0) {
        foreach ($enRetard as $d) {
            $retard = Carbon::parse($d->date_envoi)->diffInMinutes(Carbon::now());
            echo "  - {$d->titre} : en retard de {$retard} minutes\n";
        }
        echo "\n⚠️  Ces documents doivent être envoyés immédiatement !\n";
    } else {
        echo "  ✅ Aucun document en retard\n";
    }
    
    echo "\n🎯 Test terminé avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test : " . $e->getMessage() . "\n";
    echo "📋 Trace : " . $e->getTraceAsString() . "\n";
} 
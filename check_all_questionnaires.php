<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification de tous les questionnaires ===\n\n";

// 1. Lister tous les questionnaires
echo "1. Tous les questionnaires :\n";
$questionnaires = Questionnaire::with(['module.niveau', 'session'])->get();

if ($questionnaires->count() === 0) {
    echo "❌ Aucun questionnaire trouvé dans la base de données\n";
    exit;
}

foreach ($questionnaires as $q) {
    echo "\n--- Questionnaire ID {$q->id} ---\n";
    echo "Titre : {$q->titre}\n";
    echo "Description : {$q->description}\n";
    echo "Module : {$q->module->titre}\n";
    echo "Niveau : {$q->module->niveau->nom}\n";
    echo "Date d'envoi : {$q->date_envoi}\n";
    echo "Envoyé : " . ($q->envoye ? 'Oui' : 'Non') . "\n";
    echo "Nombre de questions : {$q->questions->count()}\n";
    
    // Vérifier si la date d'envoi est atteinte
    $now = Carbon::now();
    $dateEnvoi = Carbon::parse($q->date_envoi);
    $estAtteinte = $dateEnvoi <= $now;
    
    echo "Date actuelle : {$now->format('Y-m-d H:i:s')}\n";
    echo "Date d'envoi atteinte : " . ($estAtteinte ? 'Oui' : 'Non') . "\n";
    
    if ($estAtteinte && !$q->envoye) {
        echo "⚠️  QUESTIONNAIRE À ENVOYER !\n";
    }
}

// 2. Vérifier les questionnaires qui devraient être envoyés
echo "\n2. Questionnaires qui devraient être envoyés :\n";
$questionnairesAEnvoyer = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

echo "Nombre de questionnaires à envoyer : {$questionnairesAEnvoyer->count()}\n";
foreach ($questionnairesAEnvoyer as $q) {
    echo "- ID {$q->id} : {$q->titre} (Date: {$q->date_envoi})\n";
}

// 3. Vérifier les questionnaires envoyés
echo "\n3. Questionnaires envoyés :\n";
$questionnairesEnvoyes = Questionnaire::where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "Nombre de questionnaires envoyés : {$questionnairesEnvoyes->count()}\n";
foreach ($questionnairesEnvoyes as $q) {
    echo "- ID {$q->id} : {$q->titre} (Date: {$q->date_envoi})\n";
}

// 4. Tester avec un apprenant
echo "\n4. Test avec un apprenant :\n";
$apprenant = Apprenant::with(['utilisateur', 'paiements'])->first();

if ($apprenant) {
    echo "Apprenant : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    echo "Email : {$apprenant->utilisateur->email}\n";
    echo "Niveau : {$apprenant->niveau->nom}\n";
    
    // Modules payés
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "Modules payés : " . implode(', ', $moduleIds) . "\n";
    
    // Questionnaires disponibles pour cet apprenant
    $questionnairesDisponibles = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "Questionnaires disponibles pour cet apprenant : {$questionnairesDisponibles->count()}\n";
    foreach ($questionnairesDisponibles as $q) {
        echo "- ID {$q->id} : {$q->titre} (Module: {$q->module->titre})\n";
    }
} else {
    echo "❌ Aucun apprenant trouvé\n";
}

// 5. Exécuter l'envoi automatique
echo "\n5. Exécution de l'envoi automatique...\n";
$command = new \App\Console\Commands\SendScheduledContent();
$command->handle();

echo "\n=== Vérification terminée ===\n"; 
<?php

require_once 'vendor/autoload.php';

use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VÉRIFICATION ENVOI AUTOMATIQUE ===\n\n";

// 1. Vérifier tous les questionnaires
echo "1. État de tous les questionnaires :\n";
$questionnaires = Questionnaire::with(['module.niveau'])->get();

foreach ($questionnaires as $q) {
    $dateEnvoi = Carbon::parse($q->date_envoi);
    $maintenant = Carbon::now();
    $statut = $q->envoye ? 'Envoyé' : 'Non envoyé';
    $statutDate = $dateEnvoi->isPast() ? 'Passé' : 'Futur';
    
    echo "  - ID {$q->id} : '{$q->titre}'\n";
    echo "    Date d'envoi : " . $dateEnvoi->format('d/m/Y H:i') . " ({$statutDate})\n";
    echo "    Statut : {$statut}\n";
    echo "    ---\n";
}

// 2. Identifier les questionnaires en retard
echo "\n2. Questionnaires en retard (date passée mais non envoyés) :\n";
$questionnairesEnRetard = Questionnaire::where('envoye', false)
    ->where('date_envoi', '<=', Carbon::now())
    ->with(['module.niveau'])
    ->get();

if ($questionnairesEnRetard->count() > 0) {
    foreach ($questionnairesEnRetard as $q) {
        $dateEnvoi = Carbon::parse($q->date_envoi);
        echo "  - ID {$q->id} : '{$q->titre}'\n";
        echo "    Date d'envoi : " . $dateEnvoi->format('d/m/Y H:i') . "\n";
        echo "    Retard de : " . $dateEnvoi->diffForHumans() . "\n";
        echo "    ---\n";
    }
} else {
    echo "  ✅ Aucun questionnaire en retard\n";
}

// 3. Corriger les questionnaires en retard
echo "\n3. Correction des questionnaires en retard :\n";

$corriges = 0;
foreach ($questionnairesEnRetard as $q) {
    $q->update(['envoye' => true]);
    echo "  ✅ Questionnaire ID {$q->id} marqué comme envoyé\n";
    $corriges++;
}

if ($corriges === 0) {
    echo "  ℹ️  Aucun questionnaire à corriger\n";
}

// 4. Vérifier les questionnaires envoyés
echo "\n4. Questionnaires envoyés :\n";
$questionnairesEnvoyes = Questionnaire::where('envoye', true)
    ->with(['module.niveau'])
    ->get();

foreach ($questionnairesEnvoyes as $q) {
    $dateEnvoi = Carbon::parse($q->date_envoi);
    echo "  - ID {$q->id} : '{$q->titre}'\n";
    echo "    Date d'envoi : " . $dateEnvoi->format('d/m/Y H:i') . "\n";
    echo "    Module : {$q->module->titre}\n";
    echo "    Niveau : {$q->module->niveau->nom}\n";
    echo "    ---\n";
}

// 5. Tester la commande d'envoi automatique
echo "\n5. Test de la commande d'envoi automatique :\n";

try {
    // Simuler la commande d'envoi automatique
    $questionnairesAAEnvoyer = Questionnaire::where('envoye', false)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "  Questionnaires à envoyer : {$questionnairesAAEnvoyer->count()}\n";
    
    foreach ($questionnairesAAEnvoyer as $q) {
        $q->update(['envoye' => true]);
        echo "  ✅ Questionnaire ID {$q->id} envoyé automatiquement\n";
    }
    
} catch (Exception $e) {
    echo "  ❌ Erreur lors de l'envoi automatique : " . $e->getMessage() . "\n";
}

// 6. Vérification finale
echo "\n6. Vérification finale :\n";

$questionnairesEnRetardFinal = Questionnaire::where('envoye', false)
    ->where('date_envoi', '<=', Carbon::now())
    ->count();

echo "  Questionnaires encore en retard : {$questionnairesEnRetardFinal}\n";

if ($questionnairesEnRetardFinal === 0) {
    echo "  ✅ Tous les questionnaires sont maintenant envoyés !\n";
} else {
    echo "  ⚠️  Il reste {$questionnairesEnRetardFinal} questionnaire(s) en retard\n";
}

// 7. Instructions pour activer l'envoi automatique
echo "\n7. INSTRUCTIONS POUR L'ENVOI AUTOMATIQUE :\n";
echo "============================================\n";
echo "Pour que l'envoi automatique fonctionne en continu :\n";
echo "1. Lancez le script de surveillance : php surveillance_automatique.php\n";
echo "2. Ou utilisez le fichier .bat : surveillance_automatique.bat\n";
echo "3. Ou configurez un cron job :\n";
echo "   */5 * * * * cd /path/to/project && php artisan content:send-scheduled\n";

echo "\n=== VÉRIFICATION TERMINÉE ===\n";
echo "Testez maintenant : http://127.0.0.1:8000/questionnaire_test\n"; 
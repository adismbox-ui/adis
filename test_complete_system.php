<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\User;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\SessionFormation;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test complet du système de programmation automatique ===\n\n";

// 1. Créer des données de test si nécessaire
echo "1. Vérification des données de test...\n";

// Créer un niveau si nécessaire
$niveau = Niveau::firstOrCreate(['nom' => 'Débutant'], ['description' => 'Niveau débutant']);

// Créer un module si nécessaire
$module = Module::firstOrCreate([
    'titre' => 'Test Module',
    'niveau_id' => $niveau->id
], [
    'description' => 'Module de test',
    'prix' => 100,
    'formateur_id' => 1
]);

// Créer une session si nécessaire
$session = SessionFormation::firstOrCreate([
    'nom' => 'Session Test'
], [
    'date_debut' => Carbon::now()->subDays(7),
    'date_fin' => Carbon::now()->addDays(30),
    'description' => 'Session de test'
]);

// 2. Créer un questionnaire programmé pour maintenant
echo "2. Création d'un questionnaire programmé...\n";

$questionnaire = Questionnaire::create([
    'titre' => 'Questionnaire Test Automatique',
    'description' => 'Questionnaire créé automatiquement pour test',
    'module_id' => $module->id,
    'niveau_id' => $niveau->id,
    'session_id' => $session->id,
    'date_envoi' => Carbon::now()->subMinutes(5), // Programmé il y a 5 minutes
    'envoye' => false,
    'minutes' => 30,
    'semaine' => 1,
    'type_devoir' => 'hebdomadaire',
    'user_id' => 1
]);

echo "✅ Questionnaire créé : {$questionnaire->titre}\n";
echo "   📅 Date d'envoi : {$questionnaire->date_envoi}\n";
echo "   📚 Module : {$module->titre}\n";
echo "   🎓 Niveau : {$niveau->nom}\n";

// 3. Vérifier l'état avant envoi
echo "\n3. État avant envoi automatique :\n";
$questionnairesAvant = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

echo "Questionnaires à envoyer : {$questionnairesAvant->count()}\n";

// 4. Exécuter l'envoi automatique
echo "\n4. Exécution de l'envoi automatique...\n";
$command = new \App\Console\Commands\SendScheduledContent();
$command->handle();

// 5. Vérifier l'état après envoi
echo "\n5. État après envoi automatique :\n";
$questionnaireApres = Questionnaire::find($questionnaire->id);
echo "Questionnaire envoyé : " . ($questionnaireApres->envoye ? 'Oui' : 'Non') . "\n";

// 6. Tester l'affichage pour un apprenant
echo "\n6. Test de l'affichage pour un apprenant :\n";
$apprenant = Apprenant::with(['utilisateur', 'paiements'])->first();
if ($apprenant) {
    echo "Apprenant : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    
    // Simuler la logique du contrôleur
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "Modules payés : " . implode(', ', $moduleIds) . "\n";
    
    $questionnairesDisponibles = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "Questionnaires disponibles : {$questionnairesDisponibles->count()}\n";
    foreach ($questionnairesDisponibles as $q) {
        echo "  - {$q->titre} (Module: {$q->module->titre})\n";
    }
} else {
    echo "Aucun apprenant trouvé\n";
}

// 7. Nettoyer les données de test
echo "\n7. Nettoyage des données de test...\n";
$questionnaire->delete();
echo "✅ Données de test supprimées\n";

echo "\n=== Test terminé ===\n"; 
<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Paiement;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC COMPLET DU SYSTÈME DE QUESTIONNAIRES ===\n\n";

// 1. VÉRIFIER LES APPRENANTS
echo "1. VÉRIFICATION DES APPRENANTS...\n";
$apprenants = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->get();

if ($apprenants->count() === 0) {
    echo "❌ Aucun apprenant trouvé\n";
    exit;
}

$apprenant = $apprenants->first();
echo "Apprenant test : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
echo "Email : {$apprenant->utilisateur->email}\n";
echo "Niveau : {$apprenant->niveau->nom} (ID: {$apprenant->niveau_id})\n";

// 2. VÉRIFIER LES PAIEMENTS
echo "\n2. VÉRIFICATION DES PAIEMENTS...\n";
$paiements = $apprenant->paiements()->with('module')->get();

echo "Total paiements : {$paiements->count()}\n";
$paiementsValides = $paiements->where('statut', 'valide');
echo "Paiements valides : {$paiementsValides->count()}\n";

foreach ($paiementsValides as $paiement) {
    echo "  - Module ID {$paiement->module_id} : {$paiement->module->titre} (Niveau: {$paiement->module->niveau->nom})\n";
}

$moduleIds = $paiementsValides->pluck('module_id')->toArray();
echo "Modules payés (IDs) : " . implode(', ', $moduleIds) . "\n";

// 3. VÉRIFIER LES QUESTIONNAIRES
echo "\n3. VÉRIFICATION DES QUESTIONNAIRES...\n";
$questionnaires = Questionnaire::with(['module.niveau'])->get();

echo "Total questionnaires : {$questionnaires->count()}\n";
echo "Questionnaires envoyés : " . $questionnaires->where('envoye', true)->count() . "\n";
echo "Questionnaires non envoyés : " . $questionnaires->where('envoye', false)->count() . "\n";

// 4. VÉRIFIER LES QUESTIONNAIRES PAR MODULE
echo "\n4. QUESTIONNAIRES PAR MODULE PAYÉ...\n";

foreach ($moduleIds as $moduleId) {
    $module = Module::with('niveau')->find($moduleId);
    echo "\nModule ID {$moduleId} : {$module->titre} (Niveau: {$module->niveau->nom})\n";
    
    $questionnairesModule = Questionnaire::where('module_id', $moduleId)->get();
    echo "  Questionnaires pour ce module : {$questionnairesModule->count()}\n";
    
    foreach ($questionnairesModule as $q) {
        echo "    - ID {$q->id} : '{$q->titre}' - Envoyé: " . ($q->envoye ? 'Oui' : 'Non') . " - Date: {$q->date_envoi}\n";
    }
}

// 5. VÉRIFIER LA LOGIQUE D'AFFICHAGE
echo "\n5. VÉRIFICATION DE LA LOGIQUE D'AFFICHAGE...\n";

// Questionnaires pour les modules payés
$questionnairesModulesPayes = Questionnaire::whereIn('module_id', $moduleIds)->get();
echo "Questionnaires pour modules payés : {$questionnairesModulesPayes->count()}\n";

// Questionnaires envoyés
$questionnairesEnvoyes = $questionnairesModulesPayes->where('envoye', true);
echo "Questionnaires envoyés : {$questionnairesEnvoyes->count()}\n";

// Questionnaires avec date passée
$questionnairesDatePassee = $questionnairesEnvoyes->filter(function($q) {
    return $q->date_envoi <= Carbon::now();
});
echo "Questionnaires avec date passée : {$questionnairesDatePassee->count()}\n";

// Questionnaires du bon niveau
$questionnairesBonNiveau = $questionnairesDatePassee->filter(function($q) use ($apprenant) {
    return $q->module->niveau_id == $apprenant->niveau_id;
});
echo "Questionnaires du bon niveau : {$questionnairesBonNiveau->count()}\n";

foreach ($questionnairesBonNiveau as $q) {
    echo "  - '{$q->titre}' (Module: {$q->module->titre}, Niveau: {$q->module->niveau->nom})\n";
}

// 6. SIMULER LA REQUÊTE EXACTE DU CONTRÔLEUR
echo "\n6. SIMULATION DE LA REQUÊTE DU CONTRÔLEUR...\n";

$questionnairesVisibles = Questionnaire::with(['module.niveau'])
    ->whereIn('module_id', $moduleIds)
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id);
    })
    ->where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->get();

echo "Résultat de la requête du contrôleur : {$questionnairesVisibles->count()} questionnaires\n";

foreach ($questionnairesVisibles as $q) {
    echo "  - '{$q->titre}' (Module: {$q->module->titre}, Niveau: {$q->module->niveau->nom})\n";
}

// 7. CRÉER UN QUESTIONNAIRE DE TEST SI AUCUN N'EST DISPONIBLE
if ($questionnairesVisibles->count() === 0) {
    echo "\n7. CRÉATION D'UN QUESTIONNAIRE DE TEST...\n";
    
    $moduleTest = Module::whereIn('id', $moduleIds)->first();
    if ($moduleTest) {
        echo "Module de test : {$moduleTest->titre}\n";
        
        // Supprimer les anciens questionnaires de test
        Questionnaire::where('titre', 'LIKE', '%DIAGNOSTIC%')->delete();
        
        // Créer un questionnaire de test
        $questionnaire = Questionnaire::create([
            'titre' => 'Questionnaire de diagnostic - TEST',
            'description' => 'Questionnaire créé pour le diagnostic',
            'module_id' => $moduleTest->id,
            'date_envoi' => Carbon::now()->subMinutes(5),
            'envoye' => true,
            'duree' => 30,
            'points_totaux' => 10
        ]);
        
        echo "✅ Questionnaire de test créé (ID: {$questionnaire->id})\n";
        
        // Vérifier à nouveau
        $questionnairesVisibles = Questionnaire::with(['module.niveau'])
            ->whereIn('module_id', $moduleIds)
            ->whereHas('module', function($q) use ($apprenant) {
                $q->where('niveau_id', $apprenant->niveau_id);
            })
            ->where('envoye', true)
            ->where('date_envoi', '<=', Carbon::now())
            ->get();
        
        echo "Questionnaires visibles après création : {$questionnairesVisibles->count()}\n";
    }
}

// 8. INSTRUCTIONS FINALES
echo "\n8. INSTRUCTIONS POUR RÉSOUDRE LE PROBLÈME :\n";
echo "=============================================\n";

if ($questionnairesVisibles->count() > 0) {
    echo "✅ SUCCÈS ! Les questionnaires sont disponibles.\n";
    echo "Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
    echo "Connectez-vous en tant qu'apprenant\n";
} else {
    echo "❌ PROBLÈME IDENTIFIÉ :\n";
    echo "1. Vérifiez que l'apprenant a des modules payés\n";
    echo "2. Vérifiez que les modules correspondent au niveau de l'apprenant\n";
    echo "3. Vérifiez que les questionnaires ont envoye = true\n";
    echo "4. Vérifiez que les dates d'envoi sont dans le passé\n";
}

echo "\n=== DIAGNOSTIC TERMINÉ ===\n"; 
<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Apprenant;
use App\Models\Questionnaire;
use App\Models\Inscription;
use App\Models\Module;
use App\Models\Niveau;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC APPRENANT TEST ===\n\n";

// 1. Vérifier les apprenants existants
echo "1. Apprenants existants :\n";
$apprenants = Apprenant::with(['utilisateur', 'niveau'])->get();

foreach ($apprenants as $apprenant) {
    echo "  - ID {$apprenant->id} : {$apprenant->utilisateur->nom} (Niveau: {$apprenant->niveau->nom})\n";
}

// 2. Vérifier les inscriptions (modules payés)
echo "\n2. Inscriptions (modules payés) :\n";
$inscriptions = Inscription::with(['apprenant.utilisateur', 'module.niveau'])->get();

foreach ($inscriptions as $inscription) {
    $status = $inscription->statut === 'payé' ? '✅ Payé' : '❌ Non payé';
    echo "  - Apprenant: {$inscription->apprenant->utilisateur->nom}\n";
    echo "    Module: {$inscription->module->titre}\n";
    echo "    Niveau: {$inscription->module->niveau->nom}\n";
    echo "    Status: {$status}\n";
    echo "    ---\n";
}

// 3. Vérifier les questionnaires envoyés
echo "\n3. Questionnaires envoyés :\n";
$questionnairesEnvoyes = Questionnaire::where('envoye', true)
    ->with(['module.niveau'])
    ->get();

foreach ($questionnairesEnvoyes as $q) {
    echo "  - ID {$q->id} : '{$q->titre}'\n";
    echo "    Module: {$q->module->titre}\n";
    echo "    Niveau: {$q->module->niveau->nom}\n";
    echo "    Date d'envoi: " . Carbon::parse($q->date_envoi)->format('d/m/Y H:i') . "\n";
    echo "    Questions: {$q->questions->count()}\n";
    echo "    ---\n";
}

// 4. Simuler la logique de la page questionnaire_test
echo "\n4. Simulation de la logique questionnaire_test :\n";

// Prendre le premier apprenant comme exemple
$apprenantExemple = Apprenant::with(['utilisateur', 'niveau'])->first();

if ($apprenantExemple) {
    echo "Apprenant exemple : {$apprenantExemple->utilisateur->nom} (Niveau: {$apprenantExemple->niveau->nom})\n\n";
    
    // Vérifier ses modules payés
    $modulesPayes = Inscription::where('apprenant_id', $apprenantExemple->id)
        ->where('statut', 'payé')
        ->with(['module.niveau'])
        ->get();
    
    echo "Modules payés de cet apprenant :\n";
    foreach ($modulesPayes as $inscription) {
        echo "  - {$inscription->module->titre} (Niveau: {$inscription->module->niveau->nom})\n";
    }
    
    // Vérifier les questionnaires disponibles pour cet apprenant
    $niveauxApprenant = $modulesPayes->pluck('module.niveau_id')->unique();
    $modulesApprenant = $modulesPayes->pluck('module_id')->unique();
    
    echo "\nNiveaux de l'apprenant : " . implode(', ', $niveauxApprenant->toArray()) . "\n";
    echo "Modules de l'apprenant : " . implode(', ', $modulesApprenant->toArray()) . "\n";
    
    // Questionnaires qui devraient s'afficher
    $questionnairesDisponibles = Questionnaire::where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->whereIn('module_id', $modulesApprenant)
        ->with(['module.niveau'])
        ->get();
    
    echo "\nQuestionnaires disponibles pour cet apprenant :\n";
    if ($questionnairesDisponibles->count() > 0) {
        foreach ($questionnairesDisponibles as $q) {
            echo "  - ID {$q->id} : '{$q->titre}'\n";
            echo "    Module: {$q->module->titre}\n";
            echo "    Niveau: {$q->module->niveau->nom}\n";
            echo "    Questions: {$q->questions->count()}\n";
        }
    } else {
        echo "  ❌ Aucun questionnaire disponible !\n";
        
        // Diagnostic des raisons
        echo "\nDiagnostic des raisons :\n";
        
        // 1. Vérifier s'il y a des questionnaires envoyés
        $totalEnvoyes = Questionnaire::where('envoye', true)->count();
        echo "  - Questionnaires envoyés total : {$totalEnvoyes}\n";
        
        // 2. Vérifier s'il y a des questionnaires pour ses modules
        $questionnairesPourModules = Questionnaire::whereIn('module_id', $modulesApprenant)->count();
        echo "  - Questionnaires pour ses modules : {$questionnairesPourModules}\n";
        
        // 3. Vérifier s'il y a des questionnaires envoyés pour ses modules
        $questionnairesEnvoyesPourModules = Questionnaire::where('envoye', true)
            ->whereIn('module_id', $modulesApprenant)
            ->count();
        echo "  - Questionnaires envoyés pour ses modules : {$questionnairesEnvoyesPourModules}\n";
        
        // 4. Vérifier les niveaux
        echo "  - Niveau de l'apprenant : {$apprenantExemple->niveau->nom}\n";
        $questionnairesPourNiveau = Questionnaire::where('envoye', true)
            ->whereHas('module', function($query) use ($niveauxApprenant) {
                $query->whereIn('niveau_id', $niveauxApprenant);
            })
            ->count();
        echo "  - Questionnaires envoyés pour son niveau : {$questionnairesPourNiveau}\n";
    }
} else {
    echo "❌ Aucun apprenant trouvé !\n";
}

// 5. Créer un questionnaire de test pour l'apprenant
echo "\n5. Création d'un questionnaire de test :\n";

if ($apprenantExemple && $modulesPayes->count() > 0) {
    $moduleTest = $modulesPayes->first()->module;
    
    // Vérifier s'il existe déjà un questionnaire pour ce module
    $questionnaireExistant = Questionnaire::where('module_id', $moduleTest->id)
        ->where('envoye', true)
        ->first();
    
    if (!$questionnaireExistant) {
        echo "Création d'un questionnaire de test pour le module '{$moduleTest->titre}'...\n";
        
        $questionnaire = Questionnaire::create([
            'titre' => 'Questionnaire de test - ' . $moduleTest->titre,
            'module_id' => $moduleTest->id,
            'user_id' => 1, // Admin
            'date_envoi' => Carbon::now()->subMinutes(5), // 5 minutes dans le passé
            'envoye' => true,
            'type_devoir' => 'hebdomadaire',
            'semaine' => 1,
            'minutes' => 30
        ]);
        
        echo "✅ Questionnaire créé avec l'ID : {$questionnaire->id}\n";
        echo "   - Titre : {$questionnaire->titre}\n";
        echo "   - Module : {$moduleTest->titre}\n";
        echo "   - Date d'envoi : " . Carbon::parse($questionnaire->date_envoi)->format('d/m/Y H:i') . "\n";
        echo "   - Status : Envoyé\n";
    } else {
        echo "✅ Questionnaire existant trouvé : ID {$questionnaireExistant->id}\n";
    }
} else {
    echo "❌ Impossible de créer un questionnaire de test (pas d'apprenant ou pas de modules payés)\n";
}

echo "\n=== DIAGNOSTIC TERMINÉ ===\n";
echo "Vérifiez maintenant la page : http://127.0.0.1:8000/questionnaire_test\n"; 
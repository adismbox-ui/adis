<?php

require_once 'vendor/autoload.php';

use App\Models\Apprenant;
use App\Models\Utilisateur;
use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST AUTHENTIFICATION ET SESSION ===\n\n";

// 1. Vérifier les utilisateurs existants
echo "1. Utilisateurs existants :\n";
$users = Utilisateur::with(['apprenant.niveau'])->get();

foreach ($users as $user) {
    $type = $user->apprenant ? 'Apprenant' : 'Admin';
    $niveau = $user->apprenant ? $user->apprenant->niveau->nom : 'N/A';
    echo "  - ID {$user->id} : {$user->nom} ({$type}, Niveau: {$niveau})\n";
}

// 2. Vérifier l'apprenant spécifique
echo "\n2. Apprenant 'qsqsqsqs' :\n";
$apprenant = Apprenant::with(['utilisateur', 'niveau'])->whereHas('utilisateur', function($q) {
    $q->where('nom', 'qsqsqsqs');
})->first();

if ($apprenant) {
    echo "  ✅ Apprenant trouvé : {$apprenant->utilisateur->name}\n";
    echo "  - Niveau : {$apprenant->niveau->nom}\n";
    echo "  - User ID : {$apprenant->utilisateur_id}\n";
    
    // 3. Vérifier ses modules payés
    $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "  - Modules payés : " . implode(', ', $modulesPayes) . "\n";
    
    // 4. Vérifier les questionnaires disponibles
    $questionnaires = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $modulesPayes)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', true)
        ->where('date_envoi', '<=', Carbon::now())
        ->get();
    
    echo "  - Questionnaires disponibles : {$questionnaires->count()}\n";
    foreach ($questionnaires as $q) {
        echo "    * ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
    }
} else {
    echo "  ❌ Apprenant 'qsqsqsqs' non trouvé !\n";
}

// 5. Simuler la logique exacte du contrôleur
echo "\n3. Simulation exacte du contrôleur :\n";

// Simuler un utilisateur connecté
$user = Utilisateur::where('nom', 'qsqsqsqs')->first();
if ($user) {
    echo "  ✅ Utilisateur connecté : {$user->nom}\n";
    
    $apprenant = $user->apprenant;
    if ($apprenant) {
        echo "  ✅ Apprenant trouvé : {$apprenant->utilisateur->nom}\n";
        
        // Logique exacte du contrôleur
        $moduleIds = [];
        $inscritModuleIds = [];
        
        // Modules payés uniquement
        $modulesPayes = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
        $moduleIds = array_unique($modulesPayes);
        $inscritModuleIds = $moduleIds;
        
        echo "  - Modules payés : " . implode(', ', $moduleIds) . "\n";
        
        // Si aucun module payé, pas de questionnaire
        $questionnaires = collect();
        if (!empty($moduleIds) && $apprenant && $apprenant->niveau_id) {
            $questionnaires = Questionnaire::with(['module.niveau'])
                ->whereIn('module_id', $moduleIds)
                ->whereHas('module', function($q) use ($apprenant) {
                    $q->where('niveau_id', $apprenant->niveau_id);
                })
                ->where('envoye', true)
                ->where('date_envoi', '<=', Carbon::now())
                ->get();
        }
        
        echo "  - Questionnaires retournés : {$questionnaires->count()}\n";
        foreach ($questionnaires as $q) {
            echo "    * ID {$q->id} : '{$q->titre}' (Module: {$q->module->titre})\n";
        }
        
        // 6. Vérifier les conditions une par une
        echo "\n4. Vérification des conditions :\n";
        echo "  - moduleIds vide ? " . (empty($moduleIds) ? 'Oui' : 'Non') . "\n";
        echo "  - apprenant existe ? " . ($apprenant ? 'Oui' : 'Non') . "\n";
        echo "  - niveau_id existe ? " . ($apprenant->niveau_id ? 'Oui' : 'Non') . "\n";
        echo "  - Toutes conditions remplies ? " . ((!empty($moduleIds) && $apprenant && $apprenant->niveau_id) ? 'Oui' : 'Non') . "\n";
        
    } else {
        echo "  ❌ L'utilisateur n'est pas un apprenant !\n";
    }
} else {
    echo "  ❌ Utilisateur 'qsqsqsqs' non trouvé !\n";
}

// 7. Instructions pour tester
echo "\n5. INSTRUCTIONS POUR TESTER :\n";
echo "===============================\n";
echo "1. Assurez-vous d'être connecté avec l'utilisateur 'qsqsqsqs'\n";
echo "2. Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
echo "3. Si aucun questionnaire n'apparaît, vérifiez :\n";
echo "   - Que vous êtes bien connecté\n";
echo "   - Que l'utilisateur est bien un apprenant\n";
echo "   - Que les modules sont payés\n";
echo "   - Que les questionnaires sont envoyés et à la bonne date\n";

echo "\n=== TEST TERMINÉ ===\n"; 
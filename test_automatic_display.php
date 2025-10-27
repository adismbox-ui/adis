<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Niveau;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test du système de programmation automatique ===\n\n";

// 1. Envoyer le questionnaire en attente
echo "1. Envoi du questionnaire en attente...\n";
$questionnaireEnAttente = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->first();

if ($questionnaireEnAttente) {
    echo "Questionnaire trouvé : {$questionnaireEnAttente->titre}\n";
    $questionnaireEnAttente->update(['envoye' => true]);
    echo "✅ Questionnaire marqué comme envoyé\n";
} else {
    echo "Aucun questionnaire en attente\n";
}

// 2. Créer un nouveau questionnaire programmé pour dans 2 minutes
echo "\n2. Création d'un questionnaire programmé pour dans 2 minutes...\n";

// Trouver un module et niveau existants
$module = Module::first();
$niveau = Niveau::first();

if ($module && $niveau) {
    $dateEnvoi = Carbon::now()->addMinutes(2);
    
    $questionnaire = Questionnaire::create([
        'titre' => 'Questionnaire Test - Programmation Automatique',
        'description' => 'Ce questionnaire s\'affichera automatiquement dans 2 minutes',
        'module_id' => $module->id,
        'niveau_id' => $niveau->id,
        'session_id' => 1,
        'date_envoi' => $dateEnvoi,
        'envoye' => false,
        'minutes' => 30,
        'semaine' => 1,
        'type_devoir' => 'hebdomadaire',
        'user_id' => 1
    ]);
    
    // Créer quelques questions
    $questions = [
        [
            'texte' => 'Quelle est la capitale de la France ?',
            'choix' => ['Paris', 'Londres', 'Berlin', 'Madrid'],
            'bonne_reponse' => 'Paris',
            'points' => 1
        ],
        [
            'texte' => 'Combien font 2 + 2 ?',
            'choix' => ['3', '4', '5', '6'],
            'bonne_reponse' => '4',
            'points' => 1
        ]
    ];
    
    foreach ($questions as $questionData) {
        $questionnaire->questions()->create($questionData);
    }
    
    echo "✅ Questionnaire créé avec succès !\n";
    echo "   ID : {$questionnaire->id}\n";
    echo "   Titre : {$questionnaire->titre}\n";
    echo "   Date d'envoi : {$dateEnvoi->format('Y-m-d H:i:s')}\n";
    echo "   Module : {$module->titre}\n";
    echo "   Niveau : {$niveau->nom}\n";
    
} else {
    echo "❌ Impossible de créer le questionnaire (module ou niveau manquant)\n";
}

// 3. Vérifier les questionnaires disponibles pour un apprenant
echo "\n3. Vérification des questionnaires disponibles...\n";
$apprenant = Apprenant::with(['utilisateur', 'paiements'])->first();

if ($apprenant) {
    echo "Apprenant : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    echo "Niveau : {$apprenant->niveau->nom}\n";
    
    // Modules payés
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "Modules payés : " . implode(', ', $moduleIds) . "\n";
    
    // Questionnaires disponibles (envoyés et à la bonne date)
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
        echo "  - ID {$q->id} : {$q->titre} (Module: {$q->module->titre})\n";
    }
    
    // Questionnaires programmés (pas encore envoyés)
    $questionnairesProgrammes = Questionnaire::with(['module.niveau'])
        ->whereIn('module_id', $moduleIds)
        ->whereHas('module', function($q) use ($apprenant) {
            $q->where('niveau_id', $apprenant->niveau_id);
        })
        ->where('envoye', false)
        ->where('date_envoi', '>', Carbon::now())
        ->get();
    
    echo "Questionnaires programmés : {$questionnairesProgrammes->count()}\n";
    foreach ($questionnairesProgrammes as $q) {
        $dateEnvoi = Carbon::parse($q->date_envoi);
        $minutesRestantes = $dateEnvoi->diffInMinutes(Carbon::now());
        echo "  - ID {$q->id} : {$q->titre} (Envoi dans {$minutesRestantes} minutes)\n";
    }
    
} else {
    echo "❌ Aucun apprenant trouvé\n";
}

// 4. Instructions pour tester
echo "\n4. Instructions pour tester le système :\n";
echo "==========================================\n";
echo "1. Allez sur : http://127.0.0.1:8000/questionnaire_test\n";
echo "2. Connectez-vous en tant qu'apprenant\n";
echo "3. Vérifiez que les questionnaires s'affichent\n";
echo "4. Attendez 2 minutes pour voir le nouveau questionnaire apparaître\n";
echo "\n5. Pour créer un nouveau questionnaire programmé :\n";
echo "   - Allez sur : http://127.0.0.1:8000/questionnaires/create\n";
echo "   - Remplissez les informations\n";
echo "   - Dans 'Programmation automatique', définissez une date/heure future\n";
echo "   - Créez le questionnaire\n";
echo "   - Il s'affichera automatiquement à l'heure programmée\n";

echo "\n=== Test terminé ===\n"; 
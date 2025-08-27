<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Question;
use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Diagnostic et correction des choix de questions ===\n\n";

// 1. Vérifier toutes les questions
echo "1. Vérification de toutes les questions...\n";
$questions = Question::all();
$problemes = [];

foreach ($questions as $question) {
    $choix = $question->choix;
    $type = gettype($choix);
    $probleme = false;
    $details = [];
    
    if (!is_array($choix)) {
        $probleme = true;
        $details[] = "Type incorrect: {$type} (attendu: array)";
    } elseif (empty($choix)) {
        $probleme = true;
        $details[] = "Tableau vide";
    } elseif (count($choix) < 2) {
        $probleme = true;
        $details[] = "Moins de 2 choix: " . count($choix);
    }
    
    if ($probleme) {
        $problemes[] = [
            'id' => $question->id,
            'questionnaire_id' => $question->questionnaire_id,
            'texte' => substr($question->texte, 0, 50) . '...',
            'choix' => $choix,
            'type' => $type,
            'details' => $details
        ];
    }
}

echo "Questionnaires trouvés : {$questions->count()}\n";
echo "Problèmes détectés : " . count($problemes) . "\n\n";

if (count($problemes) > 0) {
    echo "2. Détails des problèmes :\n";
    foreach ($problemes as $probleme) {
        echo "- Question ID {$probleme['id']} (Questionnaire {$probleme['questionnaire_id']})\n";
        echo "  Texte : {$probleme['texte']}\n";
        echo "  Type : {$probleme['type']}\n";
        echo "  Contenu : " . json_encode($probleme['choix']) . "\n";
        foreach ($probleme['details'] as $detail) {
            echo "  Problème : {$detail}\n";
        }
        echo "\n";
    }
    
    // 3. Tentative de correction
    echo "3. Tentative de correction...\n";
    $corrigees = 0;
    
    foreach ($problemes as $probleme) {
        $question = Question::find($probleme['id']);
        if (!$question) continue;
        
        $choix = $question->choix;
        $nouveauxChoix = [];
        
        // Si c'est une chaîne, essayer de la décoder
        if (is_string($choix)) {
            $decoded = json_decode($choix, true);
            if (is_array($decoded)) {
                $nouveauxChoix = $decoded;
            } else {
                // Essayer de séparer par des points-virgules
                $nouveauxChoix = array_map('trim', explode(';', $choix));
            }
        } elseif (is_array($choix)) {
            $nouveauxChoix = $choix;
        }
        
        // Nettoyer les choix vides
        $nouveauxChoix = array_filter($nouveauxChoix, function($choix) {
            return !empty(trim($choix));
        });
        
        // Si on a au moins 2 choix valides, mettre à jour
        if (count($nouveauxChoix) >= 2) {
            $question->update(['choix' => array_values($nouveauxChoix)]);
            echo "✅ Question {$question->id} corrigée : " . count($nouveauxChoix) . " choix\n";
            $corrigees++;
        } else {
            echo "❌ Question {$question->id} : impossible de corriger (choix insuffisants)\n";
        }
    }
    
    echo "\nCorrections effectuées : {$corrigees}\n";
} else {
    echo "✅ Aucun problème détecté !\n";
}

// 4. Vérifier un questionnaire spécifique
echo "\n4. Vérification d'un questionnaire spécifique...\n";
$questionnaireId = 21; // Le questionnaire mentionné dans l'erreur
$questionnaire = Questionnaire::with('questions')->find($questionnaireId);

if ($questionnaire) {
    echo "Questionnaire {$questionnaireId} : {$questionnaire->titre}\n";
    echo "Nombre de questions : {$questionnaire->questions->count()}\n\n";
    
    foreach ($questionnaire->questions as $index => $question) {
        echo "Question " . ($index + 1) . " (ID: {$question->id}):\n";
        echo "  Texte : " . substr($question->texte, 0, 50) . "...\n";
        echo "  Type de choix : " . gettype($question->choix) . "\n";
        echo "  Nombre de choix : " . (is_array($question->choix) ? count($question->choix) : 'N/A') . "\n";
        echo "  Choix : " . json_encode($question->choix) . "\n";
        echo "  Bonne réponse : {$question->bonne_reponse}\n\n";
    }
} else {
    echo "❌ Questionnaire {$questionnaireId} non trouvé\n";
}

echo "\n=== Diagnostic terminé ===\n"; 
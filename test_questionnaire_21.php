<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Question;
use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test spécifique du questionnaire 21 ===\n\n";

// 1. Récupérer le questionnaire 21
$questionnaire = Questionnaire::with('questions')->find(21);

if (!$questionnaire) {
    echo "❌ Questionnaire 21 non trouvé\n";
    exit;
}

echo "📋 Questionnaire 21 : {$questionnaire->titre}\n";
echo "📝 Description : {$questionnaire->description}\n";
echo "📚 Module : {$questionnaire->module->titre}\n";
echo "🎓 Niveau : {$questionnaire->module->niveau->nom}\n";
echo "📅 Date d'envoi : {$questionnaire->date_envoi}\n";
echo "✅ Envoyé : " . ($questionnaire->envoye ? 'Oui' : 'Non') . "\n";
echo "❓ Nombre de questions : {$questionnaire->questions->count()}\n\n";

// 2. Analyser chaque question
echo "2. Analyse des questions :\n";
$problemes = [];

foreach ($questionnaire->questions as $index => $question) {
    echo "\n--- Question " . ($index + 1) . " (ID: {$question->id}) ---\n";
    echo "Texte : {$question->texte}\n";
    echo "Type de choix : " . gettype($question->choix) . "\n";
    echo "Nombre de choix : " . (is_array($question->choix) ? count($question->choix) : 'N/A') . "\n";
    echo "Choix : " . json_encode($question->choix) . "\n";
    echo "Bonne réponse : {$question->bonne_reponse}\n";
    echo "Points : {$question->points}\n";
    
    // Vérifier les problèmes
    $probleme = false;
    $details = [];
    
    if (!is_array($question->choix)) {
        $probleme = true;
        $details[] = "Type incorrect: " . gettype($question->choix) . " (attendu: array)";
    } elseif (empty($question->choix)) {
        $probleme = true;
        $details[] = "Tableau vide";
    } elseif (count($question->choix) < 2) {
        $probleme = true;
        $details[] = "Moins de 2 choix: " . count($question->choix);
    }
    
    if ($probleme) {
        $problemes[] = [
            'id' => $question->id,
            'index' => $index + 1,
            'details' => $details
        ];
        echo "❌ PROBLÈME DÉTECTÉ :\n";
        foreach ($details as $detail) {
            echo "  - {$detail}\n";
        }
    } else {
        echo "✅ Question valide\n";
    }
}

// 3. Tentative de correction
if (count($problemes) > 0) {
    echo "\n3. Tentative de correction des problèmes...\n";
    
    foreach ($problemes as $probleme) {
        $question = Question::find($probleme['id']);
        if (!$question) continue;
        
        echo "\nCorrection de la question {$probleme['index']} (ID: {$question->id})...\n";
        
        $choix = $question->choix;
        $nouveauxChoix = [];
        
        // Si c'est une chaîne, essayer de la décoder
        if (is_string($choix)) {
            echo "  Type actuel : string\n";
            $decoded = json_decode($choix, true);
            if (is_array($decoded)) {
                $nouveauxChoix = $decoded;
                echo "  Décodage JSON réussi : " . count($nouveauxChoix) . " choix\n";
            } else {
                // Essayer de séparer par des points-virgules
                $nouveauxChoix = array_map('trim', explode(';', $choix));
                echo "  Séparation par points-virgules : " . count($nouveauxChoix) . " choix\n";
            }
        } elseif (is_array($choix)) {
            $nouveauxChoix = $choix;
            echo "  Type actuel : array\n";
        }
        
        // Nettoyer les choix vides
        $nouveauxChoix = array_filter($nouveauxChoix, function($choix) {
            return !empty(trim($choix));
        });
        
        echo "  Choix après nettoyage : " . count($nouveauxChoix) . "\n";
        echo "  Nouveaux choix : " . json_encode(array_values($nouveauxChoix)) . "\n";
        
        // Si on a au moins 2 choix valides, mettre à jour
        if (count($nouveauxChoix) >= 2) {
            $question->update(['choix' => array_values($nouveauxChoix)]);
            echo "  ✅ Question corrigée\n";
        } else {
            echo "  ❌ Impossible de corriger (choix insuffisants)\n";
        }
    }
} else {
    echo "\n✅ Aucun problème détecté !\n";
}

// 4. Vérification finale
echo "\n4. Vérification finale...\n";
$questionnaire->refresh();
$questionnaire->load('questions');

foreach ($questionnaire->questions as $index => $question) {
    echo "Question " . ($index + 1) . " : ";
    if (is_array($question->choix) && count($question->choix) >= 2) {
        echo "✅ Valide (" . count($question->choix) . " choix)\n";
    } else {
        echo "❌ Problème persistant\n";
    }
}

echo "\n=== Test terminé ===\n"; 
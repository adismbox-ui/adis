<?php
// Script pour corriger les colonnes 'choix' mal formatées dans la table 'questions' (Laravel classique)

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Question;

// Récupérer toutes les questions
$questions = Question::all();

foreach ($questions as $question) {
    $choix = $question->choix;
    if (is_string($choix)) {
        $decoded = json_decode($choix, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // On suppose que c'est une liste séparée par des virgules
            $asArray = array_map('trim', explode(',', $choix));
            $question->choix = $asArray;
            $question->save();
            echo "Question ID {$question->id} corrigée (CSV): ".json_encode($asArray, JSON_UNESCAPED_UNICODE)."\n";
        } else {
            // C'est un JSON valide mais stocké comme string, on force la conversion
            $question->choix = $decoded;
            $question->save();
            echo "Question ID {$question->id} corrigée (JSON): ".json_encode($decoded, JSON_UNESCAPED_UNICODE)."\n";
        }
    }
}

// Afficher les questions problématiques restantes
$problems = false;
foreach ($questions as $question) {
    if (!is_array($question->choix)) {
        echo "[PROBLEME] ID: {$question->id} => ".var_export($question->choix, true)."\n";
        $problems = true;
    }
}
if (!$problems) {
    echo "Toutes les questions ont un champ 'choix' valide (array).\n";
}
echo "Correction terminée.\n";
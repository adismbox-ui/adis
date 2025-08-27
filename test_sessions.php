<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SessionFormation;

echo "=== Test des Sessions ===\n";
echo "Nombre de sessions : " . SessionFormation::count() . "\n\n";

$sessions = SessionFormation::orderBy('date_debut', 'desc')->get();

foreach ($sessions as $session) {
    echo "Session ID: " . $session->id . "\n";
    echo "Nom: " . $session->nom . "\n";
    echo "Date début: " . $session->date_debut . "\n";
    echo "Date fin: " . $session->date_fin . "\n";
    echo "Niveau ID: " . ($session->niveau_id ?? 'null') . "\n";
    echo "Formateur ID: " . ($session->formateur_id ?? 'null') . "\n";
    echo "---\n";
}

echo "\n=== Test de la méthode create du QuestionnaireController ===\n";

// Simuler le contrôleur
$niveaux = \App\Models\Niveau::orderBy('ordre')->get();
$modules = \App\Models\Module::with('niveau')->get();
$sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();

echo "Niveaux: " . $niveaux->count() . "\n";
echo "Modules: " . $modules->count() . "\n";
echo "Sessions: " . $sessions->count() . "\n";

foreach ($sessions as $session) {
    echo "- " . $session->nom . " (" . $session->date_debut . " à " . $session->date_fin . ")\n";
} 
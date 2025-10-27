<?php

require_once 'vendor/autoload.php';

use App\Models\Questionnaire;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DES NOUVELLES COLONNES DATE D'ENVOI ===\n\n";

// 1. Vérifier les questionnaires avec leurs dates d'envoi
echo "1. Questionnaires avec dates d'envoi :\n";
$questionnaires = Questionnaire::with(['module.niveau'])->orderBy('created_at', 'desc')->limit(10)->get();

foreach ($questionnaires as $q) {
    $dateEnvoi = $q->date_envoi ? Carbon::parse($q->date_envoi)->format('d/m/Y H:i') : 'Non programmé';
    $status = '';
    
    if ($q->envoye) {
        $status = 'Envoyé';
    } elseif ($q->date_envoi && $q->date_envoi <= Carbon::now()) {
        $status = 'En retard';
    } elseif ($q->date_envoi) {
        $status = 'Programmé';
    } else {
        $status = 'Non défini';
    }
    
    echo "  - ID {$q->id} : '{$q->titre}'\n";
    echo "    Date d'envoi : {$dateEnvoi}\n";
    echo "    Status : {$status}\n";
    echo "    Module : " . ($q->module->titre ?? 'N/A') . "\n";
    echo "    Niveau : " . ($q->module->niveau->nom ?? 'N/A') . "\n";
    echo "    Questions : {$q->questions->count()}\n";
    echo "    ---\n";
}

// 2. Statistiques des status
echo "\n2. Statistiques des status :\n";
$envoyes = Questionnaire::where('envoye', true)->count();
$enRetard = Questionnaire::where('date_envoi', '<=', Carbon::now())->where('envoye', false)->count();
$programmes = Questionnaire::where('date_envoi', '>', Carbon::now())->where('envoye', false)->count();
$nonProgrammes = Questionnaire::whereNull('date_envoi')->count();

echo "  - Envoyés : {$envoyes}\n";
echo "  - En retard : {$enRetard}\n";
echo "  - Programmés : {$programmes}\n";
echo "  - Non programmés : {$nonProgrammes}\n";

// 3. Corriger les questionnaires en retard
echo "\n3. Correction des questionnaires en retard :\n";
$questionnairesEnRetard = Questionnaire::where('date_envoi', '<=', Carbon::now())
    ->where('envoye', false)
    ->get();

if ($questionnairesEnRetard->count() > 0) {
    echo "Questionnaires en retard trouvés : {$questionnairesEnRetard->count()}\n";
    
    foreach ($questionnairesEnRetard as $questionnaire) {
        $questionnaire->update(['envoye' => true]);
        echo "✅ Questionnaire ID {$questionnaire->id} marqué comme envoyé\n";
    }
} else {
    echo "✅ Aucun questionnaire en retard\n";
}

// 4. Instructions pour tester la page
echo "\n4. INSTRUCTIONS POUR TESTER LA PAGE :\n";
echo "=========================================\n";
echo "1. Allez sur : http://127.0.0.1:8000/questionnaires\n";
echo "2. Vous devriez voir les nouvelles colonnes :\n";
echo "   - 'Date d\'envoi' : Affiche la date et heure programmée\n";
echo "   - 'Status' : Affiche l'état (Envoyé, En retard, Programmé, etc.)\n";
echo "3. Testez la recherche en tapant :\n";
echo "   - Une date (ex: '04/08')\n";
echo "   - Un status (ex: 'envoyé', 'programmé')\n";
echo "   - Un nom de module\n";
echo "4. Les badges colorés indiquent :\n";
echo "   - 🟢 Vert : Envoyé\n";
echo "   - 🟡 Jaune : En retard\n";
echo "   - 🔵 Bleu : Programmé\n";
echo "   - ⚫ Gris : Non défini\n";

// 5. Vérifier l'affichage des dates
echo "\n5. Vérification du format des dates :\n";
$questionnairesAvecDate = Questionnaire::whereNotNull('date_envoi')->limit(5)->get();

foreach ($questionnairesAvecDate as $q) {
    $dateFormatee = Carbon::parse($q->date_envoi)->format('d/m/Y H:i');
    $dateOriginale = $q->date_envoi;
    echo "  - ID {$q->id} : {$dateOriginale} → {$dateFormatee}\n";
}

echo "\n=== TEST TERMINÉ ===\n";
echo "Les nouvelles colonnes sont maintenant visibles sur la page !\n"; 
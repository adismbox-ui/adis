<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST DE L'INTERFACE WEB ===\n\n";

// 1. Simuler la méthode create du QuestionnaireController
echo "1. Simulation de la méthode create du QuestionnaireController :\n";

$niveaux = \App\Models\Niveau::orderBy('ordre')->get();
$modules = \App\Models\Module::with('niveau')->get();
$sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();

echo "   ✅ Niveaux récupérés : {$niveaux->count()}\n";
echo "   ✅ Modules récupérés : {$modules->count()}\n";
echo "   ✅ Sessions récupérées : {$sessions->count()}\n";

echo "\n2. Sessions disponibles pour l'interface :\n";
foreach ($sessions as $session) {
    echo "   - ID: {$session->id} | Nom: {$session->nom} | Début: {$session->date_debut} | Fin: {$session->date_fin}\n";
}

// 3. Test du calcul de date pour chaque session
echo "\n3. Test du calcul de date pour chaque session :\n";
foreach ($sessions as $session) {
    echo "\n   Session : {$session->nom}\n";
    $debut = \Carbon\Carbon::parse($session->date_debut);
    
    for ($semaine = 1; $semaine <= 3; $semaine++) {
        // Calculer le premier dimanche après la date de début
        $day = $debut->dayOfWeek;
        $daysUntilSunday = (7 - $day) % 7;
        $premierDimanche = $debut->copy()->addDays($daysUntilSunday);
        
        // Ajouter (semaine - 1) * 7 jours
        $dateEnvoi = $premierDimanche->copy()->addDays(($semaine - 1) * 7);
        $dateEnvoi->setTime(13, 0, 0);
        
        echo "     Semaine {$semaine} : {$dateEnvoi->format('Y-m-d H:i:s')}\n";
    }
}

// 4. Vérifier les questionnaires programmés
echo "\n4. Questionnaires programmés en base :\n";
$questionnairesProgrammes = \App\Models\Questionnaire::where('envoye', false)
    ->whereNotNull('session_id')
    ->with(['session', 'module', 'niveau'])
    ->get();

if ($questionnairesProgrammes->isEmpty()) {
    echo "   Aucun questionnaire programmé trouvé.\n";
} else {
    foreach ($questionnairesProgrammes as $q) {
        echo "   - {$q->titre} (Session: {$q->session->nom}, Semaine: {$q->semaine}, Envoi: {$q->date_envoi})\n";
    }
}

// 5. Test de l'URL de création
echo "\n5. Test de l'URL de création :\n";
echo "   URL à tester : http://127.0.0.1:8000/questionnaires/create\n";
echo "   URL assistant : http://127.0.0.1:8000/assistant/questionnaires/create\n";

echo "\n6. Instructions pour tester l'interface :\n";
echo "   1. Démarrez le serveur : php artisan serve\n";
echo "   2. Allez sur : http://127.0.0.1:8000/questionnaires/create\n";
echo "   3. Vous devriez voir les sessions dans le dropdown\n";
echo "   4. Sélectionnez une session et une semaine\n";
echo "   5. La date d'envoi sera calculée automatiquement\n";

echo "\n🎉 TEST DE L'INTERFACE TERMINÉ !\n";
echo "Le système est prêt pour les tests web.\n";

echo "\n=== FIN DU TEST ===\n"; 
<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Notification;
use App\Models\Apprenant;
use App\Models\Questionnaire;
use App\Models\Document;
use App\Models\Module;
use App\Models\Niveau;
use Carbon\Carbon;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Création de notifications de test ===\n\n";

// 1. Récupérer un apprenant
$apprenant = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->first();

if (!$apprenant) {
    echo "❌ Aucun apprenant trouvé\n";
    exit;
}

$user = $apprenant->utilisateur;
echo "Apprenant : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
echo "Niveau : {$apprenant->niveau->nom}\n";

// 2. Supprimer les anciennes notifications de test
Notification::where('user_id', $user->id)
    ->whereIn('type', ['questionnaire', 'document', 'questionnaire_upcoming'])
    ->delete();

echo "✅ Anciennes notifications de test supprimées\n";

// 3. Créer des notifications de test
echo "\n3. Création de notifications de test...\n";

// Notification 1 : Nouveau questionnaire
Notification::createNotification([
    'type' => 'questionnaire',
    'title' => 'Nouveau questionnaire disponible',
    'message' => 'Un nouveau questionnaire "Test de grammaire arabe" est disponible pour le module Langue Arabe',
    'icon' => 'fas fa-question-circle',
    'color' => 'success',
    'user_id' => $user->id,
    'data' => [
        'questionnaire_id' => 1,
        'module_id' => 1,
        'niveau_id' => $apprenant->niveau_id
    ],
    'action_url' => "/apprenants/questionnaires/1/repondre"
]);
echo "✅ Notification créée : Nouveau questionnaire\n";

// Notification 2 : Nouveau document
Notification::createNotification([
    'type' => 'document',
    'title' => 'Nouveau document disponible',
    'message' => 'Un nouveau document "Cours de vocabulaire - Semaine 1" est disponible pour le module Langue Arabe',
    'icon' => 'fas fa-file-alt',
    'color' => 'info',
    'user_id' => $user->id,
    'data' => [
        'document_id' => 1,
        'module_id' => 1,
        'niveau_id' => $apprenant->niveau_id
    ],
    'action_url' => "/documents/1"
]);
echo "✅ Notification créée : Nouveau document\n";

// Notification 3 : Questionnaire à venir
Notification::createNotification([
    'type' => 'questionnaire_upcoming',
    'title' => 'Questionnaire bientôt disponible',
    'message' => 'Le questionnaire "Test de compréhension" sera disponible dans 2 heure(s)',
    'icon' => 'fas fa-clock',
    'color' => 'warning',
    'user_id' => $user->id,
    'data' => [
        'questionnaire_id' => 2,
        'module_id' => 1,
        'niveau_id' => $apprenant->niveau_id,
        'available_at' => Carbon::now()->addHours(2)
    ],
    'action_url' => "/questionnaire_test"
]);
echo "✅ Notification créée : Questionnaire à venir\n";

// Notification 4 : Nouveau questionnaire (non lu)
Notification::createNotification([
    'type' => 'questionnaire',
    'title' => 'Questionnaire de révision',
    'message' => 'Un questionnaire de révision "Récapitulatif module 1" est disponible pour le module Education islamique',
    'icon' => 'fas fa-question-circle',
    'color' => 'success',
    'user_id' => $user->id,
    'is_read' => false,
    'data' => [
        'questionnaire_id' => 3,
        'module_id' => 2,
        'niveau_id' => $apprenant->niveau_id
    ],
    'action_url' => "/apprenants/questionnaires/3/repondre"
]);
echo "✅ Notification créée : Questionnaire de révision (non lu)\n";

// Notification 5 : Document important
Notification::createNotification([
    'type' => 'document',
    'title' => 'Document important disponible',
    'message' => 'Le document "Guide d\'étude complet" est maintenant disponible pour le module Education islamique',
    'icon' => 'fas fa-file-alt',
    'color' => 'info',
    'user_id' => $user->id,
    'is_read' => false,
    'data' => [
        'document_id' => 2,
        'module_id' => 2,
        'niveau_id' => $apprenant->niveau_id
    ],
    'action_url' => "/documents/2"
]);
echo "✅ Notification créée : Document important (non lu)\n";

// 4. Vérifier les notifications créées
echo "\n4. Vérification des notifications créées...\n";
$notifications = Notification::where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->get();

echo "Nombre total de notifications : {$notifications->count()}\n";
echo "Notifications non lues : " . $notifications->where('is_read', false)->count() . "\n";

foreach ($notifications as $notification) {
    echo "\n--- Notification ID {$notification->id} ---\n";
    echo "Type : {$notification->type}\n";
    echo "Titre : {$notification->title}\n";
    echo "Message : {$notification->message}\n";
    echo "Lu : " . ($notification->is_read ? 'Oui' : 'Non') . "\n";
    echo "Créée : {$notification->time_ago}\n";
    if ($notification->action_url) {
        echo "Action URL : {$notification->action_url}\n";
    }
}

// 5. Instructions pour tester
echo "\n5. Instructions pour tester :\n";
echo "================================\n";
echo "1. Allez sur : http://127.0.0.1:8000/notification_test\n";
echo "2. Connectez-vous en tant qu'apprenant\n";
echo "3. Vérifiez que les 5 notifications s'affichent\n";
echo "4. Testez les boutons 'Marquer comme lue' pour les notifications non lues\n";
echo "5. Testez les liens d'action\n";

echo "\n=== Test terminé ===\n"; 
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

echo "=== Test du système de notifications pour apprenants ===\n\n";

// 1. Vérifier les apprenants existants
echo "1. Vérification des apprenants...\n";
$apprenants = Apprenant::with(['utilisateur', 'paiements', 'niveau'])->get();

if ($apprenants->count() === 0) {
    echo "❌ Aucun apprenant trouvé\n";
    exit;
}

foreach ($apprenants as $apprenant) {
    echo "Apprenant : {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}\n";
    echo "  Email : {$apprenant->utilisateur->email}\n";
    echo "  Niveau : {$apprenant->niveau->nom}\n";
    
    // Modules payés
    $moduleIds = $apprenant->paiements()->where('statut', 'valide')->pluck('module_id')->toArray();
    echo "  Modules payés : " . implode(', ', $moduleIds) . "\n";
    
    // Notifications existantes
    $notifications = Notification::where('user_id', $apprenant->utilisateur->id)->get();
    echo "  Notifications existantes : {$notifications->count()}\n";
}

// 2. Créer des notifications de test
echo "\n2. Création de notifications de test...\n";

$apprenant = $apprenants->first();
$user = $apprenant->utilisateur;

// Supprimer les anciennes notifications de test
Notification::where('user_id', $user->id)
    ->whereIn('type', ['questionnaire', 'document', 'questionnaire_upcoming'])
    ->delete();

// Créer des notifications pour les questionnaires récents
$recentQuestionnaires = Questionnaire::where('envoye', true)
    ->where('date_envoi', '<=', Carbon::now())
    ->where('date_envoi', '>=', Carbon::now()->subDays(7))
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id);
    })
    ->whereIn('module_id', $apprenant->paiements()->where('statut', 'valide')->pluck('module_id'))
    ->limit(3)
    ->get();

foreach ($recentQuestionnaires as $questionnaire) {
    Notification::createNotification([
        'type' => 'questionnaire',
        'title' => 'Nouveau questionnaire disponible',
        'message' => "Un nouveau questionnaire '{$questionnaire->titre}' est disponible pour le module {$questionnaire->module->titre}",
        'icon' => 'fas fa-question-circle',
        'color' => 'success',
        'user_id' => $user->id,
        'data' => [
            'questionnaire_id' => $questionnaire->id,
            'module_id' => $questionnaire->module_id,
            'niveau_id' => $questionnaire->niveau_id
        ],
        'action_url' => "/apprenants/questionnaires/{$questionnaire->id}/repondre"
    ]);
    echo "✅ Notification créée pour le questionnaire : {$questionnaire->titre}\n";
}

// Créer des notifications pour les documents récents
$recentDocuments = Document::where('created_at', '>=', Carbon::now()->subDays(7))
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id);
    })
    ->whereIn('module_id', $apprenant->paiements()->where('statut', 'valide')->pluck('module_id'))
    ->limit(2)
    ->get();

foreach ($recentDocuments as $document) {
    Notification::createNotification([
        'type' => 'document',
        'title' => 'Nouveau document disponible',
        'message' => "Un nouveau document '{$document->titre}' est disponible pour le module {$document->module->titre}",
        'icon' => 'fas fa-file-alt',
        'color' => 'info',
        'user_id' => $user->id,
        'data' => [
            'document_id' => $document->id,
            'module_id' => $document->module_id,
            'niveau_id' => $document->niveau_id
        ],
        'action_url' => "/documents/{$document->id}"
    ]);
    echo "✅ Notification créée pour le document : {$document->titre}\n";
}

// Créer une notification pour un questionnaire à venir
$upcomingQuestionnaire = Questionnaire::where('envoye', false)
    ->where('date_envoi', '>', Carbon::now())
    ->where('date_envoi', '<=', Carbon::now()->addHours(24))
    ->whereHas('module', function($q) use ($apprenant) {
        $q->where('niveau_id', $apprenant->niveau_id);
    })
    ->whereIn('module_id', $apprenant->paiements()->where('statut', 'valide')->pluck('module_id'))
    ->first();

if ($upcomingQuestionnaire) {
    $hoursUntilAvailable = $upcomingQuestionnaire->date_envoi->diffInHours(Carbon::now());
    
    Notification::createNotification([
        'type' => 'questionnaire_upcoming',
        'title' => 'Questionnaire bientôt disponible',
        'message' => "Le questionnaire '{$upcomingQuestionnaire->titre}' sera disponible dans {$hoursUntilAvailable} heure(s)",
        'icon' => 'fas fa-clock',
        'color' => 'warning',
        'user_id' => $user->id,
        'data' => [
            'questionnaire_id' => $upcomingQuestionnaire->id,
            'module_id' => $upcomingQuestionnaire->module_id,
            'niveau_id' => $upcomingQuestionnaire->niveau_id,
            'available_at' => $upcomingQuestionnaire->date_envoi
        ],
        'action_url' => "/questionnaire_test"
    ]);
    echo "✅ Notification créée pour le questionnaire à venir : {$upcomingQuestionnaire->titre}\n";
}

// 3. Vérifier les notifications créées
echo "\n3. Vérification des notifications créées...\n";
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

// 4. Instructions pour tester
echo "\n4. Instructions pour tester :\n";
echo "================================\n";
echo "1. Allez sur : http://127.0.0.1:8000/notification_test\n";
echo "2. Connectez-vous en tant qu'apprenant\n";
echo "3. Vérifiez que les notifications s'affichent\n";
echo "4. Testez les boutons 'Marquer comme lue'\n";
echo "5. Testez les liens d'action\n";

echo "\n=== Test terminé ===\n"; 
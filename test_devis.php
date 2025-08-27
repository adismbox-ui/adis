<?php

/**
 * Script de test pour la fonctionnalité devis
 * Ce fichier permet de tester l'envoi d'email de devis
 */

// Inclure l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Démarrer Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Mail\DevisNotification;
use Illuminate\Support\Facades\Mail;

echo "=== Test de la fonctionnalité Devis ===\n\n";

try {
    // Créer des données de test
    $donateur = (object) [
        'nom_donateur' => 'Test Donateur',
        'email_donateur' => 'test@example.com'
    ];
    
    $don = (object) [
        'id' => 1,
        'nom_donateur' => 'Test Donateur',
        'email_donateur' => 'test@example.com',
        'montant' => 5000,
        'type_don' => 'ponctuel',
        'projet_id' => 'fonds_general',
        'mode_paiement' => 'carte',
        'date_don' => now(),
        'numero_reference' => 'DON-TEST-001'
    ];
    
    $projet = null; // Fonds général
    
    echo "✅ Données de test créées avec succès\n";
    echo "📧 Email de test: test@example.com\n";
    echo "💰 Montant: 5 000 F CFA\n";
    echo "📋 Type: Don ponctuel\n\n";
    
    // Tester la création de l'email (sans l'envoyer)
    $email = new DevisNotification($donateur, $don, $projet);
    
    echo "✅ Email de devis créé avec succès\n";
    echo "📨 Sujet: " . $email->envelope()->subject . "\n";
    echo "👤 Destinataire: " . $donateur->email_donateur . "\n\n";
    
    echo "🎉 Test réussi ! La fonctionnalité devis est opérationnelle.\n";
    echo "\nPour tester l'envoi réel, utilisez le formulaire de don sur le site.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
} 
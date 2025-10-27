<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();

use App\Mail\CoucouMail;
use Illuminate\Support\Facades\Mail;

$email = 'labilala99@gmail.com'; // Votre vraie adresse email

try {
    Mail::to($email)->send(new CoucouMail($email));
    echo "Email 'coucou' envoyé avec succès à {$email}\n";
    echo "Vérifiez votre boîte mail et le dossier SPAM.\n";
} catch (Throwable $e) {
    echo "Erreur lors de l'envoi: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} 
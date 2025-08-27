<?php
// Usage: php send_test_devis.php recipient@example.com

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$to = $argv[1] ?? config('mail.test_to') ?? config('mail.mailers.smtp.username') ?? config('mail.from.address');
if (!$to) {
    fwrite(STDERR, "Aucune adresse de réception fournie. Lancez: php send_test_devis.php votre@mail.com\n");
    exit(1);
}

$now = now();
$donateur = (object) [
    'nom_donateur'   => 'Test Donateur',
    'email_donateur' => $to,
];
$don = (object) [
    'id'               => 0,
    'nom_donateur'     => 'Test Donateur',
    'email_donateur'   => $to,
    'montant'          => 12345,
    'type_don'         => 'ponctuel',
    'projet_id'        => 'fonds_general',
    'mode_paiement'    => 'carte',
    'date_don'         => $now,
    'numero_reference' => 'DON-TEST-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
];
$projet = null; // Fonds général

try {
    \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\DevisNotification($donateur, $don, $projet));
    echo "Email de test envoyé à {$to}\n";
    echo "Vérifiez votre boîte mail et, en cas d'absence, le dossier SPAM.\n";
} catch (\Throwable $e) {
    fwrite(STDERR, "Erreur envoi: ".$e->getMessage()."\n");
    exit(2);
}
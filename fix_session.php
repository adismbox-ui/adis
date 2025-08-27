<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Changer temporairement le driver de session
config(['session.driver' => 'file']);

echo "Driver de session changé vers 'file'.\n";
echo "Vous pouvez maintenant essayer de vous connecter.\n";
echo "Identifiants admin:\n";
echo "- Email: admin@adis.com\n";
echo "- Mot de passe: password\n";
echo "\nOu utilisez les identifiants de votre admin existant.\n"; 
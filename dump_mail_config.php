<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Default mailer: ".config('mail.default')."\n\n";

echo "SMTP config:\n";
print_r(config('mail.mailers.smtp'));

echo "\nGlobal from:\n";
print_r(config('mail.from'));
<?php

echo "=== Configuration du Cron Job pour l'envoi automatique ===\n\n";

$projectPath = __DIR__;
$cronCommand = "*/5 * * * * cd {$projectPath} && php artisan content:send-scheduled >> storage/logs/cron.log 2>&1";

echo "Pour configurer l'envoi automatique, ajoutez cette ligne à votre crontab :\n\n";
echo "{$cronCommand}\n\n";

echo "Instructions :\n";
echo "1. Ouvrez votre terminal\n";
echo "2. Tapez : crontab -e\n";
echo "3. Ajoutez la ligne ci-dessus\n";
echo "4. Sauvegardez et fermez l'éditeur\n\n";

echo "Cette configuration vérifiera toutes les 5 minutes s'il y a des contenus à envoyer.\n";
echo "Les logs seront sauvegardés dans storage/logs/cron.log\n\n";

echo "Pour tester manuellement, exécutez :\n";
echo "php run_automatic_send.php\n\n";

echo "Pour voir les logs :\n";
echo "tail -f storage/logs/cron.log\n"; 
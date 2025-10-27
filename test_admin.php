<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;

echo "=== Test d'accès Admin ===\n\n";

// Vérifier si un admin existe
$admin = Utilisateur::where('type_compte', 'admin')->first();

if ($admin) {
    echo "✅ Admin trouvé:\n";
    echo "- ID: " . $admin->id . "\n";
    echo "- Nom: " . $admin->nom . " " . $admin->prenom . "\n";
    echo "- Email: " . $admin->email . "\n";
    echo "- Type: " . $admin->type_compte . "\n";
    echo "- Actif: " . ($admin->actif ? 'Oui' : 'Non') . "\n";
    echo "- Email vérifié: " . ($admin->email_verified_at ? 'Oui' : 'Non') . "\n\n";
    
    // Tester la connexion
    echo "=== Test de connexion ===\n";
    $credentials = [
        'email' => $admin->email,
        'password' => 'password' // Mot de passe par défaut
    ];
    
    if (Auth::attempt($credentials)) {
        echo "✅ Connexion réussie!\n";
        echo "Utilisateur connecté: " . Auth::user()->nom . " " . Auth::user()->prenom . "\n";
        echo "Type de compte: " . Auth::user()->type_compte . "\n";
        
        // Tester le middleware admin
        echo "\n=== Test du middleware admin ===\n";
        if (Auth::user()->type_compte === 'admin') {
            echo "✅ L'utilisateur est bien un admin\n";
            echo "✅ Le middleware admin devrait permettre l'accès\n";
        } else {
            echo "❌ L'utilisateur n'est pas un admin\n";
        }
        
        Auth::logout();
    } else {
        echo "❌ Échec de la connexion\n";
        echo "Vérifiez le mot de passe ou utilisez:\n";
        echo "php create_admin.php\n";
    }
} else {
    echo "❌ Aucun admin trouvé dans la base de données\n";
    echo "Créez un admin avec:\n";
    echo "php create_admin.php\n";
}

echo "\n=== Instructions ===\n";
echo "1. Connectez-vous sur /login avec les identifiants admin\n";
echo "2. Accédez au dashboard admin sur /admin/dashboard\n";
echo "3. Si vous avez des problèmes, vérifiez:\n";
echo "   - Que vous êtes bien connecté\n";
echo "   - Que votre utilisateur a le type_compte = 'admin'\n";
echo "   - Que la session fonctionne correctement\n"; 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use App\Models\Apprenant;
use App\Models\Formateur;
use App\Models\Module;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Certificat;
use App\Models\Document;
use App\Models\Questionnaire;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Créer un utilisateur admin par défaut
        $admin = Utilisateur::create([
            'prenom' => 'Admin',
            'nom' => 'ADIS',
            'email' => 'admin@adis.com',
            'mot_de_passe' => bcrypt('password'),
            'type_compte' => 'admin',
            'sexe' => 'Homme',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        // Créer un assistant par défaut
        $assistant = Utilisateur::create([
            'prenom' => 'Assistant',
            'nom' => 'ADIS',
            'email' => 'assistant@adis.com',
            'mot_de_passe' => bcrypt('password'),
            'type_compte' => 'assistant',
            'sexe' => 'Homme',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        // Créer quelques utilisateurs de test
        $this->call([
            // Vous pouvez ajouter d'autres seeders ici
        ]);
    }
}

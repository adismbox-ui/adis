<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppelAProjet;
use Carbon\Carbon;

class AppelAProjetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Appels en cours
        AppelAProjet::create([
            'reference' => '25PJ001',
            'intitule' => 'Fourniture de meubles scolaires',
            'domaine' => 'Mobilier',
            'date_limite_soumission' => Carbon::now()->addDays(30),
            'etat' => 'ouvert',
            'details_offre' => 'Fourniture de tables, chaises et armoires pour l\'école AL QALAM',
            'montant_estimatif' => 5000000,
        ]);

        AppelAProjet::create([
            'reference' => '25PJ002',
            'intitule' => 'Construction d\'une salle informatique',
            'domaine' => 'Construction',
            'date_limite_soumission' => Carbon::now()->addDays(45),
            'etat' => 'ouvert',
            'details_offre' => 'Construction et équipement d\'une salle informatique moderne',
            'montant_estimatif' => 15000000,
        ]);

        AppelAProjet::create([
            'reference' => '25PJ003',
            'intitule' => 'Formation en développement web',
            'domaine' => 'Formation',
            'date_limite_soumission' => Carbon::now()->addDays(20),
            'etat' => 'ouvert',
            'details_offre' => 'Formation de 20 jeunes en développement web et mobile',
            'montant_estimatif' => 8000000,
        ]);

        // Appels clôturés
        AppelAProjet::create([
            'reference' => '24PJ001',
            'intitule' => 'Fourniture de matériel de cuisine',
            'domaine' => 'Équipement',
            'date_limite_soumission' => Carbon::now()->subDays(30),
            'etat' => 'cloture',
            'details_offre' => 'Fourniture d\'équipements de cuisine pour le centre de formation',
            'montant_estimatif' => 3000000,
            'beneficiaires' => 'Centre de formation ADIS',
            'partenaire_retenu' => 'TASNIM SARL',
            'date_cloture' => Carbon::now()->subDays(15),
        ]);

        AppelAProjet::create([
            'reference' => '24PJ002',
            'intitule' => 'Installation système de sécurité',
            'domaine' => 'Sécurité',
            'date_limite_soumission' => Carbon::now()->subDays(60),
            'etat' => 'cloture',
            'details_offre' => 'Installation de caméras de surveillance et système d\'alarme',
            'montant_estimatif' => 12000000,
            'beneficiaires' => 'Complexe éducatif ADIS',
            'partenaire_retenu' => 'SECURITE PLUS',
            'date_cloture' => Carbon::now()->subDays(40),
        ]);
    }
} 
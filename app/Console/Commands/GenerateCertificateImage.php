<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;

class GenerateCertificateImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificate:generate-image {--id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a certificate image for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $certificatId = $this->option('id');
        
        $this->info("Génération de l'image du certificat ID: {$certificatId}");
        
        try {
            // Créer des données de test
            $certificat = (object) [
                'id' => $certificatId,
                'titre' => 'Certificat de Formation en Arabe',
                'date_obtention' => now(),
            ];
            
            $apprenant = (object) [
                'prenom' => 'Ahmed',
                'nom' => 'BEN ALI',
                'email' => 'ahmed.benali@example.com'
            ];
            
            $module = (object) [
                'titre' => 'Arabe Débutant',
                'niveau' => (object) ['nom' => 'Niveau 1']
            ];
            
            // Générer le HTML
            $html = view('certificats.pdf', compact('certificat', 'apprenant', 'module'))->render();
            
            // Créer le chemin de l'image
            $imagePath = storage_path('app/public/certificat_test_' . date('Y-m-d_H-i-s') . '.png');
            
            $this->info("Génération de l'image en cours...");
            
            // Générer l'image
            Browsershot::html($html)
                ->windowSize(1600, 1131)
                ->setOption('args', ['--no-sandbox', '--disable-dev-shm-usage'])
                ->waitUntilNetworkIdle()
                ->save($imagePath);
            
            $this->info("✅ Image générée avec succès !");
            $this->info("📁 Chemin: {$imagePath}");
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
            $this->error("Assurez-vous que Chrome/Chromium est installé sur votre système.");
        }
    }
}

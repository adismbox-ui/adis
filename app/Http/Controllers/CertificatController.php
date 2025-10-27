<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificat;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class CertificatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Certificat::with(['apprenant.utilisateur', 'apprenant.niveau', 'module'])
            ->orderByDesc('created_at');

        // Filtre par niveau: niveau actuel de l'apprenant OU niveau du module du certificat
        $niveauId = $request->input('niveau_id');
        if (!empty($niveauId)) {
            $query->where(function($q) use ($niveauId) {
                $q->whereHas('apprenant', function($q2) use ($niveauId) {
                    $q2->where('niveau_id', $niveauId);
                })
                ->orWhereHas('module', function($q3) use ($niveauId) {
                    $q3->where('niveau_id', $niveauId);
                });
            });
        }

        // Recherche texte: apprenant (nom, prénom, email), module (titre), certificat (titre)
        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->whereHas('apprenant.utilisateur', function($q2) use ($search) {
                    $q2->where('nom', 'like', "%$search%")
                       ->orWhere('prenom', 'like', "%$search%")
                       ->orWhere('email', 'like', "%$search%");
                })
                ->orWhereHas('module', function($q3) use ($search) {
                    $q3->where('titre', 'like', "%$search%");
                })
                ->orWhere('titre', 'like', "%$search%");
            });
        }

        $certificats = $query->paginate(20)->appends($request->query());
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();

        return view('admin.certificats.index', compact('certificats', 'niveaux', 'niveauId', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('certificats.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        Certificat::create($data);
        return redirect()->route('admin.certificats.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificat $certificat)
    {
        return view('admin.certificats.show', compact('certificat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificat $certificat)
    {
        return view('admin.certificats.edit', compact('certificat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificat $certificat)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $certificat->update($data);
        return redirect()->route('admin.certificats.index');
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(Certificat $certificat)
    {
        $certificat->delete();
        return redirect()->route('admin.certificats.index');
    }

    /**
     * Generate certificate for an apprenant by niveau
     */
    public function generateByNiveau(Request $request, $apprenantId)
    {
        $request->validate([
            'niveau_id' => 'required|exists:niveaux,id',
        ]);

        $apprenant = \App\Models\Apprenant::with('utilisateur')->findOrFail($apprenantId);
        $niveau = \App\Models\Niveau::findOrFail($request->niveau_id);

        // Check if certificate already exists for this apprenant and niveau
        $existingCertificat = Certificat::where('apprenant_id', $apprenantId)
            ->whereHas('module', function($query) use ($request) {
                $query->where('niveau_id', $request->niveau_id);
            })
            ->first();

        if ($existingCertificat) {
            return back()->with('error', 'Un certificat existe déjà pour cet apprenant et ce niveau.');
        }

        // Create certificate
        $certificat = Certificat::create([
            'apprenant_id' => $apprenantId,
            'module_id' => null, // No specific module, just niveau
            'titre' => "Certificat de niveau {$niveau->nom} - {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom}",
            'date_obtention' => now(),
            'fichier' => null, // Will be generated later if needed
        ]);

        return back()->with('success', "Certificat généré avec succès pour {$apprenant->utilisateur->prenom} {$apprenant->utilisateur->nom} - Niveau {$niveau->nom}");
    }

    /**
     * Download certificate as PDF
     */
    public function download(Certificat $certificat)
    {
        $apprenant = $certificat->apprenant->utilisateur;
        $module = $certificat->module;
        
        // Generate PDF content
        $html = view('certificats.pdf', compact('certificat', 'apprenant', 'module'))->render();
        
        // Create PDF using DomPDF or similar
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        
        $filename = "certificat_{$apprenant->prenom}_{$apprenant->nom}_" . date('Y-m-d') . ".pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Generate certificate as PNG image
     */
    public function generateCertificatImage(Certificat $certificat)
    {
        $apprenant = $certificat->apprenant->utilisateur;
        $module = $certificat->module;
        
        // Generate HTML content
        $html = view('certificats.pdf', compact('certificat', 'apprenant', 'module'))->render();
        
        // Create image path
        $imagePath = storage_path('app/public/certificat_' . $certificat->id . '_' . date('Y-m-d_H-i-s') . '.png');
        
        // Ensure storage directory exists
        if (!file_exists(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0755, true);
        }
        
        try {
            // Generate image using Browsershot
            Browsershot::html($html)
                ->windowSize(1600, 1131) // A4 landscape ratio
                ->setOption('args', ['--no-sandbox', '--disable-dev-shm-usage'])
                ->waitUntilNetworkIdle()
                ->save($imagePath);
            
            // Return the image for download
            return response()->download($imagePath)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération de l\'image: ' . $e->getMessage());
        }
    }

    /**
     * Generate certificate image using the model image as base
     */
    public function generateCertificatImageFromModel(Certificat $certificat)
    {
        $apprenant = $certificat->apprenant->utilisateur;
        $module = $certificat->module;
        
        // Path to the model image
        $modelImagePath = public_path('MODELE CERTIFICAT DE FORMATION.jpg');
        
        if (!file_exists($modelImagePath)) {
            return back()->with('error', 'Image modèle non trouvée');
        }
        
        // Create image from model
        $image = imagecreatefromjpeg($modelImagePath);
        
        if (!$image) {
            return back()->with('error', 'Impossible de charger l\'image modèle');
        }
        
        // Get image dimensions
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Define colors
        $black = imagecolorallocate($image, 0, 0, 0);
        $darkGreen = imagecolorallocate($image, 45, 95, 79); // #2d5f4f
        $gold = imagecolorallocate($image, 255, 193, 7); // #ffc107
        $white = imagecolorallocate($image, 255, 255, 255);
        $red = imagecolorallocate($image, 220, 53, 69); // #dc3545
        
        // Calculate positions based on image size
        $centerX = $width / 2;
        
        // Function to write text with built-in fonts
        $writeText = function($image, $text, $fontSize, $x, $y, $color) {
            // Use built-in font (1-5, where 5 is the largest)
            $font = 5;
            imagestring($image, $font, $x, $y, $text, $color);
        };
        
        // Function to get approximate text width for built-in fonts
        $getTextWidth = function($text, $fontSize) {
            // Approximate width for built-in fonts (roughly 10 pixels per character)
            return strlen($text) * 10;
        };
        
        // Title - "CERTIFICAT DE FORMATION" (very large and visible)
        $title = "CERTIFICAT DE FORMATION";
        $titleWidth = $getTextWidth($title, 72);
        $titleX = $centerX - ($titleWidth / 2);
        $titleY = 150;
        
        // Write title multiple times to make it bold and visible
        for ($i = 0; $i < 3; $i++) {
            $writeText($image, $title, 72, $titleX + $i, $titleY + $i, $darkGreen);
        }
        
        // Apprenant name (very large and visible)
        $apprenantName = strtoupper($apprenant->prenom . ' ' . $apprenant->nom);
        $nameWidth = $getTextWidth($apprenantName, 60);
        $nameX = $centerX - ($nameWidth / 2);
        $nameY = 300;
        
        // Write apprenant name multiple times to make it bold
        for ($i = 0; $i < 3; $i++) {
            $writeText($image, $apprenantName, 60, $nameX + $i, $nameY + $i, $black);
        }
        
        // Module/Niveau information
        $moduleInfo = $module ? $module->titre : ($certificat->titre ?? 'Formation complétée');
        $moduleWidth = $getTextWidth($moduleInfo, 36);
        $moduleX = $centerX - ($moduleWidth / 2);
        $moduleY = 400;
        
        // Write module info
        $writeText($image, $moduleInfo, 36, $moduleX, $moduleY, $darkGreen);
        
        // Date
        $date = 'Date: ' . \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y');
        $dateWidth = $getTextWidth($date, 28);
        $dateX = $centerX - ($dateWidth / 2);
        $dateY = 470;
        
        // Write date
        $writeText($image, $date, 28, $dateX, $dateY, $black);
        
        // Signature line
        $signature = "Signature du superviseur";
        $signatureWidth = $getTextWidth($signature, 24);
        $signatureX = $width - 350;
        $signatureY = $height - 150;
        
        // Write signature
        $writeText($image, $signature, 24, $signatureX, $signatureY, $black);
        
        // Certificate number
        $certNumber = "Certificat N°: " . str_pad($certificat->id, 6, '0', STR_PAD_LEFT);
        $certNumberX = 50;
        $certNumberY = 50;
        
        // Write certificate number
        $writeText($image, $certNumber, 20, $certNumberX, $certNumberY, $darkGreen);
        
        // Add decorative elements
        // Draw a border around the certificate
        imagerectangle($image, 20, 20, $width - 20, $height - 20, $darkGreen);
        imagerectangle($image, 25, 25, $width - 25, $height - 25, $gold);
        
        // Add corner decorations
        $cornerSize = 30;
        // Top-left corner
        imageline($image, 20, 20, 20 + $cornerSize, 20, $darkGreen);
        imageline($image, 20, 20, 20, 20 + $cornerSize, $darkGreen);
        
        // Top-right corner
        imageline($image, $width - 20, 20, $width - 20 - $cornerSize, 20, $darkGreen);
        imageline($image, $width - 20, 20, $width - 20, 20 + $cornerSize, $darkGreen);
        
        // Bottom-left corner
        imageline($image, 20, $height - 20, 20 + $cornerSize, $height - 20, $darkGreen);
        imageline($image, 20, $height - 20, 20, $height - 20 - $cornerSize, $darkGreen);
        
        // Bottom-right corner
        imageline($image, $width - 20, $height - 20, $width - 20 - $cornerSize, $height - 20, $darkGreen);
        imageline($image, $width - 20, $height - 20, $width - 20, $height - 20 - $cornerSize, $darkGreen);
        
        // Add some decorative lines
        imageline($image, 100, 100, $width - 100, 100, $gold);
        imageline($image, 100, $height - 100, $width - 100, $height - 100, $gold);
        
        // Create output path
        $outputPath = storage_path('app/public/certificat_' . $certificat->id . '_' . date('Y-m-d_H-i-s') . '.jpg');
        
        // Ensure storage directory exists
        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }
        
        // Save the image
        imagejpeg($image, $outputPath, 95);
        imagedestroy($image);
        
        // Return the image for download
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }

    /**
     * Show certificate generator with HTML2Canvas approach
     */
    public function showCertificateGenerator(Certificat $certificat)
    {
        $apprenant = $certificat->apprenant->utilisateur;
        $module = $certificat->module;
        $niveauApprenant = $certificat->apprenant->niveau; // Récupérer le niveau de l'apprenant
        
        return view('admin.certificats.generator', compact('certificat', 'apprenant', 'module', 'niveauApprenant'));
    }

    /**
     * Vue générateur en lecture seule pour l'apprenant
     */
    public function showCertificateGeneratorReadonly(Certificat $certificat)
    {
        $apprenant = $certificat->apprenant->utilisateur;
        $module = $certificat->module;
        $niveauApprenant = $certificat->apprenant->niveau;
        return view('certificats.show-readonly', compact('certificat', 'apprenant', 'module', 'niveauApprenant'));
    }

    /**
     * Generate certificate with saved state from localStorage
     */
    public function generateWithSavedState(Certificat $certificat)
    {
        $apprenant = $certificat->apprenant->utilisateur;
        $module = $certificat->module;
        $niveauApprenant = $certificat->apprenant->niveau;
        
        return view('admin.certificats.generate-with-state', compact('certificat', 'apprenant', 'module', 'niveauApprenant'));
    }
}

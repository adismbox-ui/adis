<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class ApiDocumentController extends Controller
{
    /**
     * Télécharge un document
     */
    public function download(Request $request, $id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json([
                'success' => false,
                'error' => 'Document non trouvé'
            ], 404);
        }

        if (!$document->fichier) {
            return response()->json([
                'success' => false,
                'error' => 'Fichier non disponible'
            ], 404);
        }

        $filePath = storage_path('app/public/' . $document->fichier);

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'error' => 'Fichier introuvable sur le serveur'
            ], 404);
        }

        // Retourner l'URL du fichier plutôt que de le télécharger directement
        // L'application mobile peut utiliser cette URL pour télécharger le fichier
        $url = url('/storage/' . $document->fichier);

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $document->id,
                'titre' => $document->titre,
                'type' => $document->type,
                'url' => $url,
                'fichier' => $document->fichier,
            ],
        ], 200);
    }
}


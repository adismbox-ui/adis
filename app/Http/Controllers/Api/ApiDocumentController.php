<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

    /**
     * Crée un nouveau document (pour assistant/admin)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        // Vérifier que l'utilisateur est assistant ou admin
        if (!in_array($user->type_compte, ['assistant', 'admin'])) {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé. Seuls les assistants et admins peuvent créer des documents.'
            ], 403);
        }

        try {
            $data = $request->validate([
                'titre' => 'required|string|max:255',
                'niveau_id' => 'required|exists:niveaux,id',
                'semaine' => 'required|integer|min:1|max:12',
                'session_id' => 'required|exists:sessions_formation,id',
                'date_envoi' => 'required|string',
                'module_id' => 'nullable|exists:modules,id',
                'type' => 'nullable|string|max:255',
            ]);

            // Gestion de l'upload du fichier PDF
            if ($request->hasFile('fichier')) {
                $file = $request->file('fichier');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('documents', $filename, 'public');
                $data['fichier'] = $path;
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Le fichier PDF est requis'
                ], 400);
            }

            // Gestion de l'upload du fichier audio (optionnel)
            if ($request->hasFile('audio')) {
                $audioFile = $request->file('audio');
                $audioFilename = time() . '_audio_' . $audioFile->getClientOriginalName();
                $audioPath = $audioFile->storeAs('audios', $audioFilename, 'public');
                $data['audio'] = $audioPath;
            }

            // Associer le formateur si l'utilisateur est formateur
            if ($user->type_compte === 'formateur') {
                $formateur = \App\Models\Formateur::where('utilisateur_id', $user->id)->first();
                $data['formateur_id'] = $formateur ? $formateur->id : null;
            }

            // Marquer comme créé par admin/assistant
            $data['created_by_admin'] = ($user->type_compte === 'admin');
            
            // Parser la date d'envoi
            $dateEnvoi = Carbon::parse($request->date_envoi);
            $data['date_envoi'] = $dateEnvoi;
            $data['envoye'] = false;

            $document = Document::create($data);

            // Envoyer immédiatement si la date d'envoi est atteinte ou dépassée
            if ($dateEnvoi <= Carbon::now()) {
                // TODO: Implémenter l'envoi immédiat si nécessaire
                $document->update(['envoye' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Document créé avec succès',
                'document' => [
                    'id' => $document->id,
                    'titre' => $document->titre,
                    'niveau_id' => $document->niveau_id,
                    'semaine' => $document->semaine,
                    'date_envoi' => $document->date_envoi,
                    'fichier' => $document->fichier ? url('/storage/' . $document->fichier) : null,
                    'audio' => $document->audio ? url('/storage/' . $document->audio) : null,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du document', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du document: ' . $e->getMessage()
            ], 500);
        }
    }
}









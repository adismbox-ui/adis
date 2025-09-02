<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentApiController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'Test document endpoint'], 200);
    }
    public function index()
    {
        $documents = Document::where('created_by_admin', true)->get();
        return response()->json(['documents' => $documents], 200);
    }
    public function create()
    {
        $modules = \App\Models\Module::orderBy('titre')->get();
        $niveaux = \App\Models\Niveau::orderBy('nom')->get();
        $sessions = \App\Models\SessionsFormation::orderBy('nom')->get();
        return response()->json(['modules' => $modules, 'niveaux' => $niveaux, 'sessions' => $sessions], 200);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'fichier' => 'nullable|file|mimes:pdf,doc,docx,txt,rtf',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a,aac,ogg',
            'module_id' => 'nullable|exists:modules,id',
            'session_id' => 'nullable|exists:sessions_formation,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'semaine' => 'required|integer|min:1|max:12',
            'date_envoi' => 'required|date|after_or_equal:today',
        ]);

        // Gérer le fichier document
        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        // Gérer le fichier audio
        if ($request->hasFile('audio')) {
            $data['audio'] = $request->file('audio')->store('audios', 'public');
        }

        // Vérifier qu'au moins un fichier est fourni
        if (!isset($data['fichier']) && !isset($data['audio'])) {
            return response()->json([
                'error' => 'Au moins un fichier (document ou audio) doit être fourni.'
            ], 422);
        }

        if (auth()->check() && auth()->user()->type_compte === 'formateur') {
            $formateur = \App\Models\Formateur::where('utilisateur_id', auth()->user()->id)->first();
            $data['formateur_id'] = $formateur ? $formateur->id : null;
        }

        $data['created_by_admin'] = true;
        $data['semaine'] = $request->input('semaine');
        $data['date_envoi'] = $request->input('date_envoi');
        $data['session_id'] = $request->input('session_id');

        $document = Document::create($data);

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $document->id,
                'titre' => $document->titre,
                'type' => $document->type,
                'fichier' => $document->fichier,
                'audio' => $document->audio,
                'module_id' => $document->module_id,
                'session_id' => $document->session_id,
                'niveau_id' => $document->niveau_id,
                'semaine' => $document->semaine,
                'date_envoi' => $document->date_envoi,
                'formateur_id' => $document->formateur_id,
                'created_by_admin' => $document->created_by_admin,
                'created_at' => $document->created_at,
                'updated_at' => $document->updated_at
            ],
            'message' => 'Document envoyé avec succès !'
        ], 201);
    }
    public function show(Document $document)
    {
        return response()->json(['document' => $document], 200);
    }
    public function edit(Document $document)
    {
        return response()->json(['document' => $document], 200);
    }
    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $document->update($data);
        return response()->json(['document' => $document, 'message' => 'Document mis à jour avec succès'], 200);
    }
    public function destroy(Document $document)
    {
        $document->delete();
        return response()->json(null, 204);
    }

    /**
     * Récupère les documents pour l'apprenant connecté
     * Uniquement les documents des modules payés dans son niveau
     */
    public function mesDocumentsApprenant(Request $request)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        // Vérifier que l'utilisateur est un apprenant
        if (!$user->apprenant) {
            return response()->json(['error' => 'Accès réservé aux apprenants'], 403);
        }

        $apprenant = $user->apprenant;

        // Récupérer les modules payés par l'apprenant
        $modulesPayes = $apprenant->paiements()
            ->where('statut', 'valide')
            ->with('module')
            ->get()
            ->pluck('module_id')
            ->unique()
            ->filter();

        if ($modulesPayes->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Aucun module payé trouvé',
                'documents' => [],
                'statistiques' => [
                    'total_documents' => 0,
                    'modules_payes' => 0,
                    'niveau_actuel' => $apprenant->niveau->nom ?? 'Non défini'
                ]
            ], 200);
        }

        // Récupérer les documents des modules payés, accessibles à partir de la date d'envoi
        $documents = Document::whereIn('module_id', $modulesPayes)
            ->where('date_envoi', '<=', now()) // Seulement les documents dont la date d'envoi est passée
            ->with(['module', 'niveau', 'formateur.utilisateur'])
            ->orderBy('semaine')
            ->orderBy('created_at', 'desc')
            ->get();

        // Formater les documents
        $documentsFormates = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'titre' => $document->titre,
                'type' => $document->type,
                'fichier' => $document->fichier,
                'audio' => $document->audio,
                'url_telechargement' => url("/api/documents/{$document->id}/telecharger"),
                'url_telechargement_audio' => $document->audio ? url("/api/documents/{$document->id}/telecharger-audio") : null,
                'semaine' => $document->semaine,
                'date_envoi' => $document->date_envoi,
                'created_at' => $document->created_at,
                'module' => $document->module ? [
                    'id' => $document->module->id,
                    'titre' => $document->module->titre,
                    'discipline' => $document->module->discipline
                ] : null,
                'niveau' => $document->niveau ? [
                    'id' => $document->niveau->id,
                    'nom' => $document->niveau->nom
                ] : null,
                'formateur' => $document->formateur && $document->formateur->utilisateur ? [
                    'id' => $document->formateur->id,
                    'nom' => $document->formateur->utilisateur->nom,
                    'prenom' => $document->formateur->utilisateur->prenom
                ] : null
            ];
        });

        // Statistiques
        $statistiques = [
            'total_documents' => $documents->count(),
            'modules_payes' => $modulesPayes->count(),
            'niveau_actuel' => $apprenant->niveau->nom ?? 'Non défini',
            'documents_par_semaine' => $documents->groupBy('semaine')->map->count(),
            'documents_par_module' => $documents->groupBy('module_id')->map->count()
        ];

        return response()->json([
            'success' => true,
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'niveau' => $apprenant->niveau->nom ?? 'Non défini'
            ],
            'documents' => $documentsFormates,
            'statistiques' => $statistiques,
            'message' => 'Documents récupérés avec succès'
        ], 200);
    }

    /**
     * Télécharge un document spécifique
     */
    public function telechargerDocument($id)
    {
        $user = auth('api')->user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        // Récupérer le document
        $document = Document::find($id);
        if (!$document) {
            return response()->json(['error' => 'Document non trouvé'], 404);
        }

        // Vérifier que la date d'envoi est passée
        if ($document->date_envoi && $document->date_envoi > now()) {
            return response()->json(['error' => 'Ce document n\'est pas encore accessible. Date d\'envoi : ' . $document->date_envoi->format('d/m/Y')], 403);
        }

        // Vérifier les permissions selon le type d'utilisateur
        if ($user->type_compte === 'apprenant') {
            // Pour les apprenants, vérifier qu'ils ont payé le module
            $apprenant = $user->apprenant;
            if (!$apprenant) {
                return response()->json(['error' => 'Accès réservé aux apprenants'], 403);
            }

            $modulePaye = $apprenant->paiements()
                ->where('module_id', $document->module_id)
                ->where('statut', 'valide')
                ->exists();

            if (!$modulePaye) {
                return response()->json(['error' => 'Vous devez payer ce module pour accéder au document'], 403);
            }
        } elseif ($user->type_compte === 'formateur') {
            // Pour les formateurs, vérifier qu'ils ont accès au niveau du document
            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json(['error' => 'Accès réservé aux formateurs'], 403);
            }

            // Vérifier que le formateur a accès au niveau du document
            // La table niveaux a une colonne 'formateur_id', pas 'niveau_id'
            $niveauFormateur = \App\Models\Niveau::where('formateur_id', $formateur->id)
                ->where('id', $document->niveau_id)
                ->exists();
                
            if (!$niveauFormateur) {
                return response()->json(['error' => 'Vous n\'avez pas accès à ce niveau'], 403);
            }
        } elseif ($user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Vérifier que le fichier existe
        $cheminFichier = storage_path('app/public/' . $document->fichier);
        if (!file_exists($cheminFichier)) {
            return response()->json(['error' => 'Fichier non trouvé sur le serveur'], 404);
        }

        // Retourner le fichier pour téléchargement
        return response()->download($cheminFichier, $document->titre . '.' . pathinfo($document->fichier, PATHINFO_EXTENSION));
    }

    /**
     * Télécharge un fichier audio spécifique
     */
    public function telechargerAudio($id)
    {
        $user = auth('api')->user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        // Récupérer le document
        $document = Document::find($id);
        if (!$document) {
            return response()->json(['error' => 'Document non trouvé'], 404);
        }

        // Vérifier que la date d'envoi est passée
        if ($document->date_envoi && $document->date_envoi > now()) {
            return response()->json(['error' => 'Ce document n\'est pas encore accessible. Date d\'envoi : ' . $document->date_envoi->format('d/m/Y')], 403);
        }

        // Vérifier que le document a un fichier audio
        if (!$document->audio) {
            return response()->json(['error' => 'Ce document n\'a pas de fichier audio associé'], 404);
        }

        // Vérifier les permissions selon le type d'utilisateur
        if ($user->type_compte === 'apprenant') {
            // Pour les apprenants, vérifier qu'ils ont payé le module
            $apprenant = $user->apprenant;
            if (!$apprenant) {
                return response()->json(['error' => 'Accès réservé aux apprenants'], 403);
            }

            $modulePaye = $apprenant->paiements()
                ->where('module_id', $document->module_id)
                ->where('statut', 'valide')
                ->exists();

            if (!$modulePaye) {
                return response()->json(['error' => 'Vous devez payer ce module pour accéder au fichier audio'], 403);
            }
        } elseif ($user->type_compte === 'formateur') {
            // Pour les formateurs, vérifier qu'ils ont accès au niveau du document
            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json(['error' => 'Accès réservé aux formateurs'], 403);
            }

            // Vérifier que le formateur a accès au niveau du document
            // La table niveaux a une colonne 'formateur_id', pas 'niveau_id'
            $niveauFormateur = \App\Models\Niveau::where('formateur_id', $formateur->id)
                ->where('id', $document->niveau_id)
                ->exists();
                
            if (!$niveauFormateur) {
                return response()->json(['error' => 'Vous n\'avez pas accès à ce niveau'], 403);
            }
        } elseif ($user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Vérifier que le fichier audio existe
        $cheminAudio = storage_path('app/public/' . $document->audio);
        if (!file_exists($cheminAudio)) {
            return response()->json(['error' => 'Fichier audio non trouvé sur le serveur'], 404);
        }

        // Retourner le fichier audio pour téléchargement
        return response()->download($cheminAudio, $document->titre . '_audio.' . pathinfo($document->audio, PATHINFO_EXTENSION));
    }

    public function debugDocumentsGeneraux()
    {
        $docs = \DB::table('documents as d')
            ->leftJoin('formateurs as f', 'd.formateur_id', '=', 'f.id')
            ->leftJoin('utilisateurs as u', 'f.utilisateur_id', '=', 'u.id')
            ->select('d.id as document_id', 'd.titre', 'd.formateur_id', 'f.utilisateur_id', 'u.nom', 'u.prenom')
            ->whereNull('d.module_id')
            ->get();
        return response()->json(['docs' => $docs], 200);
    }

    /**
     * Récupère TOUS les documents accessibles pour le formateur connecté
     * Uniquement les documents dont la date d'envoi est arrivée
     */
    public function mesDocumentsFormateur(Request $request)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        // Vérifier que l'utilisateur est un formateur
        if (!$user->formateur) {
            return response()->json(['error' => 'Accès réservé aux formateurs'], 403);
        }

        $formateur = $user->formateur;

        // Récupérer TOUS les documents accessibles à partir de la date d'envoi
        $documents = Document::where('date_envoi', '<=', now()) // Seulement les documents dont la date d'envoi est passée
            ->with(['module', 'niveau', 'formateur.utilisateur'])
            ->orderBy('semaine')
            ->orderBy('created_at', 'desc')
            ->get();

        // Formater les documents
        $documentsFormates = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'titre' => $document->titre,
                'type' => $document->type,
                'fichier' => $document->fichier,
                'audio' => $document->audio,
                'url_telechargement' => url("/api/documents/{$document->id}/telecharger"),
                'url_telechargement_audio' => $document->audio ? url("/api/documents/{$document->id}/telecharger-audio") : null,
                'semaine' => $document->semaine,
                'date_envoi' => $document->date_envoi,
                'created_at' => $document->created_at,
                'module' => $document->module ? [
                    'id' => $document->module->id,
                    'titre' => $document->module->titre,
                    'discipline' => $document->module->discipline
                ] : null,
                'niveau' => $document->niveau ? [
                    'id' => $document->niveau->id,
                    'nom' => $document->niveau->nom
                ] : null
            ];
        });

        // Statistiques
        $statistiques = [
            'total_documents' => $documents->count(),
            'documents_par_semaine' => $documents->groupBy('semaine')->map->count(),
            'documents_par_module' => $documents->groupBy('module_id')->map->count()
        ];

        return response()->json([
            'success' => true,
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email
            ],
            'documents' => $documentsFormates,
            'statistiques' => $statistiques,
            'message' => 'Tous les documents accessibles ont été récupérés avec succès'
        ], 200);
    }
}

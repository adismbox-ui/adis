<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formateur;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Document;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Inscription;
use Carbon\Carbon;

class ApiFormateurController extends Controller
{
    /**
     * Récupère le calendrier du formateur
     */
    public function getCalendrier(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $modules = Module::where('formateur_id', $formateur->id)
            ->with(['niveau', 'inscriptions.apprenant.utilisateur'])
            ->get();

        $calendrier = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'lien' => $module->lien,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
                'nombre_inscrits' => $module->inscriptions->where('statut', 'valide')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'calendrier' => $calendrier,
        ], 200);
    }

    /**
     * Récupère les modules du formateur
     */
    public function getModules(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $modules = Module::where('formateur_id', $formateur->id)
            ->with(['niveau', 'documents', 'questionnaires'])
            ->get();

        $modulesFormates = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'prix' => $module->prix,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
                'nombre_documents' => $module->documents->count(),
                'nombre_questionnaires' => $module->questionnaires->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'modules' => $modulesFormates,
        ], 200);
    }

    /**
     * Récupère les niveaux du formateur
     */
    public function getNiveaux(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $niveaux = Niveau::where('formateur_id', $formateur->id)
            ->where('actif', true)
            ->orderBy('ordre')
            ->get();

        $niveauxFormates = $niveaux->map(function ($niveau) {
            return [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description,
                'ordre' => $niveau->ordre,
                'lien_meet' => $niveau->lien_meet,
            ];
        });

        return response()->json([
            'success' => true,
            'niveaux' => $niveauxFormates,
        ], 200);
    }

    /**
     * Récupère les modules d'un niveau
     */
    public function getModulesParNiveau(Request $request, $niveauId)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $modules = Module::where('formateur_id', $formateur->id)
            ->where('niveau_id', $niveauId)
            ->with(['niveau', 'documents'])
            ->get();

        $modulesFormates = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'prix' => $module->prix,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'modules' => $modulesFormates,
        ], 200);
    }

    /**
     * Récupère le profil du formateur
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'telephone' => $user->telephone,
            ],
            'formateur' => [
                'id' => $formateur->id,
                'specialite' => $formateur->specialite,
                'valide' => $formateur->valide,
            ],
        ], 200);
    }

    /**
     * Met à jour le profil du formateur
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'prenom' => 'sometimes|string|max:255',
            'nom' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|max:20',
            'specialite' => 'sometimes|string|max:255',
        ]);

        $user->update($data);
        
        if (isset($data['specialite'])) {
            $formateur->specialite = $data['specialite'];
            $formateur->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
        ], 200);
    }

    /**
     * Récupère les apprenants du formateur
     */
    public function getApprenants(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        // Récupérer les modules du formateur
        $moduleIds = Module::where('formateur_id', $formateur->id)->pluck('id');

        // Récupérer les apprenants inscrits à ces modules
        $inscriptions = Inscription::whereIn('module_id', $moduleIds)
            ->where('statut', 'valide')
            ->with(['apprenant.utilisateur', 'module'])
            ->get();

        $apprenants = $inscriptions->map(function ($inscription) {
            return [
                'id' => $inscription->apprenant->id,
                'utilisateur' => [
                    'id' => $inscription->apprenant->utilisateur->id,
                    'nom' => $inscription->apprenant->utilisateur->nom,
                    'prenom' => $inscription->apprenant->utilisateur->prenom,
                    'email' => $inscription->apprenant->utilisateur->email,
                ],
                'module' => [
                    'id' => $inscription->module->id,
                    'titre' => $inscription->module->titre,
                ],
            ];
        })->unique('id')->values();

        return response()->json([
            'success' => true,
            'apprenants' => $apprenants,
        ], 200);
    }

    /**
     * Récupère les documents du formateur
     */
    public function getDocuments(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $documents = Document::where('formateur_id', $formateur->id)
            ->with(['module', 'niveau'])
            ->get();

        $documentsFormates = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'titre' => $document->titre,
                'type' => $document->type,
                'fichier' => $document->fichier ? url('/storage/' . $document->fichier) : null,
                'module' => $document->module ? [
                    'id' => $document->module->id,
                    'titre' => $document->module->titre,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $documentsFormates,
        ], 200);
    }

    /**
     * Upload un document
     */
    public function uploadDocument(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|string',
            'module_id' => 'nullable|exists:modules,id',
            'fichier' => 'required|file',
        ]);

        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename, 'public');
            $data['fichier'] = $path;
        }

        $data['formateur_id'] = $formateur->id;
        $document = Document::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Document uploadé avec succès',
            'document' => $document,
        ], 201);
    }

    /**
     * Récupère les questionnaires du formateur
     */
    public function getQuestionnaires(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $moduleIds = Module::where('formateur_id', $formateur->id)->pluck('id');

        $questionnaires = Questionnaire::whereIn('module_id', $moduleIds)
            ->with(['module', 'questions'])
            ->get();

        $questionnairesFormates = $questionnaires->map(function ($questionnaire) {
            return [
                'id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'description' => $questionnaire->description,
                'minutes' => $questionnaire->minutes,
                'module' => $questionnaire->module ? [
                    'id' => $questionnaire->module->id,
                    'titre' => $questionnaire->module->titre,
                ] : null,
                'nombre_questions' => $questionnaire->questions->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'questionnaires' => $questionnairesFormates,
        ], 200);
    }

    /**
     * Crée un questionnaire
     */
    public function createQuestionnaire(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'required|exists:modules,id',
            'minutes' => 'nullable|integer',
        ]);

        // Vérifier que le module appartient au formateur
        $module = Module::where('id', $data['module_id'])
            ->where('formateur_id', $formateur->id)
            ->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'error' => 'Module non trouvé ou non autorisé'
            ], 404);
        }

        $questionnaire = Questionnaire::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Questionnaire créé avec succès',
            'questionnaire' => $questionnaire,
        ], 201);
    }
}


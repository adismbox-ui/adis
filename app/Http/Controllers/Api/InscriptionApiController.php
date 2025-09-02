<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inscription;

class InscriptionApiController extends Controller
{
    public function index()
    {
        $inscriptions = Inscription::all();
        return response()->json(['inscriptions' => $inscriptions], 200);
    }

    public function create()
    {
        return response()->json(['message' => 'Endpoint pour création d\'inscription'], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'mobile_money' => 'required|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'moyen_paiement' => 'nullable|string',
        ]);
        $user = auth()->user();
        $apprenant = $user->apprenant;
        if ($apprenant && isset($data['niveau_id'])) {
            $apprenant->niveau_id = $data['niveau_id'];
            $apprenant->save();
        }
        $data['apprenant_id'] = $user->id;
        $data['date_inscription'] = now();
        $data['statut'] = 'en_attente';
        $inscription = \App\Models\Inscription::create($data);
        return response()->json(['inscription' => $inscription, 'message' => "Votre demande d'inscription a été envoyée. Elle sera validée par l'administrateur après vérification du paiement."], 201);
    }

    public function show(Inscription $inscription)
    {
        return response()->json(['inscription' => $inscription], 200);
    }

    public function edit(Inscription $inscription)
    {
        return response()->json(['inscription' => $inscription], 200);
    }

    public function update(Request $request, Inscription $inscription)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $inscription->update($data);
        return response()->json(['inscription' => $inscription, 'message' => 'Inscription mise à jour avec succès'], 200);
    }

    public function destroy(Inscription $inscription)
    {
        $inscription->delete();
        return response()->json(null, 204);
    }

    public function inscriptionModuleForm(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
        }
        $user = auth()->user();
        $apprenant = $user ? $user->apprenant : null;
        $modules = \App\Models\Module::with('niveau')->orderBy('titre')->get();
        $niveaux = \App\Models\Niveau::orderBy('ordre')->get();
        $selectedModuleId = $request->query('module_id');
        return response()->json([
            'modules' => $modules,
            'user' => $user,
            'apprenant' => $apprenant,
            'niveaux' => $niveaux,
            'selectedModuleId' => $selectedModuleId
        ], 200);
    }

    public function apprenantsParModule(\App\Models\Module $module)
    {
        $user = auth()->user();

        // 1. Vérifier si un utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }

        // 2. Vérifier si l'utilisateur est un formateur ou un admin
        if ($user->type_compte !== 'admin' && $user->type_compte !== 'formateur') {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }

        // Le reste du code est inchangé
        $inscriptionsValidees = $module->inscriptions()
            ->where('statut', 'valide')
            ->with('apprenant.utilisateur')
            ->get();

        $apprenants = $inscriptionsValidees->map(function($inscription) {
            if ($inscription->apprenant && $inscription->apprenant->utilisateur) {
                return [
                    'nom' => $inscription->apprenant->utilisateur->nom,
                    'prenom' => $inscription->apprenant->utilisateur->prenom,
                    'email' => $inscription->apprenant->utilisateur->email,
                ];
            }
            return null;
        })->filter();

        return response()->json(['apprenants' => $apprenants], 200);
    }
}

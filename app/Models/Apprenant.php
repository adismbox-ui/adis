<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apprenant extends Model
{
    protected $fillable = [
        'utilisateur_id',
        'niveau_id',
        'connaissance_adis',
        'formation_adis',
        'formation_autre',
        'niveau_coran',
        'niveau_arabe',
        'connaissance_tomes_medine',
        'tomes_medine_etudies',
        'disciplines_souhaitees',
        'attentes',
        'formateur_domicile',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function certificats()
    {
        return $this->hasMany(Certificat::class);
    }

    public function niveau()
    {
        return $this->belongsTo(\App\Models\Niveau::class);
    }

    /**
     * Vérifie si l'apprenant peut changer de niveau
     * @return bool
     */
    public function peutChangerNiveau()
    {
        // Vérifier s'il y a des inscriptions en cours
        $inscriptionsEnCours = $this->inscriptions()
            ->whereIn('statut', ['en_cours', 'valide'])
            ->count();
        
        // Vérifier s'il y a des paiements en attente
        $paiementsEnAttente = $this->paiements()
            ->whereIn('statut', ['en_attente', 'en_cours'])
            ->count();
        
        // L'apprenant peut changer de niveau s'il n'a pas d'inscriptions en cours
        // et pas de paiements en attente
        return $inscriptionsEnCours === 0 && $paiementsEnAttente === 0;
    }

    /**
     * Change le niveau de l'apprenant
     * @param int $nouveauNiveauId
     * @return bool
     */
    public function changerNiveau($nouveauNiveauId)
    {
        try {
            // Vérifier que le nouveau niveau existe
            $nouveauNiveau = Niveau::find($nouveauNiveauId);
            if (!$nouveauNiveau) {
                return false;
            }

            // Vérifier que l'apprenant peut changer de niveau
            if (!$this->peutChangerNiveau()) {
                return false;
            }

            // Mettre à jour le niveau
            $this->niveau_id = $nouveauNiveauId;
            $this->save();

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors du changement de niveau: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si tous les modules du niveau actuel de l'apprenant sont validés
     * @return array
     */
    public function verifierModulesNiveauValides()
    {
        try {
            // Vérifier que l'apprenant a un niveau assigné
            if (!$this->niveau_id) {
                return [
                    'niveau_assigne' => false,
                    'message' => 'Aucun niveau assigné à cet apprenant',
                    'modules' => [],
                    'statut_global' => 'incomplet'
                ];
            }

            // Charger le niveau avec ses modules
            $niveau = $this->niveau()->with('modules')->first();
            if (!$niveau) {
                return [
                    'niveau_assigne' => false,
                    'message' => 'Niveau non trouvé',
                    'modules' => [],
                    'statut_global' => 'incomplet'
                ];
            }

            $modules = $niveau->modules;
            $modulesValides = [];
            $modulesEnAttente = [];
            $modulesInvalides = [];
            $totalModules = $modules->count();
            $modulesCompletes = 0;

            foreach ($modules as $module) {
                $statutModule = $this->verifierStatutModule($module);
                
                $moduleInfo = [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'description' => $module->description,
                    'statut' => $statutModule['statut'],
                    'details' => $statutModule['details']
                ];

                switch ($statutModule['statut']) {
                    case 'valide':
                        $modulesValides[] = $moduleInfo;
                        $modulesCompletes++;
                        break;
                    case 'en_attente':
                        $modulesEnAttente[] = $moduleInfo;
                        break;
                    case 'invalide':
                        $modulesInvalides[] = $moduleInfo;
                        break;
                }
            }

            // Déterminer le statut global
            $statutGlobal = 'complet';
            if (count($modulesEnAttente) > 0) {
                $statutGlobal = 'en_attente';
            }
            if (count($modulesInvalides) > 0) {
                $statutGlobal = 'incomplet';
            }

            return [
                'niveau_assigne' => true,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre
                ],
                'total_modules' => $totalModules,
                'modules_completes' => $modulesCompletes,
                'modules_en_attente' => count($modulesEnAttente),
                'modules_invalides' => count($modulesInvalides),
                'statut_global' => $statutGlobal,
                'modules' => [
                    'valides' => $modulesValides,
                    'en_attente' => $modulesEnAttente,
                    'invalides' => $modulesInvalides
                ],
                'message' => $this->genererMessageStatut($statutGlobal, $totalModules, $modulesCompletes)
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors de la vérification des modules: ' . $e->getMessage());
            return [
                'niveau_assigne' => false,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage(),
                'modules' => [],
                'statut_global' => 'erreur'
            ];
        }
    }

    /**
     * Vérifie le statut d'un module spécifique pour cet apprenant
     * @param \App\Models\Module $module
     * @return array
     */
    private function verifierStatutModule($module)
    {
        // Vérifier s'il y a un paiement validé pour ce module
        $paiementValide = $this->paiements()
            ->where('module_id', $module->id)
            ->where('statut', 'valide')
            ->first();

        if ($paiementValide) {
            return [
                'statut' => 'valide',
                'details' => 'Module complété avec paiement validé'
            ];
        }

        // Vérifier s'il y a un paiement en attente
        $paiementEnAttente = $this->paiements()
            ->where('module_id', $module->id)
            ->where('statut', 'en_attente')
            ->first();

        if ($paiementEnAttente) {
            return [
                'statut' => 'en_attente',
                'details' => 'Paiement en attente de validation'
            ];
        }

        // Vérifier s'il y a une inscription en attente
        $inscriptionEnAttente = $this->inscriptions()
            ->where('module_id', $module->id)
            ->where('statut', 'en_attente')
            ->first();

        if ($inscriptionEnAttente) {
            return [
                'statut' => 'en_attente',
                'details' => 'Inscription en attente de validation'
            ];
        }

        return [
            'statut' => 'invalide',
            'details' => 'Aucune inscription ou paiement trouvé pour ce module'
        ];
    }

    /**
     * Génère un message descriptif du statut
     * @param string $statutGlobal
     * @param int $totalModules
     * @param int $modulesCompletes
     * @return string
     */
    private function genererMessageStatut($statutGlobal, $totalModules, $modulesCompletes)
    {
        switch ($statutGlobal) {
            case 'complet':
                return "Félicitations ! Vous avez complété tous les modules de ce niveau ({$modulesCompletes}/{$totalModules}).";
            case 'en_attente':
                return "Vous avez {$modulesCompletes} modules complétés sur {$totalModules}. Certains modules sont en attente de validation.";
            case 'incomplet':
                return "Vous avez {$modulesCompletes} modules complétés sur {$totalModules}. Vous devez vous inscrire aux modules manquants.";
            default:
                return "Statut indéterminé pour ce niveau.";
        }
    }
}

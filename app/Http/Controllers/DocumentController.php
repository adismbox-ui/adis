<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Carbon\Carbon;
use App\Services\ScheduledContentService;

class DocumentController extends Controller
{
    /**
     * Affiche la page de test document.
     */
    public function test()
    {
        return view('document');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = new ScheduledContentService();
        $documentsEnvoyes = $service->sendScheduledDocuments();
        $message = null;
        if (count($documentsEnvoyes) > 0) {
            $message = 'Envois automatiques : ' . count($documentsEnvoyes) . ' document(s) envoyés : ' . implode(', ', $documentsEnvoyes) . '.';
        }
        $documents = Document::where('created_by_admin', true)
            ->with(['module', 'niveau'])
            ->orderBy('date_envoi', 'desc')
            ->get();
        return view('admin.documents.index', compact('documents'))->with('auto_send_message', $message);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = \App\Models\Module::orderBy('titre')->get();
        $niveaux = \App\Models\Niveau::orderBy('nom')->get();
        $sessions = \App\Models\SessionFormation::orderBy('date_debut', 'desc')->get();
        return view('admin.documents.create', compact('modules', 'niveaux', 'sessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'fichier' => 'required|file',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg',
            'module_id' => 'nullable|exists:modules,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'semaine' => 'required|integer|min:1|max:12',
            'session_id' => 'required|exists:sessions_formation,id',
            'date_envoi' => 'required|string',
        ]);

        // Gestion de l'upload du fichier
        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        // Gestion de l'upload du fichier audio
        if ($request->hasFile('audio')) {
            $data['audio'] = $request->file('audio')->store('audios', 'public');
        }

        // Associer le formateur connecté si disponible
        if (auth()->check() && auth()->user()->type_compte === 'formateur') {
            $formateur = \App\Models\Formateur::where('utilisateur_id', auth()->user()->id)->first();
            $data['formateur_id'] = $formateur ? $formateur->id : null;
        }

        // Utiliser la date d'envoi fournie par l'utilisateur
        $dateEnvoi = Carbon::parse($request->date_envoi);
        
        // Marquer comme créé par l'admin
        $data['created_by_admin'] = true;
        $data['semaine'] = $request->input('semaine');
        $data['session_id'] = $request->session_id;
        $data['date_envoi'] = $dateEnvoi;
        $data['envoye'] = false;

        $document = Document::create($data);
        
        // Envoyer immédiatement si la date d'envoi est atteinte ou dépassée
        if ($dateEnvoi <= Carbon::now()) {
            $this->sendDocumentImmediately($document);
            $document->update(['envoye' => true]);
        }
        // Si la date est dans le futur, le document sera envoyé automatiquement par le système de surveillance
        
        $message = $dateEnvoi <= Carbon::now() 
            ? 'Document envoyé immédiatement avec succès !'
            : 'Document programmé avec succès ! Il sera envoyé automatiquement à la date et heure spécifiées.';
            
        return back()->with('success', $message);
    }
    
    /**
     * Calcule la date d'envoi automatique basée sur la session et la semaine
     */
    private function calculateEnvoiDate($sessionId, $semaine)
    {
        $session = \App\Models\SessionFormation::find($sessionId);
        if (!$session) {
            throw new \Exception('Session non trouvée');
        }
        
        // Calculer le premier dimanche après la date de début
        $debut = \Carbon\Carbon::parse($session->date_debut);
        $premierDimanche = $this->getNextSunday($debut);
        
        // Ajouter (semaine - 1) * 7 jours pour obtenir le dimanche de la semaine demandée
        $dateEnvoi = $premierDimanche->copy()->addDays(($semaine - 1) * 7);
        
        // Définir l'heure à 13h00 (dimanche soir)
        $dateEnvoi->setTime(13, 0, 0);
        
        return $dateEnvoi;
    }
    
    /**
     * Trouve le prochain dimanche après une date donnée
     */
    private function getNextSunday($date)
    {
        $day = $date->dayOfWeek;
        $daysUntilSunday = (7 - $day) % 7;
        return $date->copy()->addDays($daysUntilSunday);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        $modules = \App\Models\Module::orderBy('titre')->get();
        $niveaux = \App\Models\Niveau::orderBy('nom')->get();
        return view('documents.edit', compact('document', 'modules', 'niveaux'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $document->update($data);
        return redirect()->route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('admin.documents.index');
    }

    /**
     * Debug : Affiche les documents généraux et leur formateur/utilisateur
     */
    public function debugDocumentsGeneraux()
    {
        $docs = \DB::table('documents as d')
            ->leftJoin('formateurs as f', 'd.formateur_id', '=', 'f.id')
            ->leftJoin('utilisateurs as u', 'f.utilisateur_id', '=', 'u.id')
            ->select('d.id as document_id', 'd.titre', 'd.formateur_id', 'f.utilisateur_id', 'u.nom', 'u.prenom')
            ->whereNull('d.module_id')
            ->get();
        return view('documents.debug', compact('docs'));
    }
    
    /**
     * Envoie immédiatement un document aux apprenants
     */
    private function sendDocumentImmediately($document)
    {
        // Récupérer les apprenants concernés
        $apprenants = $this->getApprenantsForDocument($document);
        
        foreach ($apprenants as $apprenant) {
            try {
                // Envoyer l'email de notification
                $this->sendDocumentEmail($apprenant, $document);
                
                // Créer une notification en base de données
                $this->createNotification($apprenant, 'document', $document);
                
            } catch (\Exception $e) {
                \Log::error("Erreur envoi document à apprenant {$apprenant->id}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Récupère les apprenants concernés par le document
     */
    private function getApprenantsForDocument($document)
    {
        $query = \App\Models\Apprenant::with('utilisateur');
        
        // Filtrer par niveau si spécifié
        if ($document->niveau_id) {
            $query->whereHas('inscriptions.module', function($q) use ($document) {
                $q->where('niveau_id', $document->niveau_id);
            });
        }
        
        // Filtrer par module si spécifié
        if ($document->module_id) {
            $query->whereHas('inscriptions', function($q) use ($document) {
                $q->where('module_id', $document->module_id);
            });
        }
        
        // Filtrer par session si spécifiée
        if ($document->session_id) {
            $query->whereHas('inscriptions', function($q) use ($document) {
                $q->where('session_formation_id', $document->session_id);
            });
        }
        
        return $query->get();
    }
    
    /**
     * Envoie l'email de notification pour un document
     */
    private function sendDocumentEmail($apprenant, $document)
    {
        $data = [
            'apprenant' => $apprenant,
            'document' => $document,
            'url' => route('documents.show', $document->id)
        ];
        
        \Illuminate\Support\Facades\Mail::send('emails.document-notification', $data, function($message) use ($apprenant, $document) {
            $message->to($apprenant->utilisateur->email, $apprenant->utilisateur->prenom . ' ' . $apprenant->utilisateur->nom)
                    ->subject("Nouveau document disponible : {$document->titre}");
        });
    }
    
    /**
     * Crée une notification en base de données
     */
    private function createNotification($apprenant, $type, $content)
    {
        $message = $type === 'questionnaire' 
            ? "Nouveau questionnaire disponible : {$content->titre}"
            : "Nouveau document disponible : {$content->titre}";
            
        \App\Models\Notification::create([
            'utilisateur_id' => $apprenant->utilisateur_id,
            'titre' => $message,
            'message' => $message,
            'type' => $type,
            'lien' => $type === 'questionnaire' 
                ? route('questionnaire.answer', $content->id)
                : route('documents.show', $content->id),
            'lu' => false
        ]);
    }
}

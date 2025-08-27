@extends('layouts.app')

@section('content')
<!-- Particules animées -->
<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>
<div class="container-fluid main-container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 p-0">
            <div class="sidebar">
                <div class="p-4">
                    <h3 class="text-center mb-4" style="color: var(--accent-green);">
                        <i class="fas fa-project-diagram me-2"></i>Navigation
                    </h3>
                    @include('layouts.projets_sidebar')
                </div>
            </div>
        </div>
        <!-- Contenu principal -->
        <div class="col-md-9">
            <div class="content-area card-3d">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <h1 class="main-title mb-3"><i class="fas fa-bullhorn me-3"></i>Appels à projets</h1>
                <div class="description mb-4">
                    <p class="lead mb-0">
                        <strong>Engageons-nous ensemble !</strong><br>
                        ADIS invite les entreprises partenaires, associations ou bénévoles spécialisés à répondre aux appels à projets que nous lançons pour la réalisation de nos initiatives communautaires. Ce cadre permet de garantir la transparence, la qualité et l'inclusivité dans l'exécution de nos projets.<br><br>
                        Vous avez une expertise ? Une solution à proposer ? Rejoignez nos appels à projets et devenez un acteur du changement.
                    </p>
                </div>

                <!-- Appels en cours -->
                <h3 class="mb-3 text-success"><i class="fas fa-folder-open me-2"></i>Appels en cours</h3>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Référence</th>
                                <th>Intitulé</th>
                                <th>Domaine</th>
                                <th>Date limite de soumission</th>
                                <th>État</th>
                                <th>Détails de l'offre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appelsEnCours as $appel)
                                <tr>
                                    <td>{{ $appel->reference }}</td>
                                    <td>{{ $appel->intitule }}</td>
                                    <td>{{ $appel->domaine }}</td>
                                    <td>{{ $appel->date_limite_soumission->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-success">Ouvert</span></td>
                                    <td>
                                        @if($appel->details_offre)
                                            <a href="#" class="btn btn-outline-primary btn-sm" onclick="showDetails('{{ $appel->details_offre }}')">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                        @else
                                            <span class="text-muted">Non disponible</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucun appel à projet en cours</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Appels clôturés -->
                <h3 class="mb-3 text-danger"><i class="fas fa-folder-minus me-2"></i>Appels clôturés</h3>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Référence</th>
                                <th>Intitulé</th>
                                <th>Bénéficiaires</th>
                                <th>Montant estimatif</th>
                                <th>Partenaire retenu</th>
                                <th>Date de clôture</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appelsClotures as $appel)
                                <tr>
                                    <td>{{ $appel->reference }}</td>
                                    <td>{{ $appel->intitule }}</td>
                                    <td>{{ $appel->beneficiaires }}</td>
                                    <td>{{ number_format($appel->montant_estimatif, 0, ',', ' ') }}</td>
                                    <td>{{ $appel->partenaire_retenu }}</td>
                                    <td>{{ $appel->date_cloture->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucun appel à projet clôturé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Déposer une candidature -->
                <div class="text-center mb-4">
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#candidatureModal">
                        <i class="fas fa-paper-plane me-2"></i>Déposer une candidature
                    </button>
                </div>

                <!-- Critères de sélection -->
                <div class="mb-4">
                    <h3 class="mb-3 text-info"><i class="fas fa-clipboard-check me-2"></i>Critères de sélection</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent">Conformité administrative et légale : validité des documents légaux et justificatifs fournis.</li>
                        <li class="list-group-item bg-transparent">Adéquation technique : correspondance de l'offre avec le cahier des charges et les exigences techniques de l'appel.</li>
                        <li class="list-group-item bg-transparent">Expérience et références : antécédents de réalisations similaires et compétences démontrées dans le domaine concerné.</li>
                        <li class="list-group-item bg-transparent">Prix proposé : compétitivité et rapport qualité/prix de l'offre financière.</li>
                        <li class="list-group-item bg-transparent">Délais d'exécution : capacité à respecter les délais impartis pour la réalisation du projet.</li>
                        <li class="list-group-item bg-transparent">Engagements sociaux et environnementaux : prise en compte des aspects RSE et écoresponsabilité, le cas échéant.</li>
                    </ul>
                    <small class="text-muted">Seules les candidatures répondant à l'ensemble de ces critères seront retenues pour la phase suivante.</small>
                </div>

                <!-- Documents à fournir -->
                <div class="mb-4">
                    <h3 class="mb-3 text-warning"><i class="fas fa-file-alt me-2"></i>Documents à fournir</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent">Formulaire de candidature dûment rempli et signé</li>
                        <li class="list-group-item bg-transparent">Justificatif de paiement des frais de soumission</li>
                        <li class="list-group-item bg-transparent">Extrait Kbis ou tout document officiel attestant de l'existence légale de l'entreprise</li>
                        <li class="list-group-item bg-transparent">Attestations fiscales et sociales à jour</li>
                        <li class="list-group-item bg-transparent">Références ou certificats de travaux similaires réalisés</li>
                        <li class="list-group-item bg-transparent">Proposition technique détaillée conforme au cahier des charges</li>
                        <li class="list-group-item bg-transparent">Offre financière chiffrée et signée</li>
                        <li class="list-group-item bg-transparent">Planification prévisionnelle des travaux ou interventions</li>
                        <li class="list-group-item bg-transparent">Tout autre document spécifique mentionné dans l'appel à projets</li>
                    </ul>
                    <small class="text-muted">Tout dossier incomplet pourra être écarté lors de l'examen des candidatures.</small>
                </div>

                <!-- FAQ -->
                <div class="mb-4">
                    <h3 class="mb-3 text-primary"><i class="fas fa-question-circle me-2"></i>Foire aux Questions (FAQ) – Appels à projets</h3>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    1. Qu'est-ce qu'un appel à projets ?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Un appel à projets est une invitation lancée par notre organisation pour sélectionner des partenaires ou prestataires capables de réaliser un projet spécifique selon un cahier des charges précis.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    2. Qui peut participer aux appels à projets ?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Toute entreprise ou association répondant aux critères définis dans l'appel peut soumettre une candidature.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    3. Comment déposer une candidature ?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Les candidats doivent remplir le formulaire de candidature en ligne, fournir les documents demandés, et s'acquitter des frais de soumission si applicables.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                    4. Quels sont les critères de sélection ?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    La sélection se fait sur la base de la conformité au cahier des charges, la qualité technique, le prix proposé, les références et la capacité à réaliser le projet dans les délais.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                    5. Quel est le délai pour répondre à un appel à projets ?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Chaque appel mentionne une date limite de dépôt des candidatures. Toute candidature reçue après cette date ne sera pas prise en compte.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                    6. Que faire en cas de questions durant la période de soumission ?
                                </button>
                            </h2>
                            <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="faq6" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Vous pouvez contacter notre service projets via l'adresse email ou le formulaire de contact indiqué dans l'appel à projets.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq7">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                    7. Comment être informé des résultats ?
                                </button>
                            </h2>
                            <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="faq7" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Les résultats sont communiqués aux candidats par email et publiés sur notre plateforme.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq8">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                    8. Peut-on soumettre plusieurs candidatures ?
                                </button>
                            </h2>
                            <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="faq8" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oui, sous réserve que chaque candidature concerne un projet différent et respecte les conditions de l'appel.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Candidature -->
<div class="modal fade" id="candidatureModal" tabindex="-1" aria-labelledby="candidatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="candidatureModalLabel">
                    <i class="fas fa-paper-plane me-2"></i>Déposer une candidature
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('candidatures.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <h6 class="mb-3">1. Informations sur l'entreprise</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Raison sociale *</label>
                            <input type="text" class="form-control" name="raison_sociale" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Nom du responsable *</label>
                            <input type="text" class="form-control" name="nom_responsable" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Statut juridique *</label>
                            <input type="text" class="form-control" name="statut_juridique" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Numéro RCCM / Identifiant fiscal *</label>
                            <input type="text" class="form-control" name="rccm" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Contact (téléphone, e-mail) *</label>
                            <input type="text" class="form-control" name="contact" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Site web (facultatif)</label>
                            <input type="url" class="form-control" name="site_web">
                        </div>
                    </div>
                    
                    <h6 class="mb-3">2. Référence de l'appel à projet concerné *</h6>
                    <select class="form-select mb-3" name="reference_appel" required>
                        <option value="">Sélectionner un appel à projet</option>
                        @foreach($appelsEnCours as $appel)
                            <option value="{{ $appel->reference }}">{{ $appel->reference }} - {{ $appel->intitule }}</option>
                        @endforeach
                    </select>
                    
                    <h6 class="mb-3">3. Offre technique (PDF) *</h6>
                    <input type="file" class="form-control mb-3" name="offre_technique" accept="application/pdf" required>
                    
                    <h6 class="mb-3">4. Offre financière (PDF) *</h6>
                    <input type="file" class="form-control mb-3" name="offre_financiere" accept="application/pdf" required>
                    
                    <h6 class="mb-3">5. Justificatif de paiement des frais de soumission (PDF) *</h6>
                    <input type="file" class="form-control mb-3" name="justificatif_paiement" accept="application/pdf" required>
                    
                    <h6 class="mb-3">6. Expériences similaires / Références (facultatif, PDF)</h6>
                    <input type="file" class="form-control mb-3" name="references" accept="application/pdf">
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="declaration_honneur" id="declaration_honneur" required>
                        <label class="form-check-label" for="declaration_honneur">
                            Je certifie que les informations fournies sont exactes.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Soumettre ma candidature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #0d4f3a;
            --secondary-green: #1a6b4f;
            --accent-green: #26d0ce;
            --light-green: #34d399;
            --dark-bg: #0a0a0a;
            --card-bg: rgba(13, 79, 58, 0.15);
            --glass-bg: rgba(255, 255, 255, 0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--dark-bg); color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; position: relative; }
        .main-container { position: relative; z-index: 1; min-height: 100vh; backdrop-filter: blur(10px); }
        .sidebar { background: linear-gradient(145deg, var(--card-bg), rgba(26, 107, 79, 0.2)); backdrop-filter: blur(15px); border-right: 1px solid rgba(52, 211, 153, 0.3); min-height: 100vh; box-shadow: 10px 0 30px rgba(0, 0, 0, 0.3); position: relative; }
        .sidebar::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(45deg, transparent 40%, rgba(52, 211, 153, 0.1) 50%, transparent 60%); animation: shimmer 3s ease-in-out infinite; }
        @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
        .content-area { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border-radius: 20px; margin: 20px; padding: 40px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4); border: 1px solid rgba(52, 211, 153, 0.2); position: relative; overflow: hidden; }
        .content-area::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: conic-gradient(from 0deg, transparent, rgba(52, 211, 153, 0.1), transparent); animation: rotate 10s linear infinite; z-index: -1; }
        @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .main-title { font-size: 3rem; font-weight: 800; background: linear-gradient(135deg, var(--light-green), var(--accent-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-align: center; margin-bottom: 30px; text-shadow: 0 0 30px rgba(52, 211, 153, 0.3); animation: titleGlow 2s ease-in-out infinite alternate; }
        @keyframes titleGlow { 0% { text-shadow: 0 0 30px rgba(52, 211, 153, 0.3); } 100% { text-shadow: 0 0 50px rgba(52, 211, 153, 0.6); } }
        .description { background: var(--glass-bg); backdrop-filter: blur(10px); border-radius: 15px; padding: 25px; margin-bottom: 40px; border: 1px solid rgba(52, 211, 153, 0.3); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); animation: slideInUp 1s ease-out; }
        @keyframes slideInUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .card-3d { transform-style: preserve-3d; transition: transform 0.6s ease; }
        .card-3d:hover { transform: rotateY(10deg) rotateX(5deg) scale(1.02); }
        .bg-glass { background: rgba(255,255,255,0.08)!important; backdrop-filter: blur(8px); }
        .table { color: #fff; }
        .table-bordered { border: 1px solid rgba(52,211,153,0.2); }
        .table-dark { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); border: none; }
        .table-dark th { border: none; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; padding: 20px 15px; position: relative; }
        .table-dark th::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background: linear-gradient(90deg, transparent, var(--accent-green), transparent); }
        .table tbody tr { background: rgba(255, 255, 255, 0.03); border: none; transition: all 0.3s ease; position: relative; }
        .table tbody tr:hover { background: rgba(52, 211, 153, 0.1); transform: translateY(-2px); box-shadow: 0 10px 25px rgba(52, 211, 153, 0.2); }
        .table td { border: none; padding: 20px 15px; vertical-align: middle; border-bottom: 1px solid rgba(52, 211, 153, 0.1); }
        .btn-primary, .btn-success, .btn-outline-primary { background: linear-gradient(135deg, var(--accent-green), var(--light-green)); border: none; font-weight: 700; letter-spacing: 1px; }
        .btn-primary:hover, .btn-success:hover, .btn-outline-primary:hover { background: linear-gradient(135deg, var(--light-green), var(--accent-green)); }
        .modal-content { background: var(--dark-bg); color: #fff; border: 1px solid rgba(52, 211, 153, 0.3); }
        .modal-header { border-bottom: 1px solid rgba(52, 211, 153, 0.3); }
        .modal-footer { border-top: 1px solid rgba(52, 211, 153, 0.3); }
        .form-control, .form-select { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #fff; }
        .form-control:focus, .form-select:focus { background: rgba(255, 255, 255, 0.15); border-color: var(--accent-green); color: #fff; box-shadow: 0 0 0 0.2rem rgba(52, 211, 153, 0.25); }
        .form-control::placeholder { color: rgba(255, 255, 255, 0.6); }
        @media (max-width: 768px) { .main-title { font-size: 2rem; } .content-area { margin: 10px; padding: 20px; } .table th, .table td { padding: 8px; } }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetails(details) {
            alert('Détails de l\'offre : ' + details);
        }
    </script>
@endpush

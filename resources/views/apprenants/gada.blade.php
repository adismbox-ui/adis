@extends('apprenants.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-lg mb-4 border-0">
                <div class="card-header bg-gradient-success text-white text-center" style="background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);">
                    <h2 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Tableau de bord Apprenant</h2>
                </div>
                <div class="card-body">
                    <!-- Informations personnelles -->
                    <div class="card shadow-lg mb-4 border-0">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0"><i class="fas fa-id-card me-2"></i>Informations personnelles</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-0">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Nom :</strong> {{ $user->nom ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Prénom :</strong> {{ $user->prenom ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Email :</strong> {{ $user->email ?? '-' }}</li>
                                        <li class="list-group-item"><strong>Téléphone :</strong> {{ $user->telephone ?? '-' }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Statut :</strong> <span class="badge bg-info text-dark">{{ ucfirst($user->type_compte ?? 'apprenant') }}</span></li>
                                        <li class="list-group-item"><strong>Niveau :</strong> {{ $apprenant && $apprenant->niveau ? (is_object($apprenant->niveau) ? $apprenant->niveau->nom : $apprenant->niveau) : '-' }}</li>
                                        <li class="list-group-item"><strong>Année d’inscription :</strong> {{ $apprenant && $apprenant->created_at ? $apprenant->created_at->format('Y') : '-' }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Formations en cours -->
                    <div class="card shadow-lg mb-4 border-0">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0"><i class="fas fa-book-open me-2"></i>Formations en cours</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-0">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Titre de la formation</th>
                                            <th>Progression</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Date évaluation finale</th>
                                            <th>Composer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inscriptionsEnCours as $insc)
                                            <tr>
                                                <td>{{ $insc->module->titre ?? '-' }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $insc->progression ?? 10 }}%">{{ $insc->progression ?? 10 }}%</div>
                                                    </div>
                                                </td>
                                                <td>{{ $insc->date_debut ?? '-' }}</td>
                                                <td>{{ $insc->date_fin_prevue ?? '-' }}</td>
                                                <td>{{ $insc->date_evaluation_finale ?? '-' }}</td>
                                                <td>
                                                    <a href="#" class="btn btn-outline-primary btn-sm">Composer</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center">Aucune formation en cours</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Formations terminées -->
                    <div class="card shadow-lg mb-4 border-0">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i>Formations terminées</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-0">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Titre de la formation</th>
                                            <th>Progression</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Décision</th>
                                            <th>Certificat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inscriptionsTerminees as $insc)
                                            <tr>
                                                <td>{{ $insc->module->titre ?? '-' }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $insc->progression ?? 100 }}%">{{ $insc->progression ?? 100 }}%</div>
                                                    </div>
                                                </td>
                                                <td>{{ $insc->date_debut ?? '-' }}</td>
                                                <td>{{ $insc->date_fin ?? '-' }}</td>
                                                <td>
                                                    @if($insc->decision ?? null)
                                                        <span class="badge bg-success">Admis</span>
                                                    @else
                                                        <span class="badge bg-danger">Non admis</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $cert = $certificats->firstWhere('module_id', $insc->module_id);
                                                    @endphp
                                                    @if($cert)
                                                        <a href="#" class="btn btn-sm btn-outline-success"><i class="fas fa-download"></i> Disponible</a>
                                                    @else
                                                        <span class="text-muted">Non disponible</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center">Aucune formation terminée</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Suggestions de formations non entamées -->
                    <div class="card shadow-lg mb-4 border-0">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Suggestions de modules</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-0">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-warning">
                                        <tr>
                                            <th>Titre de la formation</th>
                                            <th>Niveau</th>
                                            <th>Durée</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Montant</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($modules as $module)
                                            <tr>
                                                <td>{{ $module->titre }}</td>
                                                <td>{{ $module->niveau->nom ?? '-' }}</td>
                                                <td>{{ $module->duree ?? '-' }}</td>
                                                <td>{{ $module->date_debut ?? '-' }}</td>
                                                <td>{{ $module->date_fin ?? '-' }}</td>
                                                <td>{{ $module->prix ?? 'Gratuit' }} F CFA</td>
                                                <td>
                                                    <a href="{{ route('apprenants.inscription', ['module_id' => $module->id]) }}" class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-plus-circle"></i> S'inscrire
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Ressources pédagogiques -->
                    <div class="card shadow-lg mb-4 border-0">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0"><i class="fas fa-folder-open me-2"></i>Ressources pédagogiques</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-0">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-info">
                                        <tr>
                                            <th>Titre du document</th>
                                            <th>Module</th>
                                            <th>Formateur</th>
                                            <th>Télécharger</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($documents as $doc)
                                            <tr>
                                                <td>{{ $doc->titre }}</td>
                                                <td>{{ $doc->module->titre ?? '-' }}</td>
                                                <td>{{ $doc->formateur_nom ?? 'Non renseigné' }}</td>
                                                <td>
                                                    <a href="{{ asset('storage/' . $doc->fichier) }}" class="btn btn-outline-primary btn-sm" download>
                                                        <i class="fas fa-download"></i> Télécharger
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center">Aucun document proposé pour vos formations</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <h4 class="mb-3"><i class="fas fa-bell me-2"></i>Notifications</h4>
                    <ul class="list-group mb-4">
                        @forelse($notifications as $notif)
                            <li class="list-group-item"><i class="fas fa-info-circle text-primary me-2"></i>{{ $notif }}</li>
                        @empty
                            <li class="list-group-item text-muted">Aucune notification</li>
                        @endforelse
                    </ul>

                    <!-- Paramètres du compte -->
                    <h4 class="mb-3"><i class="fas fa-cog me-2"></i>Paramètres du compte</h4>
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><a href="#"><i class="fas fa-user-edit me-2"></i>Modifier les informations personnelles</a></li>
                        <li class="list-group-item"><a href="#"><i class="fas fa-key me-2"></i>Changer le mot de passe</a></li>
                        <li class="list-group-item"><a href="#"><i class="fas fa-bell me-2"></i>Préférences de notification</a></li>
                    </ul>

                    <!-- Tableau de compositions -->
                    <h4 class="mb-3"><i class="fas fa-table me-2"></i>Compositions</h4>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>N° SEMAINE</th>
                                    @for($i=1; $i<=12; $i++)
                                        <th>{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Compositions</td>
                                    @for($i=1; $i<=12; $i++)
                                        <td></td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>

</div>
</div>
</div>
@endsection 
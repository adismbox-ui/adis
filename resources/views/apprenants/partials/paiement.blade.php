<!-- Paiement Mobile Money (partiel) -->
<div class="table-responsive mb-4">
    <table class="table table-modern align-middle">
        <thead>
            <tr>
                <th><i class="fas fa-book me-2"></i>Module</th>
                <th><i class="fas fa-money-bill me-2"></i>Montant</th>
                <th><i class="fas fa-credit-card me-2"></i>Méthode</th>
                <th><i class="fas fa-calendar me-2"></i>Date</th>
                <th><i class="fas fa-info-circle me-2"></i>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paiements->take(5) as $paiement)
                <tr>
                    <td>
                        <span class="badge-animated">
                            <i class="fas fa-book me-1"></i>
                            {{ $paiement->module->titre ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-animated">
                            <i class="fas fa-money-bill me-1"></i>
                            {{ $paiement->montant }} FCFA
                        </span>
                    </td>
                    <td>
                        <span class="badge-animated">
                            <i class="fas fa-credit-card me-1"></i>
                            {{ ucfirst($paiement->methode) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-animated">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $paiement->date_paiement }}
                        </span>
                    </td>
                    <td>
                        @if($paiement->statut == 'valide')
                            <span class="badge-animated bg-success">
                                <i class="fas fa-check-circle me-1"></i>Validé
                            </span>
                        @elseif($paiement->statut == 'en_attente')
                            <span class="badge-animated bg-warning">
                                <i class="fas fa-clock me-1"></i>En attente
                            </span>
                        @else
                            <span class="badge-animated bg-danger">
                                <i class="fas fa-times-circle me-1"></i>Refusé
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="alert alert-custom">
                            <i class="fas fa-info-circle fa-2x mb-3 text-success"></i>
                            <h5>Aucun paiement enregistré</h5>
                            <p class="mb-0">Vous n'avez pas encore effectué de paiements.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
                    
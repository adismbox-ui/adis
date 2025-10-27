@extends('apprenants.layout')
@section('content')
<div class="container py-4">
    <a href="{{ url()->previous() }}" class="btn btn-outline-primary mb-3"><i class="fas fa-arrow-left me-2"></i>Retour</a>
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-dark">
            <h3 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Achat de module</h3>
        </div>
        <div class="card-body text-center">
            <i class="fas fa-shopping-cart fa-4x text-warning mb-3"></i>
            <p class="lead">Effectuez ici l'achat de vos modules de formation.</p>
        </div>
    </div>
</div>
@endsection 
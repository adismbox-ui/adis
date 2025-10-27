@extends('apprenants.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0"><i class="fas fa-book me-2"></i>Mes cours</h2>
                </div>
                <div class="card-body">
                    <p class="lead">Bienvenue sur la page de vos cours. Ici, vous retrouverez la liste de vos cours suivis ou disponibles.</p>
                    <!-- Exemple de liste de cours -->
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cours de lecture coranique
                            <a href="#" class="btn btn-outline-primary btn-sm">Voir le cours</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cours de jurisprudence islamique
                            <a href="#" class="btn btn-outline-primary btn-sm">Voir le cours</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cours d'éducation islamique
                            <a href="#" class="btn btn-outline-primary btn-sm">Voir le cours</a>
                        </li>
                    </ul>
                    <!-- Fin exemple -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Page Certificat Test')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-certificate me-3"></i>
                        Page Certificat Test
                    </h2>
                </div>
                
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                        <h4>Bienvenue sur la page Certificat</h4>
                        <p class="text-muted">Cette page permet de tester l'accès aux certificats</p>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-graduate me-2"></i>
                                        Pour les Apprenants
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p>Si vous êtes un apprenant connecté, vous pouvez accéder à votre certificat personnalisé.</p>
                                    @auth
                                        @if(auth()->user()->type_compte === 'apprenant')
                                            <a href="{{ route('apprenants.certificat.show', 1) }}" class="btn btn-primary">
                                                <i class="fas fa-eye me-2"></i>
                                                Voir mon certificat
                                            </a>
                                        @else
                                            <p class="text-muted small">Vous n'êtes pas un apprenant</p>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            Se connecter
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-shield me-2"></i>
                                        Pour les Administrateurs
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p>Si vous êtes un administrateur, vous pouvez accéder au générateur de certificats.</p>
                                    @auth
                                        @if(auth()->user()->type_compte === 'admin')
                                            <a href="{{ route('admin.certificats.generator', 1) }}" class="btn btn-success">
                                                <i class="fas fa-cogs me-2"></i>
                                                Générateur de certificats
                                            </a>
                                        @else
                                            <p class="text-muted small">Vous n'êtes pas un administrateur</p>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-success">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            Se connecter
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note :</strong> Cette page est accessible à tous, mais les fonctionnalités spécifiques nécessitent une authentification.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

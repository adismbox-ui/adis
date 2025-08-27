@extends('layouts.app')

@section('content')
<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<div class="container-fluid main-container">
    <div class="row">
        <div class="col-md-3 p-0">
            <div class="sidebar">
                <div class="p-4">
                    <h3 class="text-center mb-4" style="color: var(--accent-green);"><i class="fas fa-project-diagram me-2"></i>Navigation</h3>
                    @include('layouts.projets_sidebar')
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="content-area card-3d">
                <h1 class="main-title"><i class="fas fa-building me-3"></i>ENREGISTRER MON ENTREPRISE</h1>
                <div class="card" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(52, 211, 153, 0.2);">
                    <div class="card-body">
                        <form method="POST" action="{{ route('partenaires.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de l'entreprise</label>
                                <input type="text" name="nom" id="nom" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="text" name="telephone" id="telephone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="site_web" class="form-label">Site web</label>
                                <input type="text" name="site_web" id="site_web" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i> Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
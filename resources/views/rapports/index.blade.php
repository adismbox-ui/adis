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
                <h1 class="main-title"><i class="fas fa-chart-bar me-3"></i>RAPPORTS ET BILANS</h1>
                <div class="modern-table">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar me-2"></i>Année</th>
                                    <th><i class="fas fa-file-alt me-2"></i>Titre</th>
                                    <th><i class="fas fa-clipboard-list me-2"></i>Projet</th>
                                    <th><i class="fas fa-calendar-check me-2"></i>Publication</th>
                                    <th><i class="fas fa-download me-2"></i>Téléchargement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rapports as $rapport)
                                    <tr>
                                        <td><strong>{{ $rapport->annee }}</strong></td>
                                        <td>{{ $rapport->titre }}</td>
                                        <td>{{ $rapport->projet->intitule ?? '' }}</td>
                                        <td>{{ $rapport->date_publication }}</td>
                                        <td><a href="{{ asset('storage/'.$rapport->fichier) }}" class="btn btn-success btn-sm" download><i class="fas fa-download"></i></a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">Aucun rapport disponible.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-green: #0d4f3a; --secondary-green: #1a6b4f; --accent-green: #26d0ce; --light-green: #34d399; --dark-bg: #0a0a0a; --card-bg: rgba(13, 79, 58, 0.15); --glass-bg: rgba(255, 255, 255, 0.05); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--dark-bg); color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; position: relative; }
        .main-container { position: relative; z-index: 1; min-height: 100vh; backdrop-filter: blur(10px); }
        .sidebar { background: linear-gradient(145deg, var(--card-bg), rgba(26, 107, 79, 0.2)); backdrop-filter: blur(15px); border-right: 1px solid rgba(52, 211, 153, 0.3); min-height: 100vh; box-shadow: 10px 0 30px rgba(0, 0, 0, 0.3); position: relative; }
        .content-area { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border-radius: 20px; margin: 20px; padding: 40px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4); border: 1px solid rgba(52, 211, 153, 0.2); position: relative; overflow: hidden; }
        .main-title { font-size: 3rem; font-weight: 800; background: linear-gradient(135deg, var(--light-green), var(--accent-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-align: center; margin-bottom: 30px; }
        .modern-table { background: var(--glass-bg); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; border: 1px solid rgba(52, 211, 153, 0.3); }
        .table-dark { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); border: none; }
        .table tbody tr:hover { background: rgba(52, 211, 153, 0.1); }
    </style>
@endpush
@endsection
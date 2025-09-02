@extends('admin.layout')

@section('content')
<style>
    /* Background avec image et overlay */
    body {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(22, 101, 52, 0.9)), 
                    url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Titre principal */
    h1 {
        color: #ffffff;
        font-size: 3.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        background: linear-gradient(135deg, #10b981, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
        from { filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.5)); }
        to { filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.8)); }
    }

    /* Container du tableau */
    .table-container {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        animation: fadeInUp 0.8s ease-out;
    }

    /* Table principale */
    .table {
        background: transparent;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border: none;
        margin: 0;
    }

    /* En-têtes du tableau */
    .table thead tr th {
        background: linear-gradient(135deg, #065f46, #047857);
        color: #ffffff;
        font-weight: 600;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 20px 15px;
        border: none;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        position: relative;
        border-bottom: 3px solid #10b981;
    }

    .table thead tr th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #10b981, transparent);
    }

    /* Cellules du tableau */
    .table tbody tr td {
        background: rgba(30, 41, 59, 0.8);
        color: #e2e8f0;
        font-weight: 500;
        font-size: 15px;
        padding: 18px 15px;
        border: 1px solid rgba(16, 185, 129, 0.1);
        transition: all 0.3s ease;
        vertical-align: middle;
    }

    /* Effet hover sur les lignes */
    .table tbody tr { transition: all 0.3s ease; }
    .table tbody tr:hover {
        background: rgba(16, 185, 129, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
    }

    /* Particules décoratives */
    .particles { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: -1; }
    .particle { position: absolute; width: 4px; height: 4px; background: #10b981; border-radius: 50%; animation: float 6s ease-in-out infinite; opacity: 0.6; }
    @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-20px) rotate(180deg); } }

    @keyframes fadeInUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<!-- Particules -->
<div class="particles">
    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 25%; animation-delay: 1s;"></div>
    <div class="particle" style="left: 40%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 60%; animation-delay: 3s;"></div>
    <div class="particle" style="left: 80%; animation-delay: 4s;"></div>
</div>

<div class="container">
    <h1>Présence - Admin</h1>

    <div class="table-container">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Séance</th>
                    <th>Formateur</th>
                    <th>Statut</th>
                    <th>Télécharger</th>
                </tr>
            </thead>
            <tbody>
            @forelse($openRequests as $req)
                <tr>
                    <td>{{ $req->nom ?? ('Séance #' . $req->id) }}</td>
                    <td>{{ optional($req->formateur->utilisateur)->nom }} {{ optional($req->formateur->utilisateur)->prenom }}</td>
                    <td>
                        @if($req->is_open)
                            <span class="badge bg-success">Ouverte</span>
                        @else
                            <span class="badge bg-secondary">Fermée</span>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-sm btn-info" href="{{ route('admin.presence.pdf', $req) }}">
                            <i class="fas fa-file-pdf me-1"></i> Télécharger PDF
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-3">Aucune présence.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


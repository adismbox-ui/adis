@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 p-0">
            @include('layouts.projets_sidebar')
        </div>
        <div class="col-md-9">
            <div class="p-4">
                <h1>PROJETS A FINANCER</h1>
                
                <div class="mb-4">
                    <p class="lead">
                        Découvrez les initiatives en attente de votre soutien pour voir le jour. 
                        Chaque contribution, petite ou grande, fait une réelle différence dans la vie de nos bénéficiaires. 
                        Rejoignez-nous dans cette aventure solidaire et participons ensemble à la réussite de ces projets porteurs d'espoir.
                    </p>
                </div>

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Intitulé du projet</th>
                            <th>Bénéficiaires</th>
                            <th>Objectif</th>
                            <th>Montant total</th>
                            <th>Montant collecté</th>
                            <th>Reste à financer</th>
                            <th>Date limite</th>
                            <th>Faire un don</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($projets->count() > 0)
                        @foreach($projets as $projet)
                            <tr>
                                <td>{{ $projet->intitule }}</td>
                                <td>{{ $projet->beneficiaires }}</td>
                                <td>{{ $projet->objectif }}</td>
                                <td>{{ number_format($projet->montant_total, 0, ',', ' ') }} F CFA</td>
                                <td>{{ number_format($projet->montant_collecte, 0, ',', ' ') }} F CFA</td>
                                <td>{{ number_format($projet->reste_a_financer, 0, ',', ' ') }} F CFA</td>
                                <td>{{ $projet->date_limite }}</td>
                                <td>
                                    <a href="{{ route('projets.don') }}" class="btn btn-success btn-sm">Faire un don</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Aucun projet à financer pour le moment.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
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
        .modern-table { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); border-radius: 20px; box-shadow: 0 10px 30px rgba(13, 79, 58, 0.2); padding: 30px; margin-bottom: 40px; }
        .table { color: #fff; }
        .table-bordered { border: 1px solid rgba(52,211,153,0.2); }
        .table-dark { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); border: none; }
        .table-dark th { border: none; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; padding: 20px 15px; position: relative; }
        .table-dark th::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background: linear-gradient(90deg, transparent, var(--accent-green), transparent); }
        .table tbody tr { background: rgba(255, 255, 255, 0.03); border: none; transition: all 0.3s ease; position: relative; }
        .table tbody tr:hover { background: rgba(52, 211, 153, 0.1); transform: translateY(-2px); box-shadow: 0 10px 25px rgba(52, 211, 153, 0.2); }
        .table td { border: none; padding: 20px 15px; vertical-align: middle; border-bottom: 1px solid rgba(52, 211, 153, 0.1); }
        .btn-success { background: linear-gradient(135deg, var(--accent-green), var(--light-green)); border: none; font-weight: 700; letter-spacing: 1px; }
        .btn-success:hover { background: linear-gradient(135deg, var(--light-green), var(--accent-green)); }
        @media (max-width: 768px) { .main-title { font-size: 2rem; } .content-area { margin: 10px; padding: 20px; } .modern-table { padding: 15px; } }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection
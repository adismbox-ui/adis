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
                <h1 class="main-title"><i class="fas fa-images me-3"></i>GALERIE PHOTOS & VIDÉOS</h1>
                <div class="row g-4">
                    @forelse($galeries as $media)
                        <div class="col-md-4">
                            <div class="card h-100" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(52, 211, 153, 0.2);">
                                @if($media->type === 'photo')
                                    <img src="{{ asset('storage/'.$media->url) }}" class="card-img-top" alt="{{ $media->titre }}">
                                @else
                                    <video controls class="w-100">
                                        <source src="{{ asset('storage/'.$media->url) }}" type="video/mp4">
                                        Votre navigateur ne supporte pas la vidéo.
                                    </video>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $media->titre }}</h5>
                                    <p class="card-text">{{ $media->description }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted">Aucun média disponible.</div>
                    @endforelse
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
        body { background: var(--dark-bg); color: #ffffff; overflow-x: hidden; position: relative; }
        .main-container { position: relative; z-index: 1; min-height: 100vh; backdrop-filter: blur(10px); }
        .sidebar { background: linear-gradient(145deg, var(--card-bg), rgba(26, 107, 79, 0.2)); backdrop-filter: blur(15px); border-right: 1px solid rgba(52, 211, 153, 0.3); min-height: 100vh; box-shadow: 10px 0 30px rgba(0, 0, 0, 0.3); }
        .content-area { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border-radius: 20px; margin: 20px; padding: 40px; border: 1px solid rgba(52, 211, 153, 0.2); }
        .main-title { font-size: 3rem; font-weight: 800; background: linear-gradient(135deg, var(--light-green), var(--accent-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-align: center; margin-bottom: 30px; }
    </style>
@endpush
@endsection
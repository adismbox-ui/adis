@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Galerie (Admin)</h1>
    <a href="{{ route('admin.galeries.create') }}" class="btn btn-success">
        <i class="fas fa-plus-circle me-2"></i>Ajouter un média
    </a>
  </div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4">
@forelse($galeries as $media)
  <div class="col-md-4">
    <div class="card h-100">
      @if($media->type === 'photo')
        <img src="{{ asset('storage/'.$media->url) }}" class="card-img-top" alt="{{ $media->titre }}">
      @else
        <video controls class="w-100">
            <source src="{{ asset('storage/'.$media->url) }}" type="video/mp4" />
        </video>
      @endif
      <div class="card-body">
        <h5 class="card-title">{{ $media->titre }}</h5>
        <p class="card-text">{{ $media->description }}</p>
      </div>
    </div>
  </div>
@empty
  <div class="col-12"><div class="alert alert-info">Aucun média pour l'instant.</div></div>
@endforelse
</div>
@endsection


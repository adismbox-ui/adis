@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Changer le logo</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form method="POST" action="{{ route('admin.logo.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 text-center">
                            <img src="{{ $adminLogo ? asset('storage/'.$adminLogo) : '/photo_2025-07-02_10-44-47.jpg' }}" alt="Logo actuel" style="max-width:120px; border-radius:12px; box-shadow:0 2px 8px #0002; margin-bottom:1rem;">
                        </div>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Sélectionner un nouveau logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*" required>
                            @error('logo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-upload me-1"></i> Mettre à jour</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

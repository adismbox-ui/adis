@extends('admin.layout')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin-create-dark.css') }}" />
@endsection

@section('content')

<!-- Fond animé et particules -->
<div class="bg-animated"></div>
<div class="floating-particles" id="particles"></div>

<!-- Conteneur principal -->
<div class="main-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card slide-in-up">
                <div class="card-header">
                    <h3>
                        <i class="{{ $icon ?? 'fas fa-plus' }}"></i>
                        {{ $title ?? 'Créer un élément' }}
                    </h3>
                </div>
                <div class="card-body">
                    @yield('form-content')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Génération des particules
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('particles');
    if (container) {
        for (let i = 0; i < 15; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 40 + 20;
            p.style.width = size + 'px';
            p.style.height = size + 'px';
            p.style.left = (Math.random() * 100) + 'vw';
            p.style.top = (Math.random() * 100) + 'vh';
            p.style.animationDelay = (Math.random() * 15) + 's';
            p.style.animationDuration = (Math.random() * 10 + 15) + 's';
            container.appendChild(p);
        }
    }
});

// Fonctions utilitaires communes
function showSuccessMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-success fade-in';
    alert.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        ${message}
    `;
    document.querySelector('.card-body').insertBefore(alert, document.querySelector('.card-body').firstChild);
    setTimeout(() => alert.remove(), 5000);
}

function showErrorMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger fade-in';
    alert.innerHTML = `
        <i class="fas fa-exclamation-circle me-2"></i>
        ${message}
    `;
    document.querySelector('.card-body').insertBefore(alert, document.querySelector('.card-body').firstChild);
    setTimeout(() => alert.remove(), 5000);
}
</script>

@endsection 
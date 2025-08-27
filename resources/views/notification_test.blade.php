@extends('apprenants.layout')

@section('content')
<style>
    :root {
        --primary-green: #22c55e;
        --dark-green: #16a34a;
        --light-green: #86efac;
        --grass-green: #15803d;
        --bg-overlay: rgba(0, 0, 0, 0.7);
    }

    body {
        background: linear-gradient(135deg, #1e3a8a 0%, #16a34a 50%, #22c55e 100%);
        background-attachment: fixed;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
        font-family: 'Poppins', sans-serif;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
            radial-gradient(circle at 25% 25%, rgba(34, 197, 94, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(22, 163, 74, 0.3) 0%, transparent 50%),
            url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        animation: backgroundFlow 20s ease-in-out infinite;
        z-index: -1;
    }

    @keyframes backgroundFlow {
        0%, 100% { transform: translateX(0) translateY(0); }
        25% { transform: translateX(-20px) translateY(-10px); }
        50% { transform: translateX(20px) translateY(-20px); }
        75% { transform: translateX(-10px) translateY(10px); }
    }

    .container {
        position: relative;
        z-index: 1;
    }

    .floating-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }

    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: var(--light-green);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
        opacity: 0.6;
    }

    .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
    .particle:nth-child(2) { left: 20%; animation-delay: 1s; }
    .particle:nth-child(3) { left: 30%; animation-delay: 2s; }
    .particle:nth-child(4) { left: 40%; animation-delay: 3s; }
    .particle:nth-child(5) { left: 50%; animation-delay: 4s; }
    .particle:nth-child(6) { left: 60%; animation-delay: 5s; }
    .particle:nth-child(7) { left: 70%; animation-delay: 0.5s; }
    .particle:nth-child(8) { left: 80%; animation-delay: 1.5s; }
    .particle:nth-child(9) { left: 90%; animation-delay: 2.5s; }

    @keyframes float {
        0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
        10% { opacity: 0.6; }
        90% { opacity: 0.6; }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .main-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        animation: slideInUp 1s ease-out;
        overflow: hidden;
        position: relative;
    }

    .main-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--primary-green), transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    @keyframes slideInUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: rotate 4s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .card-header h2 {
        position: relative;
        z-index: 2;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        animation: textGlow 2s ease-in-out infinite alternate;
    }

    @keyframes textGlow {
        from { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); }
        to { text-shadow: 2px 2px 8px rgba(255, 255, 255, 0.5); }
    }

    .notification-item {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(240, 255, 244, 0.9));
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 20px;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(5px);
    }

    .notification-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .notification-item:hover::before {
        left: 100%;
    }

    .notification-item:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 35px rgba(34, 197, 94, 0.2);
        border-color: var(--primary-green);
    }

    .notification-item.unread {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 248, 220, 0.9));
        border-color: var(--primary-green);
        box-shadow: 0 5px 15px rgba(34, 197, 94, 0.2);
    }

    .badge-animated {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.9em;
        margin: 2px;
        display: inline-block;
        animation: bounceIn 0.8s ease-out;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(34, 197, 94, 0.3);
    }

    .badge-animated:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.5);
    }

    .badge-animated.unread {
        background: linear-gradient(135deg, #dc3545, #c82333);
        animation: pulse 2s infinite;
    }

    .badge-animated.questionnaire {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .badge-animated.document {
        background: linear-gradient(135deg, #17a2b8, #6f42c1);
    }

    .badge-animated.upcoming {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes bounceIn {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.05); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); opacity: 1; }
    }

    .btn-mark-read {
        background: linear-gradient(135deg, var(--grass-green), var(--dark-green));
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-mark-read:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 20px rgba(21, 128, 61, 0.4);
        color: white;
    }

    .alert-custom {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(134, 239, 172, 0.2));
        border: 1px solid var(--primary-green);
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        animation: pulse 2s infinite;
    }

    .notification-item:nth-child(1) { animation-delay: 0.1s; }
    .notification-item:nth-child(2) { animation-delay: 0.2s; }
    .notification-item:nth-child(3) { animation-delay: 0.3s; }
    .notification-item:nth-child(4) { animation-delay: 0.4s; }
    .notification-item:nth-child(5) { animation-delay: 0.5s; }

    .notification-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .btn-action {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 20px;
        padding: 6px 12px;
        color: white;
        font-size: 0.8em;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
        color: white;
    }

    @media (max-width: 768px) {
        .main-card {
            margin: 10px;
            border-radius: 15px;
        }
    }
</style>

<div class="floating-particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-11">
            <div class="card main-card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-bell me-3"></i>Mes Notifications
                    </h2>
                    @if($apprenant)
                        <small class="text-white-50">Niveau : {{ $apprenant->niveau->nom }}</small>
                    @endif
                </div>
                <div class="card-body p-5">
                    <div id="notifications-list">
                        @if($notifications->count() === 0)
                            <div class="alert alert-custom text-center">
                                <i class="fas fa-info-circle fa-2x mb-3 text-success"></i>
                                <h5>Aucune notification</h5>
                                <p class="mb-0">Aucune notification pour l'instant.</p>
                            </div>
                        @else
                            <div class="notifications-list">
                                @foreach($notifications as $i => $notification)
                                    <div class="notification-item @if(!$notification->is_read) unread @endif" data-notification-id="{{ $notification->id }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge-animated @if(!$notification->is_read) unread @endif {{ $notification->type }}">
                                                    <i class="{{ $notification->icon }}"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-bold text-success">{{ $notification->title }}</div>
                                                    <div class="text-muted small">{{ $notification->message }}</div>
                                                    <div class="text-muted fst-italic small">
                                                        <i class="far fa-clock me-1"></i> {{ $notification->time_ago }}
                                                    </div>
                                                    @if($notification->action_url)
                                                        <div class="notification-actions">
                                                            <a href="{{ $notification->action_url }}" class="btn btn-action">
                                                                <i class="fas fa-external-link-alt me-1"></i>Voir
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(!$notification->is_read)
                                                <button type="button" class="btn btn-mark-read mark-as-read-btn" data-notification-id="{{ $notification->id }}">
                                                    <i class="fas fa-check me-1"></i>Marquer comme lue
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const badges = document.querySelectorAll('.badge-animated');
    badges.forEach((badge, index) => {
        badge.style.animationDelay = (index * 0.1) + 's';
        
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });

    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach((item, index) => {
        item.style.animationName = 'slideInUp';
        item.style.animationDuration = '0.8s';
        item.style.animationDelay = (index * 0.1) + 's';
        item.style.animationFillMode = 'both';
    });

    let ticking = false;
    function updateParticles() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelectorAll('.particle');
        const speed = scrolled * 0.5;

        parallax.forEach((particle, index) => {
            const yPos = -(speed / (index + 1));
            particle.style.transform = 'translateY(' + yPos + 'px)';
        });
        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateParticles);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestTick);
});

// Marquer une notification comme lue
document.querySelectorAll('.mark-as-read-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const notificationId = this.getAttribute('data-notification-id');
        const notificationItem = this.closest('.notification-item');
        
        // Appel AJAX pour marquer comme lue
        fetch(`/apprenants/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour l'interface
                notificationItem.classList.remove('unread');
                this.remove();
                
                const badge = notificationItem.querySelector('.badge-animated');
                badge.classList.remove('unread');
                
                // Animation de confirmation
                notificationItem.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    notificationItem.style.transform = 'scale(1)';
                }, 200);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    });
});
</script>
@endsection

@extends('admin.layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<style>
    .table tbody tr td {
        color:rgb(15, 224, 92) !important;
        font-weight: 500;
        font-size: 14px;
        background-color: rgba(11, 8, 8, 0.9) !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    .table thead tr th {
        color: #15803d !important;
        font-weight: 600;
        background-color: rgba(255, 255, 255, 0.95) !important;
    }
    .table td, .table td * {
        color: #22c55e !important;
        background-color: rgba(255, 255, 255, 0.9) !important;
    }
    .table {
        background-color: rgba(255, 255, 255, 0.8) !important;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="notifications-page">
    <!-- Header Section -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-bell me-3"></i>
                    Notifications
                </h1>
                <p class="page-subtitle">Gérez toutes vos notifications système</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline-success me-2" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-2"></i>
                    Marquer toutes comme lues
                </button>
                <button class="btn btn-success" onclick="refreshNotifications()">
                    <i class="fas fa-sync-alt me-2"></i>
                    Actualiser
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ count($notifications) }}</h3>
                    <p>Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ count(array_filter($notifications, fn($n) => !$n['is_read'])) }}</h3>
                    <p>Non lues</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ count(array_filter($notifications, fn($n) => $n['created_at']->isToday())) }}</h3>
                    <p>Aujourd'hui</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ count(array_filter($notifications, fn($n) => $n['created_at']->isCurrentWeek())) }}</h3>
                    <p>Cette semaine</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <select class="form-select" id="typeFilter">
                            <option value="">Tous les types</option>
                            <option value="inscription">Inscriptions</option>
                            <option value="payment">Paiements</option>
                            <option value="session">Sessions</option>
                            <option value="document">Documents</option>
                            <option value="system">Système</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">Tous les statuts</option>
                            <option value="unread">Non lues</option>
                            <option value="read">Lues</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchFilter" placeholder="Rechercher dans les notifications...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                            <i class="fas fa-filter me-2"></i>
                            Filtrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        @forelse($notifications as $notification)
            <div class="notification-card {{ !$notification['is_read'] ? 'unread' : '' }}" data-id="{{ $notification['id'] }}" data-type="{{ $notification['type'] }}">
                <div class="notification-content">
                    <div class="notification-icon">
                        <div class="icon-wrapper bg-{{ $notification['color'] }}">
                            <i class="{{ $notification['icon'] }}"></i>
                        </div>
                        @if(!$notification['is_read'])
                            <div class="unread-dot"></div>
                        @endif
                    </div>
                    
                    <div class="notification-body">
                        <div class="notification-header">
                            <h5 class="notification-title">{{ $notification['title'] }}</h5>
                            <span class="notification-time">{{ $notification['created_at']->diffForHumans() }}</span>
                        </div>
                        <p class="notification-message">{{ $notification['message'] }}</p>
                        
                        @if(isset($notification['data']) && !empty($notification['data']))
                            <div class="notification-details">
                                @foreach($notification['data'] as $key => $value)
                                    <span class="detail-badge">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="notification-actions">
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification({{ $notification['id'] }})" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3>Aucune notification</h3>
                <p>Vous n'avez aucune notification pour le moment.</p>
            </div>
        @endforelse
    </div>
</div>

<div id="notif-toast" style="position:fixed;top:30px;right:30px;z-index:9999;display:none;min-width:220px;" class="alert"></div>

<style>
.notifications-page {
    padding: 2rem 0;
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #718096;
    font-size: 1.1rem;
    margin: 0;
}

.header-actions .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: #fff;
    font-size: 1.5rem;
}

.stat-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #2d3748;
}

.stat-content p {
    margin: 0;
    color: #718096;
    font-weight: 500;
}

.filters-section .card {
    border: none;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
}

.notification-card {
    background: #fff;
    border-radius: 12px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    overflow: hidden;
}

.notification-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.notification-card.unread {
    border-left: 4px solid #43ea4a;
    background: linear-gradient(90deg, rgba(67, 234, 74, 0.02) 0%, #fff 100%);
}

.notification-content {
    display: flex;
    align-items: flex-start;
    padding: 1.5rem;
}

.notification-icon {
    position: relative;
    margin-right: 1rem;
}

.icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.2rem;
}

.unread-dot {
    position: absolute;
    top: -3px;
    right: -3px;
    width: 12px;
    height: 12px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid #fff;
}

.notification-body {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.notification-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    flex: 1;
}

.notification-time {
    font-size: 0.85rem;
    color: #718096;
    white-space: nowrap;
    margin-left: 1rem;
}

.notification-message {
    color: #4a5568;
    margin-bottom: 0.75rem;
    line-height: 1.5;
}

.notification-details {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.detail-badge {
    background: #f7fafc;
    color: #4a5568;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.notification-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-left: 1rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #718096;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #4a5568;
}

@media (max-width: 768px) {
    .notification-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .notification-actions {
        flex-direction: row;
        margin-left: 0;
    }
    
    .header-actions {
        margin-top: 1rem;
    }
    
    .header-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
function markAsRead(id) {
    fetch(`/admin/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.text().then(txt => {
        console.log('Réponse brute:', txt);
        try { return JSON.parse(txt); } catch(e) { return {success: false, message: txt}; }
    }))
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-id="${id}"]`);
            card.classList.remove('unread');
            card.querySelector('.unread-dot')?.remove();
            card.querySelector('.btn-outline-success')?.remove();
            updateSidebarBadge();
            showToast('Notification marquée comme lue !', 'success');
        } else {
            showToast(data.message || 'Erreur lors de la validation', 'danger');
        }
    })
    .catch(error => { showToast(error.message || 'Erreur JS lors de la validation', 'danger'); });
}

function markAllAsRead() {
    fetch('/admin/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-card.unread').forEach(card => {
                card.classList.remove('unread');
                card.querySelector('.unread-dot')?.remove();
                card.querySelector('.btn-outline-success')?.remove();
            });
            
            // Update sidebar badge
            updateSidebarBadge();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        fetch(`/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json().catch(() => ({success: false, message: 'Réponse serveur invalide'})))
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-id="${id}"]`).remove();
                updateSidebarBadge();
                showToast('Notification supprimée !', 'success');
            } else {
                showToast(data.message || 'Erreur lors de la suppression', 'danger');
            }
        })
        .catch(error => { showToast(error.message || 'Erreur JS lors de la suppression', 'danger'); });
    }
}

function refreshNotifications() {
    fetch('/admin/notifications/ajax')
        .then(response => response.json())
        .then(data => {
            const list = document.querySelector('.notifications-list');
            if (!list) return;
            list.innerHTML = '';
            if (data.notifications.length === 0) {
                list.innerHTML = `<div class="empty-state"><div class="empty-icon"><i class="fas fa-bell-slash"></i></div><h3>Aucune notification</h3><p>Vous n'avez aucune notification pour le moment.</p></div>`;
            } else {
                data.notifications.forEach(notification => {
                    const card = document.createElement('div');
                    card.className = `notification-card ${!notification.is_read ? 'unread' : ''}`;
                    card.setAttribute('data-id', notification.id);
                    card.setAttribute('data-type', notification.type);
                    card.innerHTML = `
                        <div class="notification-content">
                            <div class="notification-icon">
                                <div class="icon-wrapper bg-${notification.color}"><i class="${notification.icon}"></i></div>
                                ${!notification.is_read ? '<div class="unread-dot"></div>' : ''}
                            </div>
                            <div class="notification-body">
                                <div class="notification-header">
                                    <h5 class="notification-title">${notification.title}</h5>
                                    <span class="notification-time">${formatTime(notification.created_at)}</span>
                                </div>
                                <p class="notification-message">${notification.message}</p>
                                ${renderDetailBadges(notification.data)}
                            </div>
                            <div class="notification-actions">
                                <button class='btn btn-sm btn-outline-danger' onclick='deleteNotification(${notification.id})' title='Supprimer'><i class='fas fa-trash'></i></button>
                            </div>
                        </div>
                    `;
                    list.appendChild(card);
                });
            }
            updateSidebarBadge();
        });
}

// Ajoute une fonction utilitaire pour formater la date comme dans le backend
function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    if (diffInMinutes < 1) return "À l'instant";
    if (diffInMinutes < 60) return `Il y a ${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''}`;
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `Il y a ${diffInHours} heure${diffInHours > 1 ? 's' : ''}`;
    const diffInDays = Math.floor(diffInHours / 24);
    return `Il y a ${diffInDays} jour${diffInDays > 1 ? 's' : ''}`;
}

// Ajoute une fonction utilitaire pour générer les badges de détails
function renderDetailBadges(data) {
    if (!data || Object.keys(data).length === 0) return '';
    let html = '<div class="notification-details">';
    Object.entries(data).forEach(function(entry) {
        var k = entry[0], v = entry[1];
        html += `<span class='detail-badge'>${k.charAt(0).toUpperCase() + k.slice(1).replace('_',' ')}: ${v}</span>`;
    });
    html += '</div>';
    return html;
}

function updateSidebarBadge() {
    const unreadCount = document.querySelectorAll('.notification-card.unread').length;
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        badge.textContent = unreadCount;
        badge.style.display = unreadCount > 0 ? 'block' : 'none';
    }
}

function applyFilters() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    
    document.querySelectorAll('.notification-card').forEach(card => {
        let show = true;
        
        // Type filter
        if (typeFilter && card.dataset.type !== typeFilter) {
            show = false;
        }
        
        // Status filter
        if (statusFilter === 'unread' && !card.classList.contains('unread')) {
            show = false;
        } else if (statusFilter === 'read' && card.classList.contains('unread')) {
            show = false;
        }
        
        // Search filter
        if (searchFilter) {
            const title = card.querySelector('.notification-title').textContent.toLowerCase();
            const message = card.querySelector('.notification-message').textContent.toLowerCase();
            if (!title.includes(searchFilter) && !message.includes(searchFilter)) {
                show = false;
            }
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

// Real-time search
document.getElementById('searchFilter').addEventListener('input', applyFilters);
document.getElementById('typeFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

// Ajout du toast HTML juste avant </div> de .notifications-page
// Ajouter la fonction utilitaire pour afficher un toast
function showToast(message, type = 'success') {
    const toast = document.getElementById('notif-toast');
    toast.textContent = message;
    toast.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger');
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 2000);
}
</script>
@endsection

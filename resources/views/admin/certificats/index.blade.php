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

    /* Alert personnalisé */
    .alert {
        border: none;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        animation: slideInDown 0.6s ease-out;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9));
        color: white;
        border-left: 5px solid #10b981;
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
    .table tbody tr {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .table tbody tr:hover {
        background: rgba(16, 185, 129, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
    }

    .table tbody tr:hover td {
        background: rgba(16, 185, 129, 0.15);
        color: #ffffff;
        text-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    /* Boutons d'action */
    .btn {
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 3px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.6);
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.6);
        color: white;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
        color: white;
    }

    /* Badge pour le statut */
    .badge-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }

    /* Boutons d'action sans fond */
    .btn-action {
        background: none;
        border: none;
        color: #10b981;
        font-size: 1.2rem;
        padding: 8px;
        margin: 0 3px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        min-height: 40px;
    }

    .btn-action:hover {
        color: #059669;
        transform: scale(1.1);
        background: rgba(16, 185, 129, 0.1);
    }

    .btn-action:active {
        transform: scale(0.95);
    }

    /* Animations */
    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        h1 {
            font-size: 2.5rem;
        }
        
        .table-container {
            padding: 15px;
            margin: 10px;
        }
        
        .table {
            font-size: 14px;
        }
        
        .btn {
            padding: 6px 12px;
            font-size: 11px;
            margin: 2px;
        }
    }

    /* Effet de particules */
    .particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: #10b981;
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
        opacity: 0.6;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
</style>

<!-- Particules animées -->
<div class="particles">
    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
    <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
    <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
    <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
    <div class="particle" style="left: 70%; animation-delay: 6s;"></div>
    <div class="particle" style="left: 80%; animation-delay: 7s;"></div>
    <div class="particle" style="left: 90%; animation-delay: 8s;"></div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h1>🏆 Gestion des Certificats</h1>

    <div class="table-container" style="margin-bottom:20px;">
        <form method="GET" action="{{ route('admin.certificats.index') }}" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
            <div style="flex:1; min-width:260px;">
                <label for="search" style="color:#e2e8f0; font-weight:600;">Recherche</label>
                <input type="text" id="search" name="q" value="{{ $search ?? '' }}" placeholder="Nom, prénom, email, module, titre..." class="form-control" style="background: rgba(30,41,59,0.8); border:1px solid rgba(16,185,129,0.3); color:#e2e8f0;">
            </div>
            <div style="width:260px;">
                <label for="niveau_id" style="color:#e2e8f0; font-weight:600;">Filtrer par niveau</label>
                <select id="niveau_id" name="niveau_id" class="form-select" style="background: rgba(30,41,59,0.8); border:1px solid rgba(16,185,129,0.3); color:#e2e8f0;">
                    <option value="">Tous les niveaux</option>
                    @foreach(($niveaux ?? []) as $niveau)
                        <option value="{{ $niveau->id }}" {{ (string)($niveauId ?? '') === (string)$niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-success"><i class="fas fa-search me-1"></i> Rechercher</button>
            </div>
            @if(($search ?? '') || ($niveauId ?? ''))
            <div>
                <a href="{{ route('admin.certificats.index') }}" class="btn btn-warning"><i class="fas fa-rotate-left me-1"></i> Réinitialiser</a>
            </div>
            @endif
        </form>
        <div style="margin-top:15px; display:flex; justify-content:center;">
            {{ $certificats->links() }}
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-user me-2"></i>Apprenant</th>
                    <th><i class="fas fa-layer-group me-2"></i>Niveau</th>
                    <th><i class="fas fa-calendar me-2"></i>Date obtention</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($certificats as $certificat)
                    <tr>
                        <td>
                            <strong>{{ $certificat->apprenant->utilisateur->prenom ?? 'N/A' }} {{ $certificat->apprenant->utilisateur->nom ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-layer-group me-1"></i>{{ $certificat->apprenant->niveau->nom ?? '-' }}
                        </td>
                        <td>
                            <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.certificats.generator', $certificat) }}" class="btn-action" title="Générateur de certificat interactif">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.certificats.destroy', $certificat) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action" 
                                        onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce certificat ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Script pour les effets interactifs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Effet de clignotement pour les nouvelles notifications
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.animation = 'slideInDown 0.6s ease-out';
    });

    // Effet de survol amélioré pour les lignes du tableau
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des boutons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            let ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            let x = e.clientX - e.target.offsetLeft;
            let y = e.clientY - e.target.offsetTop;
            
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Gestion des boutons de génération avec état sauvegardé
    const generateWithStateBtns = document.querySelectorAll('.generate-with-state-btn');
    generateWithStateBtns.forEach(btn => {
        btn.addEventListener('click', async function() {
            const certificatId = this.getAttribute('data-certificat-id');
            const apprenantName = this.getAttribute('data-apprenant-name');
            const originalText = this.innerHTML;
            
            try {
                // Vérifier s'il y a un état sauvegardé
                const savedState = localStorage.getItem(`certificat_state_${certificatId}`);
                
                if (!savedState) {
                    alert('⚠️ Aucun état sauvegardé trouvé pour ce certificat.\n\nVeuillez d\'abord utiliser le générateur pour créer et sauvegarder un état personnalisé.');
                    return;
                }
                
                // Afficher l'état de chargement
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Génération...';
                this.disabled = true;
                
                // Récupérer l'état sauvegardé
                const state = JSON.parse(savedState);
                console.log('État sauvegardé trouvé:', state);
                
                // Log détaillé des couleurs pour déboguer
                console.log('🎨 Couleurs dans l\'état sauvegardé:');
                console.log('  Nom - couleur:', state.nom.color, 'texte:', state.nom.text);
                console.log('  Niveau - couleur:', state.niveau.color, 'texte:', state.niveau.text);
                console.log('  Période - couleur:', state.periode.color, 'texte:', state.periode.text);
                console.log('  Arrière-plan:', state.background);
                console.log('  Image personnalisée:', state.customBackgroundData ? 'Oui' : 'Non');
                
                // Vérifier que tous les éléments nécessaires sont présents
                if (!state.nom || !state.niveau || !state.periode) {
                    alert('⚠️ L\'état sauvegardé est incomplet.\n\nVeuillez retourner dans le générateur et sauvegarder à nouveau l\'état.');
                    this.innerHTML = originalText;
                    this.disabled = false;
                    return;
                }
                
                // Vérifier que les propriétés essentielles existent
                const requiredProps = ['top', 'left', 'fontSize', 'color', 'fontWeight', 'fontStyle', 'textDecoration', 'textTransform'];
                const hasAllProps = requiredProps.every(prop => 
                    state.nom[prop] !== undefined && 
                    state.niveau[prop] !== undefined && 
                    state.periode[prop] !== undefined
                );
                
                if (!hasAllProps) {
                    alert('⚠️ L\'état sauvegardé est incomplet.\n\nVeuillez retourner dans le générateur et sauvegarder à nouveau l\'état.');
                    this.innerHTML = originalText;
                    this.disabled = false;
                    return;
                }
                
                // Créer un certificat temporaire avec l'état sauvegardé EXACT
                const certificatDiv = document.createElement('div');
                certificatDiv.id = 'temp-certificat';
                
                // Utiliser EXCLUSIVEMENT l'état sauvegardé pour l'arrière-plan
                let backgroundStyle = '';
                if (state.customBackgroundData) {
                    backgroundStyle = `background: url("${state.customBackgroundData}") no-repeat center/cover`;
                } else if (state.background) {
                    backgroundStyle = `background: ${state.background} no-repeat center/cover`;
                } else {
                    backgroundStyle = `background: url("{{ asset("MODELE CERTIFICAT DE FORMATION.jpg") }}") no-repeat center/cover`;
                }
                
                certificatDiv.style.cssText = `
                    position: absolute;
                    left: -9999px;
                    width: 1086px;
                    height: 768px;
                    ${backgroundStyle};
                    overflow: hidden;
                `;
                
                // Créer les éléments de texte avec l'état sauvegardé EXACT
                const nomDiv = document.createElement('div');
                nomDiv.style.cssText = `
                    position: absolute;
                    top: ${state.nom.top};
                    left: ${state.nom.left};
                    transform: ${state.nom.left === '50%' ? 'translateX(-50%)' : 'none'};
                    font-size: ${state.nom.fontSize};
                    color: ${state.nom.color};
                    text-align: center;
                    min-width: 200px;
                    font-weight: ${state.nom.fontWeight};
                    font-style: ${state.nom.fontStyle};
                    text-decoration: ${state.nom.textDecoration};
                    text-transform: ${state.nom.textTransform};
                `;
                nomDiv.textContent = state.nom.text;
                
                // Log pour vérifier que la couleur est bien appliquée
                console.log('✅ Nom créé avec couleur:', nomDiv.style.color, 'et texte:', nomDiv.textContent);
                
                const niveauDiv = document.createElement('div');
                niveauDiv.style.cssText = `
                    position: absolute;
                    top: ${state.niveau.top};
                    left: ${state.niveau.left};
                    font-size: ${state.niveau.fontSize};
                    text-align: left;
                    color: ${state.niveau.color};
                    min-width: 150px;
                    font-weight: ${state.niveau.fontWeight};
                    font-style: ${state.niveau.fontStyle};
                    text-decoration: ${state.niveau.textDecoration};
                    text-transform: ${state.niveau.textTransform};
                `;
                niveauDiv.textContent = state.niveau.text;
                
                // Log pour vérifier que la couleur est bien appliquée
                console.log('✅ Niveau créé avec couleur:', niveauDiv.style.color, 'et texte:', niveauDiv.textContent);
                
                const periodeDiv = document.createElement('div');
                periodeDiv.style.cssText = `
                    position: absolute;
                    top: ${state.periode.top};
                    right: ${state.periode.right};
                    font-size: ${state.periode.fontSize};
                    text-align: left;
                    color: ${state.periode.color};
                    min-width: 150px;
                    font-weight: ${state.periode.fontWeight};
                    font-style: ${state.periode.fontStyle};
                    text-decoration: ${state.periode.textDecoration};
                    text-transform: ${state.periode.textTransform};
                `;
                periodeDiv.textContent = state.periode.text;
                
                // Log pour vérifier que la couleur est bien appliquée
                console.log('✅ Période créée avec couleur:', periodeDiv.style.color, 'et texte:', periodeDiv.textContent);
                
                // Ajouter les éléments au certificat
                certificatDiv.appendChild(nomDiv);
                certificatDiv.appendChild(niveauDiv);
                certificatDiv.appendChild(periodeDiv);
                
                // Ajouter au DOM
                document.body.appendChild(certificatDiv);
                
                // Attendre un peu pour que les styles soient appliqués
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Charger html2canvas si pas déjà chargé
                if (typeof html2canvas === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                    script.onload = async () => {
                        await generateCertificate();
                    };
                    document.head.appendChild(script);
                } else {
                    await generateCertificate();
                }
                
                async function generateCertificate() {
                    try {
                        // Générer l'image
                        const canvas = await html2canvas(certificatDiv, { 
                            useCORS: true,
                            allowTaint: true,
                            backgroundColor: null,
                            scale: 2,
                            logging: false
                        });
                        
                        // Créer le lien de téléchargement
                        const link = document.createElement("a");
                        const fileName = `certificat_${apprenantName.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.png`;
                        link.download = fileName;
                        link.href = canvas.toDataURL("image/png", 1.0);
                        
                        // Nettoyer
                        document.body.removeChild(certificatDiv);
                        
                        // Déclencher le téléchargement
                        link.click();
                        
                        // Afficher le succès
                        this.innerHTML = '<i class="fas fa-check me-1"></i>Terminé !';
                        this.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.style.background = '';
                            this.disabled = false;
                        }, 2000);
                        
                    } catch (error) {
                        console.error('Erreur lors de la génération:', error);
                        this.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Erreur';
                        this.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.style.background = '';
                            this.disabled = false;
                        }, 2000);
                        
                        // Nettoyer en cas d'erreur
                        if (document.body.contains(certificatDiv)) {
                            document.body.removeChild(certificatDiv);
                        }
                    }
                }
                
            } catch (error) {
                console.error('Erreur:', error);
                this.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Erreur';
                this.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.background = '';
                    this.disabled = false;
                }, 2000);
            }
        });
    });

    // Gestion des boutons de téléchargement avec état sauvegardé
    const downloadWithStateBtns = document.querySelectorAll('.download-with-state-btn');
    downloadWithStateBtns.forEach(btn => {
        btn.addEventListener('click', async function() {
            const certificatId = this.getAttribute('data-certificat-id');
            const apprenantName = this.getAttribute('data-apprenant-name');
            const originalText = this.innerHTML;
            
            try {
                // Vérifier s'il y a un état sauvegardé
                const key = `certificat_state_${certificatId}`;
                console.log('🔑 Clé utilisée pour récupérer l\'état:', key);
                const savedState = localStorage.getItem(key);
                
                if (!savedState) {
                    alert('⚠️ Aucun état sauvegardé trouvé pour ce certificat.\n\nVeuillez d\'abord utiliser le générateur pour créer et sauvegarder un état personnalisé.');
                    console.log('❌ Aucun état trouvé pour la clé:', key);
                    return;
                }
                
                // Afficher l'état de chargement
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Téléchargement...';
                this.disabled = true;
                
                // Récupérer l'état sauvegardé
                const state = JSON.parse(savedState);
                console.log('État sauvegardé trouvé pour téléchargement:', state);
                
                // Log détaillé de TOUT l'état sauvegardé pour déboguer
                console.log('🎨 ÉTAT COMPLET sauvegardé (téléchargement):');
                console.log('  Nom:', {
                    texte: state.nom.text,
                    couleur: state.nom.color,
                    position: { top: state.nom.top, left: state.nom.left },
                    taille: { fontSize: state.nom.fontSize, width: state.nom.width, height: state.nom.height },
                    style: { fontWeight: state.nom.fontWeight, fontStyle: state.nom.fontStyle, textDecoration: state.nom.textDecoration, textTransform: state.nom.textTransform }
                });
                console.log('  Niveau:', {
                    texte: state.niveau.text,
                    couleur: state.niveau.color,
                    position: { top: state.niveau.top, left: state.niveau.left },
                    taille: { fontSize: state.niveau.fontSize, width: state.niveau.width, height: state.niveau.height },
                    style: { fontWeight: state.niveau.fontWeight, fontStyle: state.niveau.fontStyle, textDecoration: state.niveau.textDecoration, textTransform: state.niveau.textTransform }
                });
                console.log('  Période:', {
                    texte: state.periode.text,
                    couleur: state.periode.color,
                    position: { top: state.periode.top, right: state.periode.right },
                    taille: { fontSize: state.periode.fontSize, width: state.periode.width, height: state.periode.height },
                    style: { fontWeight: state.periode.fontWeight, fontStyle: state.periode.fontStyle, textDecoration: state.periode.textDecoration, textTransform: state.periode.textTransform }
                });
                console.log('  Arrière-plan:', state.background);
                console.log('  Image personnalisée:', state.customBackgroundData ? 'Oui' : 'Non');
                
                // Vérifier que tous les éléments nécessaires sont présents
                if (!state.nom || !state.niveau || !state.periode) {
                    alert('⚠️ L\'état sauvegardé est incomplet.\n\nVeuillez retourner dans le générateur et sauvegarder à nouveau l\'état.');
                    this.innerHTML = originalText;
                    this.disabled = false;
                    return;
                }
                
                // Vérifier si l'état contient des positions personnalisées
                const standardPositions = {
                    nom: { top: '230px', left: '50%' },
                    niveau: { top: '410px', left: '160px' },
                    periode: { top: '410px', right: '160px' }
                };
                
                console.log('🔍 Vérification des positions personnalisées:');
                console.log('  Nom - Sauvegardé:', state.nom.top, state.nom.left, '| Standard:', standardPositions.nom.top, standardPositions.nom.left);
                console.log('  Niveau - Sauvegardé:', state.niveau.top, state.niveau.left, '| Standard:', standardPositions.niveau.top, standardPositions.niveau.left);
                console.log('  Période - Sauvegardé:', state.periode.top, state.periode.right, '| Standard:', standardPositions.periode.top, standardPositions.periode.right);
                
                // Vérifier si les positions sont identiques aux valeurs standard
                const isStandardPosition = (saved, standard) => {
                    return (saved.top === standard.top && saved.left === standard.left) || 
                           (saved.top === standard.top && saved.right === standard.right);
                };
                
                const nomIsStandard = isStandardPosition(state.nom, standardPositions.nom);
                const niveauIsStandard = isStandardPosition(state.niveau, standardPositions.niveau);
                const periodeIsStandard = isStandardPosition(state.periode, standardPositions.periode);
                
                if (nomIsStandard && niveauIsStandard && periodeIsStandard) {
                    console.log('⚠️ ATTENTION: L\'état sauvegardé contient les positions standard !');
                    console.log('   Cela signifie que soit:');
                    console.log('   1. L\'état n\'a pas été sauvegardé après modification');
                    console.log('   2. Les modifications n\'ont pas été détectées');
                    console.log('   3. L\'état a été écrasé par les valeurs standard');
                    
                    const confirmContinue = confirm('⚠️ L\'état sauvegardé contient les positions standard.\n\nVoulez-vous continuer quand même ou retourner dans le générateur pour sauvegarder un état personnalisé ?');
                    
                    if (!confirmContinue) {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        return;
                    }
                }
                
                // Créer un certificat temporaire avec l'état sauvegardé EXACT
                const certificatDiv = document.createElement('div');
                certificatDiv.id = 'temp-certificat-download';
                
                // Utiliser EXCLUSIVEMENT l'état sauvegardé pour l'arrière-plan
                let backgroundStyle = '';
                if (state.customBackgroundData) {
                    backgroundStyle = `background: url("${state.customBackgroundData}") no-repeat center/cover`;
                } else if (state.background) {
                    backgroundStyle = `background: ${state.background} no-repeat center/cover`;
                } else {
                    backgroundStyle = `background: url("{{ asset("MODELE CERTIFICAT DE FORMATION.jpg") }}") no-repeat center/cover`;
                }
                
                certificatDiv.style.cssText = `
                    position: absolute;
                    left: -9999px;
                    width: 1086px;
                    height: 768px;
                    ${backgroundStyle};
                    overflow: hidden;
                `;
                
                // Créer les éléments de texte avec l'état sauvegardé EXACT (même structure que le générateur)
                const nomDiv = document.createElement('div');
                nomDiv.className = 'field';
                nomDiv.id = 'nom';
                
                // Nettoyer le texte en enlevant les espaces en trop
                const cleanText = (text) => text.replace(/\s+/g, ' ').trim();
                
                // Convertir les positions en pixels si elles sont en pourcentages
                const convertPosition = (value, containerSize, isRight = false) => {
                    console.log('🔄 Conversion position:', value, 'containerSize:', containerSize, 'isRight:', isRight);
                    
                    if (typeof value === 'string' && value.includes('%')) {
                        const percentage = parseFloat(value) / 100;
                        let result;
                        
                        if (isRight) {
                            // Pour la période (right), calculer depuis la droite
                            result = containerSize * (1 - percentage);
                            console.log('  % -> px (right):', value, '=', percentage, '*', containerSize, '*(1-', percentage, ') =', result + 'px');
                        } else {
                            // Pour left/top, calculer depuis la gauche/haut
                            result = containerSize * percentage;
                            console.log('  % -> px (left/top):', value, '=', percentage, '*', containerSize, '=', result + 'px');
                        }
                        
                        return result + 'px';
                    } else if (typeof value === 'string' && value.includes('px')) {
                        console.log('  Déjà en px:', value);
                        return value;
                    } else {
                        console.log('  Valeur brute:', value);
                        return value;
                    }
                };
                
                const containerWidth = 1086;
                const containerHeight = 768;
                
                console.log('📐 Dimensions du conteneur:', containerWidth, 'x', containerHeight);
                console.log('📍 Positions brutes de l\'état:');
                console.log('  Nom - top:', state.nom.top, 'left:', state.nom.left);
                console.log('  Niveau - top:', state.niveau.top, 'left:', state.niveau.left);
                console.log('  Période - top:', state.periode.top, 'right:', state.periode.right);
                
                const nomTop = convertPosition(state.nom.top, containerHeight);
                const nomLeft = convertPosition(state.nom.left, containerWidth);
                const niveauTop = convertPosition(state.niveau.top, containerHeight);
                const niveauLeft = convertPosition(state.niveau.left, containerWidth);
                const periodeTop = convertPosition(state.periode.top, containerHeight);
                const periodeRight = convertPosition(state.periode.right, containerWidth, true);
                
                // Logs détaillés pour la période
                console.log('🎯 DÉTAILS PÉRIODE:');
                console.log('  Position brute:', state.periode.right);
                console.log('  Type:', typeof state.periode.right);
                console.log('  Contient %:', state.periode.right && state.periode.right.includes('%'));
                console.log('  Conversion:', periodeRight);
                console.log('  Calcul inversé (pour vérification):', containerWidth - parseFloat(periodeRight));
                
                console.log('🔧 Positions converties en pixels:');
                console.log('  Nom - top:', nomTop, 'left:', nomLeft);
                console.log('  Niveau - top:', niveauTop, 'left:', niveauLeft);
                console.log('  Période - top:', periodeTop, 'right:', periodeRight);
                
                // Créer un style CSS inline complet avec !important pour forcer les couleurs
                const nomStyle = `
                    position: absolute !important;
                    top: ${nomTop} !important;
                    left: ${nomLeft} !important;
                    transform: ${state.nom.left === '50%' ? 'translateX(-50%)' : 'none'} !important;
                    font-size: ${state.nom.fontSize} !important;
                    color: ${state.nom.color} !important;
                    text-align: center !important;
                    min-width: 200px !important;
                    font-weight: ${state.nom.fontWeight} !important;
                    font-style: ${state.nom.fontStyle} !important;
                    text-decoration: ${state.nom.textDecoration} !important;
                    text-transform: ${state.nom.textTransform} !important;
                `;
                
                nomDiv.setAttribute('style', nomStyle);
                nomDiv.setAttribute('data-color', state.nom.color);
                
                // Appliquer aussi via style object pour compatibilité
                nomDiv.style.position = 'absolute';
                nomDiv.style.top = nomTop;
                nomDiv.style.left = nomLeft;
                nomDiv.style.transform = state.nom.left === '50%' ? 'translateX(-50%)' : 'none';
                nomDiv.style.fontSize = state.nom.fontSize;
                nomDiv.style.color = state.nom.color; // Forcer la couleur
                nomDiv.style.textAlign = 'center';
                nomDiv.style.minWidth = '200px';
                nomDiv.style.fontWeight = state.nom.fontWeight;
                nomDiv.style.fontStyle = state.nom.fontStyle;
                nomDiv.style.textDecoration = state.nom.textDecoration;
                nomDiv.style.textTransform = state.nom.textTransform;
                
                nomDiv.textContent = cleanText(state.nom.text);
                
                // Log pour vérifier que TOUT est bien appliqué
                console.log('✅ Nom créé avec tous les styles:');
                console.log('  - Couleur:', nomDiv.style.color, '(état:', state.nom.color, ')');
                console.log('  - Position:', nomDiv.style.top, nomDiv.style.left);
                console.log('  - Taille:', nomDiv.style.fontSize, nomDiv.style.width, nomDiv.style.height);
                console.log('  - Style:', nomDiv.style.fontWeight, nomDiv.style.fontStyle, nomDiv.style.textDecoration, nomDiv.style.textTransform);
                console.log('  - Texte:', nomDiv.textContent);
                
                const niveauDiv = document.createElement('div');
                niveauDiv.className = 'field';
                niveauDiv.id = 'niveau';
                
                // Créer un style CSS inline complet avec !important pour forcer les couleurs
                const niveauStyle = `
                    position: absolute !important;
                    top: ${niveauTop} !important;
                    left: ${niveauLeft} !important;
                    font-size: ${state.niveau.fontSize} !important;
                    text-align: left !important;
                    color: ${state.niveau.color} !important;
                    min-width: 150px !important;
                    font-weight: ${state.niveau.fontWeight} !important;
                    font-style: ${state.niveau.fontStyle} !important;
                    text-decoration: ${state.niveau.textDecoration} !important;
                    text-transform: ${state.niveau.textTransform} !important;
                `;
                
                niveauDiv.setAttribute('style', niveauStyle);
                niveauDiv.setAttribute('data-color', state.niveau.color);
                
                // Appliquer aussi via style object pour compatibilité
                niveauDiv.style.position = 'absolute';
                niveauDiv.style.top = niveauTop;
                niveauDiv.style.left = niveauLeft;
                niveauDiv.style.fontSize = state.niveau.fontSize;
                niveauDiv.style.textAlign = 'left';
                niveauDiv.style.color = state.niveau.color; // Forcer la couleur
                niveauDiv.style.minWidth = '150px';
                niveauDiv.style.fontWeight = state.niveau.fontWeight;
                niveauDiv.style.fontStyle = state.niveau.fontStyle;
                niveauDiv.style.textDecoration = state.niveau.textDecoration;
                niveauDiv.style.textTransform = state.niveau.textTransform;
                
                niveauDiv.textContent = cleanText(state.niveau.text);
                
                // Log pour vérifier que TOUT est bien appliqué
                console.log('✅ Niveau créé avec tous les styles:');
                console.log('  - Couleur:', niveauDiv.style.color, '(état:', state.niveau.color, ')');
                console.log('  - Position:', niveauDiv.style.top, niveauDiv.style.left);
                console.log('  - Taille:', niveauDiv.style.fontSize, niveauDiv.style.width, niveauDiv.style.height);
                console.log('  - Style:', niveauDiv.style.fontWeight, niveauDiv.style.fontStyle, niveauDiv.style.textDecoration, niveauDiv.style.textTransform);
                console.log('  - Texte:', niveauDiv.textContent);
                
                const periodeDiv = document.createElement('div');
                periodeDiv.className = 'field';
                periodeDiv.id = 'periode';
                
                // Vérification visuelle de la position de la période
                const periodeLeftFromRight = parseFloat(periodeRight);
                const periodeLeftFromLeft = containerWidth - periodeLeftFromRight;
                console.log('🎯 POSITION FINALE PÉRIODE:');
                console.log('  right:', periodeRight, '(distance depuis la droite)');
                console.log('  left calculé:', periodeLeftFromLeft + 'px', '(distance depuis la gauche)');
                console.log('  position relative:', ((periodeLeftFromLeft / containerWidth) * 100).toFixed(2) + '%', 'depuis la gauche');
                
                // Créer un style CSS inline complet avec !important pour forcer les couleurs
                const periodeStyle = `
                    position: absolute !important;
                    top: ${periodeTop} !important;
                    right: ${periodeRight} !important;
                    font-size: ${state.periode.fontSize} !important;
                    text-align: left !important;
                    color: ${state.periode.color} !important;
                    min-width: 150px !important;
                    font-weight: ${state.periode.fontWeight} !important;
                    font-style: ${state.periode.fontStyle} !important;
                    text-decoration: ${state.periode.textDecoration} !important;
                    text-transform: ${state.periode.textTransform} !important;
                `;
                
                periodeDiv.setAttribute('style', periodeStyle);
                periodeDiv.setAttribute('data-color', state.periode.color);
                
                // Appliquer aussi via style object pour compatibilité
                periodeDiv.style.position = 'absolute';
                periodeDiv.style.top = periodeTop;
                periodeDiv.style.right = periodeRight;
                periodeDiv.style.fontSize = state.periode.fontSize;
                periodeDiv.style.textAlign = 'left';
                periodeDiv.style.color = state.periode.color; // Forcer la couleur
                periodeDiv.style.minWidth = '150px';
                periodeDiv.style.fontWeight = state.periode.fontWeight;
                periodeDiv.style.fontStyle = state.periode.fontStyle;
                periodeDiv.style.textDecoration = state.periode.textDecoration;
                periodeDiv.style.textTransform = state.periode.textTransform;
                
                periodeDiv.textContent = cleanText(state.periode.text);
                
                // Log pour vérifier que TOUT est bien appliqué
                console.log('✅ Période créée avec tous les styles:');
                console.log('  - Couleur:', periodeDiv.style.color, '(état:', state.periode.color, ')');
                console.log('  - Position:', periodeDiv.style.top, periodeDiv.style.right);
                console.log('  - Taille:', periodeDiv.style.fontSize, periodeDiv.style.width, periodeDiv.style.height);
                console.log('  - Style:', periodeDiv.style.fontWeight, periodeDiv.style.fontStyle, periodeDiv.style.textDecoration, periodeDiv.style.textTransform);
                console.log('  - Texte:', periodeDiv.textContent);
                
                // Ajouter les éléments au certificat
                certificatDiv.appendChild(nomDiv);
                certificatDiv.appendChild(niveauDiv);
                certificatDiv.appendChild(periodeDiv);
                
                // Ajouter au DOM
                document.body.appendChild(certificatDiv);
                
                // Vérifier que les couleurs sont bien appliquées après l'ajout au DOM
                console.log('🔍 Vérification finale des couleurs après ajout au DOM:');
                console.log('  Nom - couleur appliquée:', nomDiv.style.color, 'computed:', window.getComputedStyle(nomDiv).color);
                console.log('  Niveau - couleur appliquée:', niveauDiv.style.color, 'computed:', window.getComputedStyle(nomDiv).color);
                console.log('  Période - couleur appliquée:', periodeDiv.style.color, 'computed:', window.getComputedStyle(periodeDiv).color);
                
                // Attendre un peu pour que les styles soient appliqués
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Charger html2canvas si pas déjà chargé
                if (typeof html2canvas === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                    script.onload = async () => {
                        await downloadCertificate();
                    };
                    document.head.appendChild(script);
                } else {
                    await downloadCertificate();
                }
                
                async function downloadCertificate() {
                    try {
                        // Même logique que le bouton de téléchargement du générateur
                        // Masquer les bordures et poignées pour une image propre
                        const fields = certificatDiv.querySelectorAll('.field');
                        fields.forEach(field => {
                            field.style.border = 'none';
                            field.style.background = 'none';
                        });
                        
                        // FORCER les couleurs AVANT la génération html2canvas
                        console.log('🎨 FORÇAGE FINAL des couleurs avant html2canvas:');
                        
                        // Forcer la couleur du nom
                        nomDiv.style.setProperty('color', state.nom.color, 'important');
                        nomDiv.style.setProperty('color', state.nom.color, 'important');
                        nomDiv.setAttribute('data-color', state.nom.color);
                        console.log('  Nom - couleur forcée:', nomDiv.style.color, 'data-color:', nomDiv.getAttribute('data-color'));
                        
                        // Forcer la couleur du niveau
                        niveauDiv.style.setProperty('color', state.niveau.color, 'important');
                        niveauDiv.style.setProperty('color', state.niveau.color, 'important');
                        niveauDiv.setAttribute('data-color', state.niveau.color);
                        console.log('  Niveau - couleur forcée:', niveauDiv.style.color, 'data-color:', niveauDiv.getAttribute('data-color'));
                        
                        // Forcer la couleur de la période
                        periodeDiv.style.setProperty('color', state.periode.color, 'important');
                        periodeDiv.style.setProperty('color', state.periode.color, 'important');
                        periodeDiv.setAttribute('data-color', state.periode.color);
                        console.log('  Période - couleur forcée:', periodeDiv.style.color, 'data-color:', periodeDiv.getAttribute('data-color'));
                        
                        // Attendre un peu pour que les styles soient appliqués
                        await new Promise(resolve => setTimeout(resolve, 200));
                        
                        // Générer l'image avec les mêmes paramètres que le générateur
                        const canvas = await html2canvas(certificatDiv, { 
                            useCORS: true,
                            allowTaint: true,
                            backgroundColor: null,
                            scale: 2, // Higher quality
                            logging: true, // Activer le logging pour déboguer
                            onclone: function(clonedDoc) {
                                // Callback appelé sur le clone avant le rendu
                                console.log('🔍 html2canvas onclone - Vérification des couleurs:');
                                const clonedNom = clonedDoc.getElementById('nom');
                                const clonedNiveau = clonedDoc.getElementById('niveau');
                                const clonedPeriode = clonedDoc.getElementById('periode');
                                
                                if (clonedNom) {
                                    console.log('  Clone Nom - couleur:', clonedNom.style.color, 'computed:', window.getComputedStyle(clonedNom).color);
                                    // Forcer encore une fois sur le clone
                                    clonedNom.style.setProperty('color', state.nom.color, 'important');
                                }
                                if (clonedNiveau) {
                                    console.log('  Clone Niveau - couleur:', clonedNiveau.style.color, 'computed:', window.getComputedStyle(clonedNiveau).color);
                                    clonedNiveau.style.setProperty('color', state.niveau.color, 'important');
                                }
                                if (clonedPeriode) {
                                    console.log('  Clone Période - couleur:', clonedPeriode.style.color, 'computed:', window.getComputedStyle(clonedPeriode).color);
                                    clonedPeriode.style.setProperty('color', state.periode.color, 'important');
                                }
                            }
                        });
                        
                        // Créer le lien de téléchargement avec le même nom de fichier que le générateur
                        const link = document.createElement("a");
                        const fileName = `certificat_${apprenantName.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.png`;
                        link.download = fileName;
                        link.href = canvas.toDataURL("image/png", 1.0);
                        
                        // Nettoyer
                        document.body.removeChild(certificatDiv);
                        
                        // Déclencher le téléchargement
                        link.click();
                        
                        // Afficher le succès avec le même message que le générateur
                        btn.innerHTML = '<i class="fas fa-check me-1"></i>Certificat téléchargé !';
                        btn.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
                        
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.style.background = '';
                            btn.disabled = false;
                        }, 2000);
                        
                    } catch (error) {
                        console.error('Erreur lors de la génération:', error);
                        btn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Erreur de génération';
                        btn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                        
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.style.background = '';
                            btn.disabled = false;
                        }, 2000);
                        
                        // Nettoyer en cas d'erreur
                        if (document.body.contains(certificatDiv)) {
                            document.body.removeChild(certificatDiv);
                        }
                    }
                }
                
            } catch (error) {
                console.error('Erreur:', error);
                this.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Erreur';
                this.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.background = '';
                    this.disabled = false;
                }, 2000);
            }
        });
    });


});


</script>

@endsection 
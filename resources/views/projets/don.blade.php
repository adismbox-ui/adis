@extends('layouts.app')

@section('title', 'Faire un don - ADIS')

@section('content')
<div class="container-fluid main-container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 p-0">
            <div class="sidebar">
                <div class="p-4">
                    <h3 class="text-center mb-4" style="color: var(--accent-green);">
                        <i class="fas fa-project-diagram me-2"></i>Navigation
                    </h3>
                    @include('layouts.projets_sidebar')
                </div>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="col-md-9">
            <div class="content-area card-3d">
                <h1 class="main-title mb-3"><i class="fas fa-hand-holding-heart me-3"></i>FAIRE UN DON</h1>
                <div class="description mb-4">
                    <p class="lead mb-0">
                        <strong>Votre générosité fait la différence !</strong><br>
                        Votre générosité est la clé qui permet de transformer nos projets en réalités concrètes. 
                        Que ce soit un don ponctuel ou régulier, chaque geste compte et soutient directement nos actions communautaires. 
                        Merci de votre confiance et de votre engagement à nos côtés.
                    </p>
                </div>

                <!-- Formulaire de don -->
                <div class="mb-4">
                    <h3 class="mb-3 text-success"><i class="fas fa-gift me-2"></i>Formulaire de don</h3>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('projets.don.store') }}" class="don-form">
                        @csrf
                        
                        <!-- Informations personnelles -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nom_donateur" class="form-label">Nom complet *</label>
                                <input type="text" name="nom_donateur" id="nom_donateur" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email_donateur" class="form-label">Adresse e-mail *</label>
                                <input type="email" name="email_donateur" id="email_donateur" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" name="telephone" id="telephone" class="form-control">
                        </div>

                        <!-- Montant du don -->
                        <div class="mb-3">
                            <label class="form-label">Montant du don * :</label>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="montant" id="montant1000" value="1000" checked>
                                        <label class="form-check-label" for="montant1000">1 000 F CFA</label>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="montant" id="montant2000" value="2000">
                                        <label class="form-check-label" for="montant2000">2 000 F CFA</label>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="montant" id="montant5000" value="5000">
                                        <label class="form-check-label" for="montant5000">5 000 F CFA</label>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="montant" id="montant10000" value="10000">
                                        <label class="form-check-label" for="montant10000">10 000 F CFA</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="montant" id="montantautre" value="autre">
                                <label class="form-check-label" for="montantautre">Autre montant :</label>
                                <input type="number" name="montant_autre" id="montant_autre" class="form-control mt-2" placeholder="Montant en F CFA" style="display: none;">
                            </div>
                        </div>

                        <!-- Type de don -->
                        <div class="mb-3">
                            <label class="form-label">Type de don * :</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type_don" id="ponctuel" value="ponctuel" checked>
                                        <label class="form-check-label" for="ponctuel">Ponctuel</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type_don" id="mensuel" value="mensuel">
                                        <label class="form-check-label" for="mensuel">Mensuel</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Projet -->
                        <div class="mb-3">
                            <label for="projet_id" class="form-label">Destination du don * :</label>
                            <select name="projet_id" id="projet_id" class="form-select" required>
                                <option value="">-- Sélectionnez un projet --</option>
                                @if(isset($projetsEnCours) && $projetsEnCours->count())
                                    <optgroup label="📋 Projets en cours">
                                        @foreach($projetsEnCours as $projet)
                                            <option value="{{ $projet->id }}">{{ $projet->intitule }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if(isset($projetsAFinancer) && $projetsAFinancer->count())
                                    <optgroup label="💰 Projets à financer">
                                        @foreach($projetsAFinancer as $projet)
                                            <option value="{{ $projet->id }}">{{ $projet->intitule }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                <option value="fonds_general" selected>🏦 Fonds général (pour tous nos projets)</option>
                            </select>
                        </div>

                        <!-- Mode de paiement -->
                        <div class="mb-3">
                            <label class="form-label">Mode de paiement * :</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mode_paiement" id="carte" value="carte" checked>
                                        <label class="form-check-label" for="carte">Carte bancaire</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mode_paiement" id="virement" value="virement">
                                        <label class="form-check-label" for="virement">Virement bancaire</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mode_paiement" id="mobile" value="mobile">
                                        <label class="form-check-label" for="mobile">Mobile money</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="mb-3">
                            <label for="devis_email" class="form-label">Email pour le devis (optionnel)</label>
                            <input type="email" name="devis_email" id="devis_email" class="form-control" placeholder="Par défaut: même que l'adresse e-mail">
                            <small class="form-text text-muted">Le reçu est envoyé à l'adresse e-mail principale. Si vous renseignez ce champ, le devis sera envoyé à cette adresse.</small>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message (facultatif)</label>
                            <textarea name="message" id="message" class="form-control" rows="3" placeholder="Si vous souhaitez dire quelque chose de particulier..."></textarea>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="conditions" id="conditions" required>
                                <label class="form-check-label" for="conditions">
                                    Je confirme avoir lu et accepté les conditions générales et la politique de confidentialité. *
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer le don
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Informations sur les dons -->
                <div class="mb-4">
                    <h3 class="mb-3 text-info"><i class="fas fa-info-circle me-2"></i>Informations importantes</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent">Tous les dons sont utilisés exclusivement pour financer nos projets communautaires.</li>
                        <li class="list-group-item bg-transparent">Vous recevrez un reçu électronique pour vos déclarations fiscales.</li>
                        <li class="list-group-item bg-transparent">Si vous demandez un devis, nous vous l'enverrons par email dans les 24h suivant votre demande.</li>
                        <li class="list-group-item bg-transparent">Vos informations personnelles sont protégées et ne seront jamais partagées avec des tiers.</li>
                        <li class="list-group-item bg-transparent">Vous pouvez annuler votre don mensuel à tout moment en nous contactant.</li>
                        <li class="list-group-item bg-transparent">Nous publions régulièrement des rapports sur l'utilisation des fonds collectés.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
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
.card-3d { transform-style: preserve-3d; transition: transform 0.6s ease; }
.card-3d:hover { transform: rotateY(10deg) rotateX(5deg) scale(1.02); }
.bg-glass { background: rgba(255,255,255,0.08)!important; backdrop-filter: blur(8px); }
.don-form { background: rgba(255, 255, 255, 0.02); border-radius: 15px; padding: 30px; border: 1px solid rgba(52, 211, 153, 0.2); margin-bottom: 30px; }
.form-control, .form-select { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(52, 211, 153, 0.3); color: #fff; }
.form-control:focus, .form-select:focus { background: rgba(255, 255, 255, 0.15); border-color: var(--accent-green); color: #fff; box-shadow: 0 0 0 0.2rem rgba(52, 211, 153, 0.25); }
.form-control::placeholder { color: rgba(255, 255, 255, 0.6); }
.form-label, .form-check-label { color: #fff; font-weight: 500; }
.form-check-input:checked { background-color: var(--accent-green); border-color: var(--accent-green); box-shadow: 0 0 15px rgba(52, 211, 153, 0.5); }
.btn-success { background: linear-gradient(135deg, var(--accent-green), var(--light-green)); border: none; font-weight: 700; letter-spacing: 1px; }
.btn-success:hover { background: linear-gradient(135deg, var(--light-green), var(--accent-green)); }
.list-group-item { background: transparent; color: #fff; border: 1px solid rgba(52, 211, 153, 0.2); }
@media (max-width: 768px) { .main-title { font-size: 2rem; } .content-area { margin: 10px; padding: 20px; } }
</style>
@endpush

@push('scripts')
<script>
// Gestion du montant personnalisé
document.getElementById('montantautre').addEventListener('change', function() {
    const montantAutre = document.getElementById('montant_autre');
    if (this.checked) {
        montantAutre.style.display = 'block';
        montantAutre.required = true;
    } else {
        montantAutre.style.display = 'none';
        montantAutre.required = false;
    }
});

// Masquer le champ montant_autre par défaut
document.getElementById('montant_autre').style.display = 'none';

// Auto-pré-remplir le champ devis_email avec l'email du donateur
const emailDonateurInput = document.getElementById('email_donateur');
const devisEmailInput = document.getElementById('devis_email');

emailDonateurInput.addEventListener('blur', function() {
    if (!devisEmailInput.value) {
        devisEmailInput.value = emailDonateurInput.value;
    }
});
</script>
@endpush
@endsection
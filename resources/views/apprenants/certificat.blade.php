@extends('layouts.app')

@section('title', 'Mon Certificat - ' . $apprenant->utilisateur->prenom . ' ' . $apprenant->utilisateur->nom)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        Mon Certificat de Formation
                    </h3>
                    <p class="mb-0 text-light">
                        {{ $apprenant->utilisateur->prenom }} {{ $apprenant->utilisateur->nom }} - {{ $niveauApprenant ? $niveauApprenant->nom : 'Formation complétée' }}
                    </p>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Zone du certificat -->
                            <div class="certificat-container text-center mb-4">
                                <div id="certificat" class="certificat-apprenant">
                                    <div id="nom" class="field field-nom">
                                        {{ $apprenant->utilisateur->prenom }} {{ $apprenant->utilisateur->nom }}
                                    </div>
                                    <div id="niveau" class="field field-niveau">
                                        {{ $niveauApprenant ? $niveauApprenant->nom : ($module ? $module->titre : 'Formation complétée') }}
                                    </div>
                                    <div id="periode" class="field field-periode">
                                        {{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informations du certificat -->
                            <div class="certificat-info bg-light p-3 rounded">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Détails du certificat
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nom complet :</strong> {{ $apprenant->utilisateur->prenom }} {{ $apprenant->utilisateur->nom }}</p>
                                        <p><strong>Niveau :</strong> {{ $niveauApprenant ? $niveauApprenant->nom : 'Non défini' }}</p>
                                        <p><strong>Date d'obtention :</strong> {{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Module :</strong> {{ $module ? $module->titre : 'Formation générale' }}</p>
                                        <p><strong>Statut :</strong> <span class="badge bg-success">Validé</span></p>
                                        <p><strong>Numéro :</strong> #{{ $certificat->id }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Actions -->
                            <div class="action-panel">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-download me-2"></i>
                                            Actions
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-3">
                                            <!-- Sauvegarder l'état -->
                                            <button id="btnSaveState" type="button" class="btn btn-outline-primary">
                                                <i class="fas fa-save me-2"></i>
                                                Sauvegarder mon certificat
                                            </button>
                                            
                                            <!-- Télécharger -->
                                            <button id="btnDownload" type="button" class="btn btn-success">
                                                <i class="fas fa-download me-2"></i>
                                                Télécharger le certificat
                                            </button>
                                            
                                            <!-- Tester l'état -->
                                            <button id="btnTestState" type="button" class="btn btn-warning">
                                                <i class="fas fa-eye me-2"></i>
                                                Vérifier la sauvegarde
                                            </button>
                                        </div>
                                        
                                        <hr class="my-3">
                                        
                                        <div class="text-center">
                                            <small class="text-muted">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Votre certificat est sécurisé et personnel
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Instructions -->
                                <div class="card mt-3 border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-question-circle me-2"></i>
                                            Comment utiliser
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <ol class="small">
                                            <li><strong>Sauvegardez</strong> votre certificat pour conserver vos préférences</li>
                                            <li><strong>Téléchargez</strong> le certificat en image pour l'imprimer</li>
                                            <li><strong>Vérifiez</strong> que votre sauvegarde fonctionne</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS -->
<style>
.certificat-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.certificat-apprenant {
    position: relative;
    width: 100%;
    max-width: 800px;
    height: 500px;
    margin: 0 auto;
    background: url("{{ asset('MODELE CERTIFICAT DE FORMATION.jpg') }}") no-repeat center/cover;
    background-size: contain;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-radius: 10px;
    overflow: hidden;
}

.field {
    position: absolute;
    font-weight: bold;
    color: #2d5f4f;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
    user-select: none;
    border: 2px dashed #22c55e;
    padding: 8px;
    background: rgba(34, 197, 94, 0.1);
    border-radius: 5px;
    min-width: 120px;
    text-align: center;
}

.field-nom {
    top: 30%;
    left: 50%;
    transform: translateX(-50%);
    font-size: 32px;
    min-width: 200px;
}

.field-niveau {
    top: 55%;
    left: 20%;
    font-size: 24px;
    text-align: left;
    min-width: 150px;
}

.field-periode {
    top: 55%;
    right: 20%;
    font-size: 24px;
    text-align: left;
    min-width: 150px;
}

.action-panel .btn {
    font-weight: 500;
    padding: 12px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.action-panel .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.certificat-info {
    border-left: 4px solid #007bff;
}

@media (max-width: 768px) {
    .certificat-apprenant {
        height: 400px;
    }
    
    .field-nom {
        font-size: 24px;
    }
    
    .field-niveau,
    .field-periode {
        font-size: 18px;
    }
}
</style>

<!-- Scripts JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const certificat = document.getElementById('certificat');
    const nom = document.getElementById('nom');
    const niveau = document.getElementById('niveau');
    const periode = document.getElementById('periode');
    
    // Save state functionality
    document.getElementById("btnSaveState").addEventListener("click", () => {
        const button = document.getElementById("btnSaveState");
        const originalText = button.textContent;
        
        try {
            // Get current background image
            const currentBackground = certificat.style.backgroundImage;
            
            // Collect current state
            const state = {
                nom: {
                    text: nom.textContent,
                    fontSize: window.getComputedStyle(nom).fontSize,
                    left: '50%',
                    top: '30%',
                    width: window.getComputedStyle(nom).width,
                    height: window.getComputedStyle(nom).height,
                    color: window.getComputedStyle(nom).color,
                    fontWeight: window.getComputedStyle(nom).fontWeight,
                    fontStyle: window.getComputedStyle(nom).fontStyle,
                    textDecoration: window.getComputedStyle(nom).textDecoration,
                    textTransform: window.getComputedStyle(nom).textTransform
                },
                niveau: {
                    text: niveau.textContent,
                    fontSize: window.getComputedStyle(niveau).fontSize,
                    left: '20%',
                    top: '55%',
                    width: window.getComputedStyle(niveau).width,
                    height: window.getComputedStyle(niveau).height,
                    color: window.getComputedStyle(niveau).color,
                    fontWeight: window.getComputedStyle(niveau).fontWeight,
                    fontStyle: window.getComputedStyle(niveau).fontStyle,
                    textDecoration: window.getComputedStyle(niveau).textDecoration,
                    textTransform: window.getComputedStyle(niveau).textTransform
                },
                periode: {
                    text: periode.textContent,
                    fontSize: window.getComputedStyle(periode).fontSize,
                    right: '20%',
                    top: '55%',
                    width: window.getComputedStyle(periode).width,
                    height: window.getComputedStyle(periode).height,
                    color: window.getComputedStyle(periode).color,
                    fontWeight: window.getComputedStyle(periode).fontWeight,
                    fontStyle: window.getComputedStyle(periode).fontStyle,
                    textDecoration: window.getComputedStyle(periode).textDecoration,
                    textTransform: window.getComputedStyle(periode).textTransform
                },
                background: currentBackground || 'url("{{ asset("MODELE CERTIFICAT DE FORMATION.jpg") }}")',
                customBackgroundData: null,
                timestamp: new Date().toISOString(),
                certificatId: {{ $certificat->id }},
                apprenantId: {{ $apprenant->id }}
            };
            
            // Save to localStorage
            const key = `certificat_apprenant_{{ $certificat->id }}`;
            localStorage.setItem(key, JSON.stringify(state));
            
            // Verify save
            const saved = localStorage.getItem(key);
            if (saved) {
                console.log('✅ Certificat sauvegardé avec succès');
                button.textContent = "✅ Sauvegardé !";
                button.className = "btn btn-success";
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.className = "btn btn-outline-primary";
                }, 2000);
            } else {
                throw new Error('Échec de la sauvegarde');
            }
            
        } catch (error) {
            console.error('Erreur lors de la sauvegarde:', error);
            button.textContent = "❌ Erreur";
            button.className = "btn btn-danger";
            
            setTimeout(() => {
                button.textContent = originalText;
                button.className = "btn btn-outline-primary";
            }, 2000);
        }
    });

    // Test state functionality
    document.getElementById("btnTestState").addEventListener("click", () => {
        const button = document.getElementById("btnTestState");
        const originalText = button.textContent;
        
        try {
            const key = `certificat_apprenant_{{ $certificat->id }}`;
            const savedState = localStorage.getItem(key);
            
            if (!savedState) {
                button.textContent = "❌ Aucune sauvegarde";
                button.className = "btn btn-danger";
                setTimeout(() => {
                    button.textContent = originalText;
                    button.className = "btn btn-warning";
                }, 2000);
                return;
            }
            
            const state = JSON.parse(savedState);
            console.log('🧪 État sauvegardé trouvé:', state);
            
            button.textContent = "✅ Sauvegarde OK";
            button.className = "btn btn-success";
            
            setTimeout(() => {
                button.textContent = originalText;
                button.className = "btn btn-warning";
            }, 2000);
            
        } catch (error) {
            console.error('Erreur lors du test:', error);
            button.textContent = "❌ Erreur de test";
            button.className = "btn btn-danger";
            
            setTimeout(() => {
                button.textContent = originalText;
                button.className = "btn btn-warning";
            }, 2000);
        }
    });

    // Download functionality
    document.getElementById("btnDownload").addEventListener("click", async () => {
        const button = document.getElementById("btnDownload");
        const originalText = button.textContent;
        
        try {
            button.textContent = '<i class="fas fa-spinner fa-spin me-2"></i>Téléchargement...';
            button.disabled = true;
            
            // Masquer les bordures pour une image propre
            const fields = certificat.querySelectorAll('.field');
            fields.forEach(field => {
                field.style.border = 'none';
                field.style.background = 'none';
            });
            
            // Générer l'image
            const canvas = await html2canvas(certificat, { 
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                scale: 2,
                logging: false
            });
            
            // Créer le lien de téléchargement
            const link = document.createElement("a");
            const fileName = `certificat_{{ $apprenant->utilisateur->prenom }}_{{ $apprenant->utilisateur->nom }}_{{ \Carbon\Carbon::parse($certificat->date_obtention)->format('Y-m-d') }}.png`;
            link.download = fileName;
            link.href = canvas.toDataURL("image/png", 1.0);
            
            // Déclencher le téléchargement
            link.click();
            
            // Restaurer les bordures
            fields.forEach(field => {
                field.style.border = '2px dashed #22c55e';
                field.style.background = 'rgba(34, 197, 94, 0.1)';
            });
            
            // Afficher le succès
            button.innerHTML = '<i class="fas fa-check me-2"></i>Téléchargé !';
            button.className = 'btn btn-success';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.className = 'btn btn-success';
                button.disabled = false;
            }, 2000);
            
        } catch (error) {
            console.error('Erreur lors du téléchargement:', error);
            button.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Erreur';
            button.className = 'btn btn-danger';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.className = 'btn btn-success';
                button.disabled = false;
            }, 2000);
        }
    });
});
</script>
@endsection 
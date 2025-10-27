<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>Génération de Certificat avec État Sauvegardé - ADIS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    gap: 10px;
            background: linear-gradient(135deg, #1e3a8a 0%, #16a34a 50%, #22c55e 100%);
            min-height: 100vh;
            margin: 0;
        }
        
  .container {
            background: rgba(255, 255, 255, 0.95);
    padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    backdrop-filter: blur(10px);
    max-width: 800px;
    width: 100%;
  }
  
  .header {
            text-align: center;
    margin-bottom: 30px;
  }
  
  .header h1 {
    color: #2d5f4f;
    margin-bottom: 10px;
  }
  
  .certificate-info {
    background: rgba(34, 197, 94, 0.1);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #22c55e;
  }
  
  .certificate-info h3 {
    margin: 0 0 15px 0;
    color: #2d5f4f;
  }
  
  .certificate-info p {
    margin: 8px 0;
            color: #374151;
  }
  
  .state-info {
    background: rgba(59, 130, 246, 0.1);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #3b82f6;
  }
  
  .state-info h3 {
    margin: 0 0 15px 0;
    color: #1e40af;
  }
  
  .state-info p {
    margin: 8px 0;
    color: #374151;
  }
  
  .actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin: 30px 0;
  }
  
  .btn {
    padding: 15px 25px;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
  }
  
  .btn-success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
  }
  
  .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
  }
  
  .btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }
  
  .btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
  }
  
  .btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
  }
  
  .btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
  }
  
  .no-state {
    background: rgba(239, 68, 68, 0.1);
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
    border-left: 4px solid #ef4444;
    text-align: center;
  }
  
  .no-state h3 {
    color: #dc2626;
    margin-bottom: 15px;
  }
  
  .no-state p {
    color: #374151;
    margin-bottom: 20px;
  }
  
  .back-link {
    text-align: center;
    margin-top: 30px;
  }
  
  .back-link a {
    color: #22c55e;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
  }
  
  .back-link a:hover {
    text-decoration: underline;
  }
  
  @media (max-width: 768px) {
    .container {
      padding: 20px;
      margin: 10px;
    }
    
    .actions {
      flex-direction: column;
      align-items: center;
    }
    
    .btn {
      width: 100%;
      max-width: 300px;
      justify-content: center;
    }
  }
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <h1>🎓 Génération de Certificat avec État Sauvegardé</h1>
    <p>Générez votre certificat en utilisant un état précédemment sauvegardé</p>
  </div>

  <div class="certificate-info">
    <h3>📋 Informations du Certificat</h3>
    <p><strong>Apprenant:</strong> {{ $apprenant->prenom }} {{ $apprenant->nom }}</p>
    <p><strong>Module:</strong> {{ $module ? $module->titre : 'Formation complétée' }}</p>
    <p><strong>Niveau de l'apprenant:</strong> {{ $niveauApprenant ? $niveauApprenant->nom : 'Non défini' }}</p>
    <p><strong>Date d'obtention:</strong> {{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}</p>
    <p><strong>Numéro de certificat:</strong> {{ str_pad($certificat->id, 6, '0', STR_PAD_LEFT) }}</p>
  </div>

  <div class="state-info">
    <h3>💾 État Sauvegardé</h3>
    <p>Cette page vous permet de générer un certificat en utilisant un état précédemment sauvegardé dans votre navigateur.</p>
    <p>Si vous avez sauvegardé un état personnalisé, il sera automatiquement restauré et utilisé pour la génération.</p>
  </div>

  <div class="actions">
    <a href="{{ route('admin.certificats.generator', $certificat) }}" class="btn btn-primary">
      ✏️ Éditer le Certificat
    </a>
    
    <a href="{{ route('admin.certificats.image-model', $certificat) }}" class="btn btn-success">
      🖼️ Générer avec Modèle
    </a>
    
    <a href="{{ route('admin.certificats.image', $certificat) }}" class="btn btn-warning">
      📄 Générer Standard
    </a>
    
    <a href="{{ route('admin.certificats.download', $certificat) }}" class="btn btn-danger">
      📥 Télécharger
    </a>
  </div>

  <div class="back-link">
    <a href="{{ route('admin.certificats.index') }}">
      ← Retour à la liste des certificats
    </a>
  </div>
</div>

<script>
  // Vérifier s'il y a un état sauvegardé
  window.addEventListener('load', function() {
    const certificatId = {{ $certificat->id }};
    const savedState = localStorage.getItem(`certificat_state_${certificatId}`);
    
    if (savedState) {
      try {
        const state = JSON.parse(savedState);
        console.log('État sauvegardé trouvé:', state);
        
        // Mettre à jour les informations affichées
        const stateInfo = document.querySelector('.state-info');
        stateInfo.innerHTML = `
          <h3>✅ État Sauvegardé Trouvé</h3>
          <p><strong>Dernière sauvegarde:</strong> ${new Date(state.timestamp).toLocaleString('fr-FR')}</p>
          <p><strong>Nom:</strong> ${state.nom.text}</p>
          <p><strong>Niveau:</strong> ${state.niveau.text}</p>
          <p><strong>Période:</strong> ${state.periode.text}</p>
          <p><strong>Image de fond:</strong> ${state.background ? 'Personnalisée' : 'Par défaut'}</p>
        `;
        
        // Ajouter un bouton pour générer avec l'état sauvegardé
        const actions = document.querySelector('.actions');
        const generateWithStateBtn = document.createElement('a');
        generateWithStateBtn.href = "{{ route('admin.certificats.generator', $certificat) }}";
        generateWithStateBtn.className = 'btn btn-success';
        generateWithStateBtn.innerHTML = '🚀 Générer avec État Sauvegardé';
        generateWithStateBtn.style.order = '-1'; // Placer en premier
        
        actions.insertBefore(generateWithStateBtn, actions.firstChild);
        
      } catch (error) {
        console.error('Erreur lors de la lecture de l\'état:', error);
      }
    } else {
      // Aucun état sauvegardé
      const stateInfo = document.querySelector('.state-info');
      stateInfo.innerHTML = `
        <h3>⚠️ Aucun État Sauvegardé</h3>
        <p>Vous n'avez pas encore sauvegardé d'état pour ce certificat.</p>
        <p>Utilisez l'éditeur de certificat pour créer et sauvegarder un état personnalisé.</p>
      `;
      
      // Ajouter un bouton pour aller à l'éditeur
      const actions = document.querySelector('.actions');
      const editBtn = document.createElement('a');
      editBtn.href = "{{ route('admin.certificats.generator', $certificat) }}";
      editBtn.className = 'btn btn-primary';
      editBtn.innerHTML = '✏️ Créer un État Personnalisé';
      editBtn.style.order = '-1'; // Placer en premier
      
      actions.insertBefore(editBtn, actions.firstChild);
    }
  });
    </script>

</body>
</html> 
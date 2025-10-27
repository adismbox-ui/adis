<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Générateur de Certificat ADIS</title>
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
  }
  
  .controls {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .size-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
  }
  
  .size-control {
    display: flex;
    align-items: center;
    gap: 5px;
    background: rgba(34, 197, 94, 0.1);
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid rgba(34, 197, 94, 0.3);
  }
  
  .size-control label {
    font-weight: bold;
    color: #2d5f4f;
    font-size: 14px;
  }
  
  .size-control input[type="range"] {
    width: 100px;
  }
  
     .size-control .size-value {
     min-width: 30px;
     text-align: center;
     font-weight: bold;
     color: #22c55e;
   }
   
   .color-controls {
     display: flex;
     gap: 10px;
     margin-bottom: 20px;
     flex-wrap: wrap;
     justify-content: center;
     align-items: center;
   }
   
   .color-control {
     display: flex;
     align-items: center;
     gap: 5px;
     background: rgba(239, 68, 68, 0.1);
     padding: 8px 12px;
     border-radius: 8px;
     border: 1px solid rgba(239, 68, 68, 0.3);
   }
   
   .color-control label {
     font-weight: bold;
     color: #dc2626;
     font-size: 14px;
   }
   
   .color-control input[type="color"] {
     width: 40px;
     height: 30px;
     border: none;
     border-radius: 4px;
     cursor: pointer;
   }
   
   .background-control {
     display: flex;
     align-items: center;
     gap: 10px;
     margin-bottom: 20px;
     justify-content: center;
     background: rgba(168, 85, 247, 0.1);
     padding: 12px 16px;
     border-radius: 8px;
     border: 1px solid rgba(168, 85, 247, 0.3);
   }
   
   .background-control label {
     font-weight: bold;
     color: #7c3aed;
     font-size: 14px;
   }
   
   .background-control select {
     padding: 8px 12px;
     border-radius: 6px;
     border: 2px solid #e5e7eb;
     background: white;
     min-width: 200px;
     font-size: 14px;
   }
   
       .background-control select:focus {
      outline: none;
      border-color: #a855f7;
      box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
    }
    
    .background-control input[type="file"] {
      display: none;
    }
    
    .style-controls {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
    }
    
    .style-control {
      display: flex;
      align-items: center;
      gap: 5px;
      background: rgba(34, 197, 94, 0.1);
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid rgba(34, 197, 94, 0.3);
    }
    
    .style-control label {
      font-weight: bold;
      color: #16a34a;
      font-size: 14px;
    }
    
    .style-btn {
      width: 30px;
      height: 30px;
      border: 2px solid #e5e7eb;
      background: white;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      font-size: 12px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #000000;
    }
    
    .style-btn:hover {
      border-color: #22c55e;
      background: rgba(34, 197, 94, 0.1);
    }
    
    .style-btn.active {
      background: #ffffff;
      color: #22c55e;
      border-color: #16a34a;
      font-weight: bold;
    }
  
  #certificat {
    position: relative;
    width: 1086px; /* largeur exacte de l'image */
    height: 768px; /* hauteur exacte */
    background: url("{{ asset('MODELE CERTIFICAT DE FORMATION.jpg') }}") no-repeat center/cover;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    border-radius: 10px;
    overflow: hidden;
    cursor: crosshair;
  }
  
  .field {
    position: absolute;
    font-weight: bold;
    color: #1a1a1a;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
    cursor: move;
    user-select: none;
    border: 2px dashed #22c55e;
    padding: 5px;
    transition: all 0.3s ease;
    background: rgba(34, 197, 94, 0.1);
    min-width: 100px;
    min-height: 30px;
  }
  
  .field:hover {
    border-color: #16a34a;
    background: rgba(34, 197, 94, 0.2);
    box-shadow: 0 0 10px rgba(34, 197, 94, 0.3);
  }
  
  .field.selected {
    border-color: #16a34a;
    background: rgba(34, 197, 94, 0.3);
    z-index: 1000;
    box-shadow: 0 0 15px rgba(34, 197, 94, 0.5);
  }
  
  .field .resize-handle {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #22c55e;
    border: 2px solid white;
    border-radius: 50%;
    cursor: nw-resize;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .field:hover .resize-handle,
  .field.selected .resize-handle {
    opacity: 1;
  }
  
  .field .resize-handle.top-left { top: -6px; left: -6px; cursor: nw-resize; }
  .field .resize-handle.top-right { top: -6px; right: -6px; cursor: ne-resize; }
  .field .field .resize-handle.bottom-left { bottom: -6px; left: -6px; cursor: sw-resize; }
  .field .resize-handle.bottom-right { bottom: -6px; right: -6px; cursor: se-resize; }
  
  #nom {
    top: 230px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 40px;
    color: #2d5f4f;
    text-align: center;
    min-width: 200px;
  }
  
  #niveau {
    top: 410px;
    left: 160px;
    font-size: 28px;
    text-align: left;
    color: #2d5f4f;
    min-width: 150px;
  }
  
  #periode {
    top: 410px;
    right: 160px;
    font-size: 28px;
    text-align: left;
    color: #2d5f4f;
    min-width: 150px;
  }
  
  input, button {
    padding: 12px 16px;
    font-size: 16px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
  }
  
  input {
    background: white;
    min-width: 200px;
  }
  
  input:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
  }
  
  button {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    font-weight: bold;
    cursor: pointer;
    border: none;
    min-width: 150px;
  }
  
  button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
  }
  
  .certificate-info {
    background: rgba(34, 197, 94, 0.1);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #22c55e;
  }
  
  .certificate-info h3 {
    margin: 0 0 10px 0;
    color: #2d5f4f;
  }
  
  .certificate-info p {
    margin: 5px 0;
    color: #374151;
  }
  
  .instructions {
    background: rgba(59, 130, 246, 0.1);
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #3b82f6;
    font-size: 14px;
  }
  
  .instructions h4 {
    margin: 0 0 10px 0;
    color: #1e40af;
  }
  
  .instructions ul {
    margin: 0;
    padding-left: 20px;
  }
  
  .instructions li {
    margin: 5px 0;
    color: #374151;
  }
  
  @media (max-width: 1200px) {
    #certificat {
      width: 800px;
      height: 566px;
      background-size: contain;
    }
    
    #nom {
      font-size: 30px;
      top: 170px;
    }
    
    #niveau, #periode {
      font-size: 20px;
      top: 300px;
    }
  }
  
  @media (max-width: 900px) {
    #certificat {
      width: 600px;
      height: 424px;
    }
    
    #nom {
      font-size: 24px;
      top: 130px;
    }
    
    #niveau, #periode {
      font-size: 16px;
      top: 220px;
    }
    
    .controls, .size-controls {
      flex-direction: column;
      align-items: center;
    }
    
    input, button {
      width: 100%;
      max-width: 300px;
    }
  }
  
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
</style>
</head>
<body>

<div class="container">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
    <a href="{{ route('admin.dashboard') }}" style="text-decoration:none; padding:10px 14px; border-radius:8px; background:linear-gradient(135deg,#16a34a,#065f46); color:#fff; font-weight:600; box-shadow:0 6px 18px rgba(22,163,74,.35);">
      ← Aller au Dashboard Admin
    </a>
    <span style="color:#64748b; font-size:14px;">Générateur de certificat</span>
  </div>
  <div class="certificate-info">
    <h3>📋 Informations du Certificat</h3>
    <p><strong>Apprenant:</strong> {{ $apprenant->prenom }} {{ $apprenant->nom }}</p>
    <p><strong>Module:</strong> {{ $module ? $module->titre : 'Formation complétée' }}</p>
    <p><strong>Niveau de l'apprenant:</strong> {{ $niveauApprenant ? $niveauApprenant->nom : 'Non défini' }}</p>
    @php
      $session = ($niveauApprenant && method_exists($niveauApprenant, 'sessionFormation')) ? $niveauApprenant->sessionFormation : null;
    @endphp
    <p><strong>Dates de la session:</strong> {{ ($session && $session->date_debut && $session->date_fin) ? (\Carbon\Carbon::parse($session->date_debut)->format('d/m/Y').' - '.\Carbon\Carbon::parse($session->date_fin)->format('d/m/Y')) : 'Non définies' }}</p>
    <p><strong>Numéro de certificat:</strong> {{ str_pad($certificat->id, 6, '0', STR_PAD_LEFT) }}</p>
  </div>

  <div class="instructions">
    <h4>🎯 Instructions d'utilisation :</h4>
    <ul>
      <li><strong>Déplacer :</strong> Cliquez et faites glisser les éléments</li>
      <li><strong>Redimensionner :</strong> Utilisez les points verts aux coins</li>
      <li><strong>Sélectionner :</strong> Cliquez sur un élément pour le sélectionner</li>
      <li><strong>Modifier le texte :</strong> Utilisez les champs ci-dessous</li>
      <li><strong>Réduire la taille :</strong> Utilisez les curseurs de taille</li>
    </ul>
  </div>

  <div class="controls">
    <input id="inpNom" placeholder="Nom complet" value="{{ $apprenant->prenom }} {{ $apprenant->nom }}">
    <input id="inpNiveau" placeholder="Niveau" value="{{ $niveauApprenant ? $niveauApprenant->nom : 'Non défini' }}">
    <input id="inpPeriode" placeholder="Période" value="{{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}">
    @php
      $session = ($niveauApprenant && method_exists($niveauApprenant, 'sessionFormation')) ? $niveauApprenant->sessionFormation : null;
    @endphp
    <input id="inpDate" placeholder="Date (début - fin)" value="{{ ($session && $session->date_debut && $session->date_fin) ? (\Carbon\Carbon::parse($session->date_debut)->format('d/m/Y').' - '.\Carbon\Carbon::parse($session->date_fin)->format('d/m/Y')) : '' }}">
    <button id="btnSave">📥 Télécharger le Certificat</button>
    <button id="btnSaveState" type="button" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">💾 Sauvegarder l'état</button>
    <button id="btnTestState" type="button" style="background: linear-gradient(135deg, #f59e0b, #d97706);">🧪 Tester l'état</button>
  </div>

  <div class="color-controls">
    <div class="color-control">
      <label>Couleur Nom:</label>
      <input type="color" id="colorNom" value="#2d5f4f">
    </div>
    <div class="color-control">
      <label>Couleur Niveau:</label>
      <input type="color" id="colorNiveau" value="#2d5f4f">
    </div>
    <div class="color-control">
      <label>Couleur Période:</label>
      <input type="color" id="colorPeriode" value="#2d5f4f">
    </div>
  </div>

  <div class="style-controls">
    <div class="style-control">
      <label>Style Nom:</label>
      <button type="button" id="boldNom" class="style-btn" title="Gras">B</button>
      <button type="button" id="italicNom" class="style-btn" title="Italique">I</button>
      <button type="button" id="underlineNom" class="style-btn" title="Souligné">U</button>
      <button type="button" id="uppercaseNom" class="style-btn" title="Majuscules">A</button>
    </div>
    <div class="style-control">
      <label>Style Niveau:</label>
      <button type="button" id="boldNiveau" class="style-btn" title="Gras">B</button>
      <button type="button" id="italicNiveau" class="style-btn" title="Italique">I</button>
      <button type="button" id="underlineNiveau" class="style-btn" title="Souligné">U</button>
      <button type="button" id="uppercaseNiveau" class="style-btn" title="Majuscules">A</button>
    </div>
    <div class="style-control">
      <label>Style Période:</label>
      <button type="button" id="boldPeriode" class="style-btn" title="Gras">B</button>
      <button type="button" id="italicPeriode" class="style-btn" title="Italique">I</button>
      <button type="button" id="underlinePeriode" class="style-btn" title="Souligné">U</button>
      <button type="button" id="uppercasePeriode" class="style-btn" title="Majuscules">A</button>
    </div>
  </div>

  <div class="background-control">
    <label>Image de fond:</label>
    <button type="button" id="chooseBackgroundBtn" style="
      background: linear-gradient(135deg, #a855f7, #7c3aed);
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(168, 85, 247, 0.4)'" 
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(168, 85, 247, 0.3)'">📁 Choisir une image</button>
    <input type="file" id="customBackground" accept="image/*" style="display: none;">
  </div>

  <div class="size-controls">
    <div class="size-control">
      <label>Nom:</label>
      <input type="range" id="sizeNom" min="12" max="60" value="40">
      <span class="size-value" id="sizeNomValue">40px</span>
    </div>
    <div class="size-control">
      <label>Niveau:</label>
      <input type="range" id="sizeNiveau" min="12" max="40" value="28">
      <span class="size-value" id="sizeNiveauValue">28px</span>
    </div>
    <div class="size-control">
      <label>Période:</label>
      <input type="range" id="sizePeriode" min="12" max="40" value="28">
      <span class="size-value" id="sizePeriodeValue">28px</span>
    </div>
    <div class="size-control">
      <label>Date (début - fin):</label>
      <input type="range" id="sizeDates" min="12" max="40" value="22">
      <span class="size-value" id="sizeDatesValue">22px</span>
    </div>
  </div>

  <div id="certificat">
    <div id="nom" class="field">
      {{ $apprenant->prenom }} {{ $apprenant->nom }}
      <div class="resize-handle top-left"></div>
      <div class="resize-handle top-right"></div>
      <div class="resize-handle bottom-left"></div>
      <div class="resize-handle bottom-right"></div>
    </div>
    <div id="niveau" class="field">
      {{ $niveauApprenant ? $niveauApprenant->nom : 'Non défini' }}
      <div class="resize-handle top-left"></div>
      <div class="resize-handle top-right"></div>
      <div class="resize-handle bottom-left"></div>
      <div class="resize-handle bottom-right"></div>
    </div>
    <div id="periode" class="field">
      {{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}
      <div class="resize-handle top-left"></div>
      <div class="resize-handle top-right"></div>
      <div class="resize-handle bottom-left"></div>
      <div class="resize-handle bottom-right"></div>
    </div>
    <div id="dates_session" class="field" style="top: 480px; left: 60px; font-size: 22px;">
      @php
        $session = null;
        if ($niveauApprenant && method_exists($niveauApprenant, 'sessionFormation')) {
            $session = $niveauApprenant->sessionFormation; // belongsTo SessionFormation via session_id
        }
      @endphp
      {{ ($session && $session->date_debut && $session->date_fin) ? (\Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y')) : '' }}
      <div class="resize-handle top-left"></div>
      <div class="resize-handle top-right"></div>
      <div class="resize-handle bottom-left"></div>
      <div class="resize-handle bottom-right"></div>
    </div>
  </div>
  
  <div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('admin.certificats.index') }}" style="color: #22c55e; text-decoration: none; font-weight: bold;">
      ← Retour à la liste des certificats
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
  let selectedField = null;
  let isDragging = false;
  let isResizing = false;
  let dragOffset = { x: 0, y: 0 };
  let resizeHandle = null;

  const nom = document.getElementById("nom");
  const niveau = document.getElementById("niveau");
  const periode = document.getElementById("periode");
  const datesSession = document.getElementById("dates_session");

  // Text input handlers
  document.getElementById("inpNom").addEventListener("input", e => {
    nom.textContent = e.target.value || "Nom Complet";
  });
  
  document.getElementById("inpNiveau").addEventListener("input", e => {
    niveau.textContent = e.target.value || "Niveau";
  });
  
  document.getElementById("inpPeriode").addEventListener("input", e => {
    periode.textContent = e.target.value || "Période";
  });
  const inpDate = document.getElementById("inpDate");
  if (inpDate && datesSession) {
    inpDate.addEventListener("input", e => {
      datesSession.textContent = e.target.value || "";
    });
  }

  // Size control handlers
  document.getElementById("sizeNom").addEventListener("input", e => {
    const size = e.target.value;
    nom.style.fontSize = size + "px";
    document.getElementById("sizeNomValue").textContent = size + "px";
  });
  
  document.getElementById("sizeNiveau").addEventListener("input", e => {
    const size = e.target.value;
    niveau.style.fontSize = size + "px";
    document.getElementById("sizeNiveauValue").textContent = size + "px";
  });
  
  document.getElementById("sizePeriode").addEventListener("input", e => {
    const size = e.target.value;
    periode.style.fontSize = size + "px";
    document.getElementById("sizePeriodeValue").textContent = size + "px";
  });
  const sizeDates = document.getElementById("sizeDates");
  if (sizeDates && datesSession) {
    sizeDates.addEventListener("input", e => {
      const size = e.target.value;
      datesSession.style.fontSize = size + "px";
      document.getElementById("sizeDatesValue").textContent = size + "px";
    });
  }

  // Color control handlers
  document.getElementById("colorNom").addEventListener("input", e => {
    nom.style.color = e.target.value;
  });
  
  document.getElementById("colorNiveau").addEventListener("input", e => {
    niveau.style.color = e.target.value;
  });
  
  document.getElementById("colorPeriode").addEventListener("input", e => {
    periode.style.color = e.target.value;
  });

  // Style control handlers for Nom
  document.getElementById("boldNom").addEventListener("click", e => {
    e.target.classList.toggle('active');
    nom.style.fontWeight = e.target.classList.contains('active') ? 'bold' : 'normal';
  });
  
  document.getElementById("italicNom").addEventListener("click", e => {
    e.target.classList.toggle('active');
    nom.style.fontStyle = e.target.classList.contains('active') ? 'italic' : 'normal';
  });
  
  document.getElementById("underlineNom").addEventListener("click", e => {
    e.target.classList.toggle('active');
    nom.style.textDecoration = e.target.classList.contains('active') ? 'underline' : 'none';
  });
  
  document.getElementById("uppercaseNom").addEventListener("click", e => {
    e.target.classList.toggle('active');
    nom.style.textTransform = e.target.classList.contains('active') ? 'uppercase' : 'none';
  });

  // Style control handlers for Niveau
  document.getElementById("boldNiveau").addEventListener("click", e => {
    e.target.classList.toggle('active');
    niveau.style.fontWeight = e.target.classList.contains('active') ? 'bold' : 'normal';
  });
  
  document.getElementById("italicNiveau").addEventListener("click", e => {
    e.target.classList.toggle('active');
    niveau.style.fontStyle = e.target.classList.contains('active') ? 'italic' : 'normal';
  });
  
  document.getElementById("underlineNiveau").addEventListener("click", e => {
    e.target.classList.toggle('active');
    niveau.style.textDecoration = e.target.classList.contains('active') ? 'underline' : 'none';
  });
  
  document.getElementById("uppercaseNiveau").addEventListener("click", e => {
    e.target.classList.toggle('active');
    niveau.style.textTransform = e.target.classList.contains('active') ? 'uppercase' : 'none';
  });

  // Style control handlers for Période
  document.getElementById("boldPeriode").addEventListener("click", e => {
    e.target.classList.toggle('active');
    periode.style.fontWeight = e.target.classList.contains('active') ? 'bold' : 'normal';
  });
  
  document.getElementById("italicPeriode").addEventListener("click", e => {
    e.target.classList.toggle('active');
    periode.style.fontStyle = e.target.classList.contains('active') ? 'italic' : 'normal';
  });
  
  document.getElementById("underlinePeriode").addEventListener("click", e => {
    e.target.classList.toggle('active');
    periode.style.textDecoration = e.target.classList.contains('active') ? 'underline' : 'none';
  });
  
  document.getElementById("uppercasePeriode").addEventListener("click", e => {
    e.target.classList.toggle('active');
    periode.style.textTransform = e.target.classList.contains('active') ? 'uppercase' : 'none';
  });

  // Background control handler
  document.getElementById("chooseBackgroundBtn").addEventListener("click", () => {
    document.getElementById("customBackground").click();
  });

  // Custom background file handler
  document.getElementById("customBackground").addEventListener("change", e => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(event) {
        const certificat = document.getElementById("certificat");
        certificat.style.backgroundImage = `url("${event.target.result}")`;
        
        // Mettre à jour le bouton pour montrer que c'est une image personnalisée
        const btn = document.getElementById("chooseBackgroundBtn");
        btn.textContent = "✅ Image chargée";
        btn.style.background = "linear-gradient(135deg, #22c55e, #16a34a)";
        
        // Stocker les données de l'image pour la sauvegarde
        certificat.dataset.customBackgroundData = event.target.result;
        
        console.log('Image personnalisée chargée:', event.target.result.substring(0, 100) + '...');
        
        // Remettre le bouton à l'état normal après 2 secondes
        setTimeout(() => {
          btn.textContent = "📁 Choisir une image";
          btn.style.background = "linear-gradient(135deg, #a855f7, #7c3aed)";
        }, 2000);
      };
      reader.readAsDataURL(file);
    }
  });

  // Field selection
  function selectField(field) {
    if (selectedField) {
      selectedField.classList.remove('selected');
    }
    selectedField = field;
    field.classList.add('selected');
  }

  // Mouse event handlers for dragging
  function handleMouseDown(e) {
    const field = e.target.closest('.field');
    if (!field) return;

    const handle = e.target.closest('.resize-handle');
    if (handle) {
      // Start resizing
      isResizing = true;
      resizeHandle = handle;
      selectField(field);
    } else {
      // Start dragging
      isDragging = true;
      selectField(field);
      const rect = field.getBoundingClientRect();
      dragOffset.x = e.clientX - rect.left;
      dragOffset.y = e.clientY - rect.top;
    }
    
    e.preventDefault();
  }

  function handleMouseMove(e) {
    if (!isDragging && !isResizing) return;

    if (isDragging && selectedField) {
      const certificat = document.getElementById("certificat");
      const certRect = certificat.getBoundingClientRect();
      
      let newX = e.clientX - certRect.left - dragOffset.x;
      let newY = e.clientY - certRect.top - dragOffset.y;
      
      // Keep within bounds
      newX = Math.max(0, Math.min(newX, certRect.width - selectedField.offsetWidth));
      newY = Math.max(0, Math.min(newY, certRect.height - selectedField.offsetHeight));
      
      selectedField.style.left = newX + 'px';
      selectedField.style.top = newY + 'px';
      selectedField.style.transform = 'none';
    }
    
    if (isResizing && selectedField && resizeHandle) {
      const certificat = document.getElementById("certificat");
      const certRect = certificat.getBoundingClientRect();
      const fieldRect = selectedField.getBoundingClientRect();
      
      let newWidth, newHeight;
      
      if (resizeHandle.classList.contains('bottom-right')) {
        newWidth = e.clientX - fieldRect.left;
        newHeight = e.clientY - fieldRect.top;
      } else if (resizeHandle.classList.contains('bottom-left')) {
        newWidth = fieldRect.right - e.clientX;
        newHeight = e.clientY - fieldRect.top;
        selectedField.style.left = e.clientX - certRect.left + 'px';
      } else if (resizeHandle.classList.contains('top-right')) {
        newWidth = e.clientX - fieldRect.left;
        newHeight = fieldRect.bottom - e.clientY;
        selectedField.style.top = e.clientY - certRect.top + 'px';
      } else if (resizeHandle.classList.contains('top-left')) {
        newWidth = fieldRect.right - e.clientX;
        newHeight = fieldRect.bottom - e.clientY;
        selectedField.style.left = e.clientX - certRect.left + 'px';
        selectedField.style.top = e.clientY - certRect.top + 'px';
      }
      
      // Minimum size
      newWidth = Math.max(50, newWidth);
      newHeight = Math.max(20, newHeight);
      
      // Keep within bounds
      const maxWidth = certRect.width - parseFloat(selectedField.style.left || 0);
      const maxHeight = certRect.height - parseFloat(selectedField.style.top || 0);
      newWidth = Math.min(newWidth, maxWidth);
      newHeight = Math.min(newHeight, maxHeight);
      
      selectedField.style.width = newWidth + 'px';
      selectedField.style.height = newHeight + 'px';
    }
  }

  function handleMouseUp() {
    isDragging = false;
    isResizing = false;
    resizeHandle = null;
  }

  // Event listeners
  document.addEventListener('mousedown', handleMouseDown);
  document.addEventListener('mousemove', handleMouseMove);
  document.addEventListener('mouseup', handleMouseUp);

  // Click to select
  document.addEventListener('click', (e) => {
    const field = e.target.closest('.field');
    if (field && !e.target.closest('.resize-handle')) {
      selectField(field);
    }
  });

  // Download functionality
  document.getElementById("btnSave").addEventListener("click", async () => {
    const button = document.getElementById("btnSave");
    const originalText = button.textContent;
    
    // Hide resize handles and selection borders for clean image
    const fields = document.querySelectorAll('.field');
    fields.forEach(field => {
      field.style.border = 'none';
      field.style.background = 'none';
      const handles = field.querySelectorAll('.resize-handle');
      handles.forEach(handle => handle.style.display = 'none');
    });
    
    // Show loading state
    button.textContent = "⏳ Génération en cours...";
    button.disabled = true;
    
    try {
      const canvas = await html2canvas(document.getElementById("certificat"), { 
        useCORS: true,
        allowTaint: true,
        backgroundColor: null,
        scale: 2, // Higher quality
        logging: false
      });
      
      const link = document.createElement("a");
      link.download = "certificat_{{ $apprenant->prenom }}_{{ $apprenant->nom }}_{{ date('Y-m-d') }}.png";
      link.href = canvas.toDataURL("image/png", 1.0);
      link.click();
      
      // Show success message
      button.textContent = "✅ Certificat téléchargé !";
      setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
      }, 2000);
      
    } catch (error) {
      console.error('Erreur lors de la génération:', error);
      button.textContent = "❌ Erreur de génération";
      setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
      }, 2000);
    } finally {
      // Restore resize handles and selection borders
      fields.forEach(field => {
        field.style.border = '';
        field.style.background = '';
        const handles = field.querySelectorAll('.resize-handle');
        handles.forEach(handle => handle.style.display = '');
      });
    }
  });

  // Save state functionality
  document.getElementById("btnSaveState").addEventListener("click", () => {
    const button = document.getElementById("btnSaveState");
    const originalText = button.textContent;
    
    try {
      // Get current background image correctly
      const certificat = document.getElementById("certificat");
      const currentBackground = certificat.style.backgroundImage;
      
      console.log('Image de fond actuelle:', currentBackground);
      
      // Collect current state EXACT - sans valeurs par défaut
      // Utiliser getComputedStyle pour s'assurer d'avoir les vraies valeurs
      const nomComputed = window.getComputedStyle(nom);
      const niveauComputed = window.getComputedStyle(niveau);
      const periodeComputed = window.getComputedStyle(periode);
      const datesComputed = datesSession ? window.getComputedStyle(datesSession) : null;
      
      // Récupérer les positions réelles des éléments de manière plus fiable
      console.log('📍 Récupération des positions:');
      
      // Utiliser les styles calculés ET getBoundingClientRect pour une approche hybride
      const nomStyle = window.getComputedStyle(nom);
      const niveauStyle = window.getComputedStyle(niveau);
      const periodeStyle = window.getComputedStyle(periode);
      const datesStyle = datesSession ? window.getComputedStyle(datesSession) : null;
      
      // Récupérer les positions depuis les styles CSS
      let nomLeft = nomStyle.left || '50%';
      let nomTop = nomStyle.top || '230px';
      let niveauLeft = niveauStyle.left || '160px';
      let niveauTop = niveauStyle.top || '410px';
      let periodeRight = periodeStyle.right || '160px';
      let periodeTop = periodeStyle.top || '410px';
      let datesLeft = datesStyle ? (datesStyle.left || '60px') : null;
      let datesTop = datesStyle ? (datesStyle.top || '480px') : null;
      
      // Si les styles sont 'auto' ou vides, utiliser getBoundingClientRect comme fallback
      if (nomLeft === 'auto' || nomLeft === '') {
        const nomRect = nom.getBoundingClientRect();
        const certificatRect = certificat.getBoundingClientRect();
        nomLeft = ((nomRect.left - certificatRect.left) / certificatRect.width * 100).toFixed(2) + '%';
        nomTop = ((nomRect.top - certificatRect.top) / certificatRect.height * 100).toFixed(2) + '%';
        console.log('  Nom - Fallback getBoundingClientRect:', nomLeft, nomTop);
      }
      
      if (niveauLeft === 'auto' || niveauLeft === '') {
        const niveauRect = niveau.getBoundingClientRect();
        const certificatRect = certificat.getBoundingClientRect();
        niveauLeft = ((niveauRect.left - certificatRect.left) / certificatRect.width * 100).toFixed(2) + '%';
        niveauTop = ((niveauRect.top - certificatRect.top) / certificatRect.height * 100).toFixed(2) + '%';
        console.log('  Niveau - Fallback getBoundingClientRect:', niveauLeft, niveauTop);
      }
      
      if (periodeRight === 'auto' || periodeRight === '') {
        const periodeRect = periode.getBoundingClientRect();
        const certificatRect = certificat.getBoundingClientRect();
        periodeRight = ((certificatRect.right - periodeRect.right) / certificatRect.width * 100).toFixed(2) + '%';
        periodeTop = ((periodeRect.top - certificatRect.top) / certificatRect.height * 100).toFixed(2) + '%';
        console.log('  Période - Fallback getBoundingClientRect:', periodeRight, periodeTop);
      }
      
      console.log('📍 Positions finales:');
      console.log('  Nom:', nomLeft, nomTop);
      console.log('  Niveau:', niveauLeft, niveauTop);
      console.log('  Période:', periodeRight, periodeTop);
      if (datesSession) console.log('  Dates session:', datesLeft, datesTop);
      
      // Vérifier que les positions sont différentes des valeurs par défaut
      const defaultNomLeft = '50%';
      const defaultNomTop = '230px';
      const defaultNiveauLeft = '160px';
      const defaultNiveauTop = '410px';
      const defaultPeriodeRight = '160px';
      const defaultPeriodeTop = '410px';
      const defaultDatesLeft = '60px';
      const defaultDatesTop = '480px';
      
      console.log('🔍 Comparaison avec les positions par défaut:');
      console.log('  Nom - Actuel:', nomLeft, nomTop, '| Par défaut:', defaultNomLeft, defaultNomTop);
      console.log('  Niveau - Actuel:', niveauLeft, niveauTop, '| Par défaut:', defaultNiveauLeft, defaultNiveauTop);
      console.log('  Période - Actuel:', periodeRight, periodeTop, '| Par défaut:', defaultPeriodeRight, defaultPeriodeTop);
      
      const state = {
        nom: {
          text: nom.textContent,
          fontSize: nomComputed.fontSize,
          left: nomLeft,
          top: nomTop,
          width: nomComputed.width,
          height: nomComputed.height,
          color: nomComputed.color,
          fontWeight: nomComputed.fontWeight,
          fontStyle: nomComputed.fontStyle,
          textDecoration: nomComputed.textDecoration,
          textTransform: nomComputed.textTransform
        },
        niveau: {
          text: niveau.textContent,
          fontSize: niveauComputed.fontSize,
          left: niveauLeft,
          top: niveauTop,
          width: niveauComputed.width,
          height: niveauComputed.height,
          color: niveauComputed.color,
          fontWeight: niveauComputed.fontWeight,
          fontStyle: niveauComputed.fontStyle,
          textDecoration: niveauComputed.textDecoration,
          textTransform: niveauComputed.textTransform
        },
        periode: {
          text: periode.textContent,
          fontSize: periodeComputed.fontSize,
          right: periodeRight,
          top: periodeTop,
          width: periodeComputed.width,
          height: periodeComputed.height,
          color: periodeComputed.color,
          fontWeight: periodeComputed.fontWeight,
          fontStyle: periodeComputed.fontStyle,
          textDecoration: periodeComputed.textDecoration,
          textTransform: periodeComputed.textTransform
        },
        dates_session: datesSession ? {
          text: datesSession.textContent,
          fontSize: datesComputed.fontSize,
          left: datesLeft,
          top: datesTop,
          width: datesComputed.width,
          height: datesComputed.height,
          color: datesComputed.color,
          fontWeight: datesComputed.fontWeight,
          fontStyle: datesComputed.fontStyle,
          textDecoration: datesComputed.textDecoration,
          textTransform: datesComputed.textTransform
        } : null,
        background: currentBackground || 'url("{{ asset("MODELE CERTIFICAT DE FORMATION.jpg") }}")',
        customBackgroundData: certificat.dataset.customBackgroundData || null,
        timestamp: new Date().toISOString(),
        certificatId: {{ $certificat->id }},
        apprenantId: {{ $apprenant->id }}
      };
      
      // Log complet de l'état sauvegardé
      console.log('🎨 ÉTAT COMPLET à sauvegarder:');
      console.log('  Nom:', {
        texte: state.nom.text,
        couleur: state.nom.color,
        position: { left: state.nom.left, top: state.nom.top },
        taille: { width: state.nom.width, height: state.nom.height, fontSize: state.nom.fontSize }
      });
      console.log('  Niveau:', {
        texte: state.niveau.text,
        couleur: state.niveau.color,
        position: { left: state.niveau.left, top: state.niveau.top },
        taille: { width: state.niveau.width, height: state.niveau.height, fontSize: state.niveau.fontSize }
      });
      console.log('  Période:', {
        texte: state.periode.text,
        couleur: state.periode.color,
        position: { right: state.periode.right, top: state.periode.top },
        taille: { width: state.periode.width, height: state.periode.height, fontSize: state.periode.fontSize }
      });
      
      // Save to localStorage
      const key = `certificat_state_{{ $certificat->id }}`;
      localStorage.setItem(key, JSON.stringify(state));
      
      // Verify save
      const saved = localStorage.getItem(key);
      if (saved) {
        console.log('✅ État sauvegardé avec succès dans localStorage');
        console.log('Clé utilisée:', key);
        console.log('Données sauvegardées:', JSON.parse(saved));
      } else {
        console.error('❌ Échec de la sauvegarde dans localStorage');
      }
      
      // Show success message
      button.textContent = "✅ État sauvegardé !";
      button.style.background = "linear-gradient(135deg, #22c55e, #16a34a)";
      
      setTimeout(() => {
        button.textContent = originalText;
        button.style.background = "linear-gradient(135deg, #3b82f6, #1d4ed8)";
      }, 2000);
      
    } catch (error) {
      console.error('Erreur lors de la sauvegarde:', error);
      button.textContent = "❌ Erreur de sauvegarde";
      button.style.background = "linear-gradient(135deg, #ef4444, #dc2626)";
      
      setTimeout(() => {
        button.textContent = originalText;
        button.style.background = "linear-gradient(135deg, #3b82f6, #1d4ed8)";
      }, 2000);
    }
  });

  // Test state functionality
  document.getElementById("btnTestState").addEventListener("click", () => {
    const button = document.getElementById("btnTestState");
    const originalText = button.textContent;
    
    try {
      // Get current state from localStorage
      const key = `certificat_state_{{ $certificat->id }}`;
      const savedState = localStorage.getItem(key);
      
      if (!savedState) {
        button.textContent = "❌ Aucun état";
        button.style.background = "linear-gradient(135deg, #ef4444, #dc2626)";
        setTimeout(() => {
          button.textContent = originalText;
          button.style.background = "linear-gradient(135deg, #f59e0b, #d97706)";
        }, 2000);
        return;
      }
      
      const state = JSON.parse(savedState);
      
      // Show current state in console
      console.log('🧪 ÉTAT ACTUEL dans localStorage:');
      console.log('Clé:', key);
      console.log('État complet:', state);
      console.log('  Nom:', state.nom);
      console.log('  Niveau:', state.niveau);
      console.log('  Période:', state.periode);
      console.log('  Arrière-plan:', state.background);
      console.log('  Timestamp:', state.timestamp);
      
      // Test de simulation du téléchargement
      console.log('🧪 SIMULATION du téléchargement:');
      console.log('  Création d\'un div temporaire...');
      
      const testDiv = document.createElement('div');
      testDiv.style.cssText = `
        position: absolute;
        left: -9999px;
        width: 1086px;
        height: 768px;
        background: ${state.background || 'url("{{ asset("MODELE CERTIFICAT DE FORMATION.jpg") }}")'};
        overflow: hidden;
      `;
      
      // Créer les éléments de test avec l'état sauvegardé
      const testNom = document.createElement('div');
      testNom.style.cssText = `
        position: absolute;
        top: ${state.nom.top};
        left: ${state.nom.left};
        font-size: ${state.nom.fontSize};
        color: ${state.nom.color};
        text-align: center;
        min-width: 200px;
        font-weight: ${state.nom.fontWeight};
        font-style: ${state.nom.fontStyle};
        text-decoration: ${state.nom.textDecoration};
        text-transform: ${state.nom.textTransform};
      `;
      testNom.textContent = state.nom.text;
      
      const testNiveau = document.createElement('div');
      testNiveau.style.cssText = `
        position: absolute;
        top: ${state.niveau.top};
        left: ${state.niveau.left};
        font-size: ${state.niveau.fontSize};
        color: ${state.niveau.color};
        text-align: left;
        min-width: 150px;
        font-weight: ${state.niveau.fontWeight};
        font-style: ${state.niveau.fontStyle};
        text-decoration: ${state.niveau.textDecoration};
        text-transform: ${state.niveau.textTransform};
      `;
      testNiveau.textContent = state.niveau.text;
      
      const testPeriode = document.createElement('div');
      testPeriode.style.cssText = `
        position: absolute;
        top: ${state.periode.top};
        right: ${state.periode.right};
        font-size: ${state.periode.fontSize};
        color: ${state.periode.color};
        text-align: left;
        min-width: 150px;
        font-weight: ${state.periode.fontWeight};
        font-style: ${state.periode.fontStyle};
        text-decoration: ${state.periode.textDecoration};
        text-transform: ${state.periode.textTransform};
      `;
      testPeriode.textContent = state.periode.text;
      
      testDiv.appendChild(testNom);
      testDiv.appendChild(testNiveau);
      testDiv.appendChild(testPeriode);
      
      console.log('  Éléments de test créés:');
      console.log('    Nom - position:', testNom.style.top, testNom.style.left, 'couleur:', testNom.style.color);
      console.log('    Niveau - position:', testNiveau.style.top, testNiveau.style.left, 'couleur:', testNiveau.style.color);
      console.log('    Période - position:', testPeriode.style.top, testPeriode.style.right, 'couleur:', testPeriode.style.color);
      
      // Test de conversion des positions (comme dans le téléchargement)
      console.log('🧪 TEST de conversion des positions:');
      
      const convertPosition = (value, containerSize, isRight = false) => {
        if (typeof value === 'string' && value.includes('%')) {
          const percentage = parseFloat(value) / 100;
          const result = isRight ? (containerSize * (1 - percentage)) : (containerSize * percentage);
          return result + 'px';
        }
        return value;
      };
      
      const containerWidth = 1086;
      const containerHeight = 768;
      
      const testNomTop = convertPosition(state.nom.top, containerHeight);
      const testNomLeft = convertPosition(state.nom.left, containerWidth);
      const testNiveauTop = convertPosition(state.niveau.top, containerHeight);
      const testNiveauLeft = convertPosition(state.niveau.left, containerWidth);
      const testPeriodeTop = convertPosition(state.periode.top, containerHeight);
      const testPeriodeRight = convertPosition(state.periode.right, containerWidth, true);
      
      console.log('  Positions converties:');
      console.log('    Nom - top:', testNomTop, 'left:', testNomLeft);
      console.log('    Niveau - top:', testNiveauTop, 'left:', testNiveauLeft);
      console.log('    Période - top:', testPeriodeTop, 'right:', testPeriodeRight);
      
      // Nettoyer
      testDiv.remove();
      
      // Show success message
      button.textContent = "✅ Test complet !";
      button.style.background = "linear-gradient(135deg, #22c55e, #16a34a)";
      
      setTimeout(() => {
        button.textContent = originalText;
        button.style.background = "linear-gradient(135deg, #f59e0b, #d97706)";
      }, 2000);
      
    } catch (error) {
      console.error('Erreur lors du test:', error);
      button.textContent = "❌ Erreur de test";
      button.style.background = "linear-gradient(135deg, #ef4444, #dc2626)";
      
      setTimeout(() => {
        button.textContent = originalText;
        button.style.background = "linear-gradient(135deg, #f59e0b, #d97706)";
      }, 2000);
    }
  });

  // Test state functionality
  document.getElementById("btnTestState").addEventListener("click", () => {
    const button = document.getElementById("btnTestState");
    const originalText = button.textContent;
    
    try {
      const key = `certificat_state_{{ $certificat->id }}`;
      const saved = localStorage.getItem(key);
      
      if (saved) {
        const state = JSON.parse(saved);
        console.log('🧪 Test - État trouvé dans localStorage:');
        console.log('Clé:', key);
        console.log('État:', state);
        
        button.textContent = "✅ État trouvé !";
        button.style.background = "linear-gradient(135deg, #22c55e, #16a34a)";
        
        // Afficher les détails dans une alerte
        alert(`État trouvé pour le certificat {{ $certificat->id }}:\n\nNom: ${state.nom.text}\nNiveau: ${state.niveau.text}\nPériode: ${state.periode.text}\n\nVérifiez la console pour plus de détails.`);
        
      } else {
        console.log('🧪 Test - Aucun état trouvé pour la clé:', key);
        button.textContent = "❌ Aucun état";
        button.style.background = "linear-gradient(135deg, #ef4444, #dc2626)";
        
        alert('Aucun état sauvegardé trouvé pour ce certificat.');
      }
      
      setTimeout(() => {
        button.textContent = originalText;
        button.style.background = "linear-gradient(135deg, #f59e0b, #d97706)";
      }, 2000);
      
    } catch (error) {
      console.error('Erreur lors du test:', error);
      button.textContent = "❌ Erreur test";
      button.style.background = "linear-gradient(135deg, #ef4444, #dc2626)";
      
      setTimeout(() => {
        button.textContent = originalText;
        button.style.background = "linear-gradient(135deg, #f59e0b, #d97706)";
      }, 2000);
    }
  });

  // Load saved state on page load
  function loadSavedState() {
    try {
      const savedState = localStorage.getItem(`certificat_state_{{ $certificat->id }}`);
      if (savedState) {
        const state = JSON.parse(savedState);
        
        console.log('État trouvé dans localStorage:', state);
        
        // Apply saved state
        if (state.nom) {
          nom.textContent = state.nom.text;
          nom.style.fontSize = state.nom.fontSize;
          nom.style.left = state.nom.left;
          nom.style.top = state.nom.top;
          if (state.nom.width !== 'auto') nom.style.width = state.nom.width;
          if (state.nom.height !== 'auto') nom.style.height = state.nom.height;
          if (state.nom.color) nom.style.color = state.nom.color;
          if (state.nom.fontWeight) nom.style.fontWeight = state.nom.fontWeight;
          if (state.nom.fontStyle) nom.style.fontStyle = state.nom.fontStyle;
          if (state.nom.textDecoration) nom.style.textDecoration = state.nom.textDecoration;
          if (state.nom.textTransform) nom.style.textTransform = state.nom.textTransform;
          
          // Update size control
          const nomSize = parseInt(state.nom.fontSize);
          if (nomSize >= 12 && nomSize <= 60) {
            document.getElementById("sizeNom").value = nomSize;
            document.getElementById("sizeNomValue").textContent = nomSize + "px";
          }
          
          // Update color control
          if (state.nom.color) {
            document.getElementById("colorNom").value = state.nom.color;
          }
          
          // Update style controls
          if (state.nom.fontWeight === 'bold') document.getElementById("boldNom").classList.add('active');
          if (state.nom.fontStyle === 'italic') document.getElementById("italicNom").classList.add('active');
          if (state.nom.textDecoration === 'underline') document.getElementById("underlineNom").classList.add('active');
          if (state.nom.textTransform === 'uppercase') document.getElementById("uppercaseNom").classList.add('active');
        }
        
        if (state.niveau) {
          niveau.textContent = state.niveau.text;
          niveau.style.fontSize = state.niveau.fontSize;
          niveau.style.left = state.niveau.left;
          niveau.style.top = state.niveau.top;
          if (state.niveau.width !== 'auto') niveau.style.width = state.niveau.width;
          if (state.niveau.height !== 'auto') niveau.style.height = state.niveau.height;
          if (state.niveau.color) niveau.style.color = state.niveau.color;
          if (state.niveau.fontWeight) niveau.style.fontWeight = state.niveau.fontWeight;
          if (state.niveau.fontStyle) niveau.style.fontStyle = state.niveau.fontStyle;
          if (state.niveau.textDecoration) niveau.style.textDecoration = state.niveau.textDecoration;
          if (state.niveau.textTransform) niveau.style.textTransform = state.niveau.textTransform;
          
          // Update size control
          const niveauSize = parseInt(state.niveau.fontSize);
          if (niveauSize >= 12 && niveauSize <= 40) {
            document.getElementById("sizeNiveau").value = niveauSize;
            document.getElementById("sizeNiveauValue").textContent = niveauSize + "px";
          }
          
          // Update color control
          if (state.niveau.color) {
            document.getElementById("colorNiveau").value = state.niveau.color;
          }
          
          // Update style controls
          if (state.niveau.fontWeight === 'bold') document.getElementById("boldNiveau").classList.add('active');
          if (state.niveau.fontStyle === 'italic') document.getElementById("italicNiveau").classList.add('active');
          if (state.niveau.textDecoration === 'underline') document.getElementById("underlineNiveau").classList.add('active');
          if (state.niveau.textTransform === 'uppercase') document.getElementById("uppercaseNiveau").classList.add('active');
        }
        
        if (state.periode) {
          periode.textContent = state.periode.text;
          periode.style.fontSize = state.periode.fontSize;
          periode.style.top = state.periode.top;
          if (state.periode.left) periode.style.left = state.periode.left;
          if (state.periode.right) periode.style.right = state.periode.right;
          if (state.periode.width !== 'auto') periode.style.width = state.periode.width;
          if (state.periode.height !== 'auto') periode.style.height = state.periode.height;
          if (state.periode.color) periode.style.color = state.periode.color;
          if (state.periode.fontWeight) periode.style.fontWeight = state.periode.fontWeight;
          if (state.periode.fontStyle) periode.style.fontStyle = state.periode.fontStyle;
          if (state.periode.textDecoration) periode.style.textDecoration = state.periode.textDecoration;
          if (state.periode.textTransform) periode.style.textTransform = state.periode.textTransform;
        }
        if (state.dates_session && typeof datesSession !== 'undefined' && datesSession) {
          datesSession.textContent = state.dates_session.text;
          datesSession.style.fontSize = state.dates_session.fontSize;
          if (state.dates_session.left) datesSession.style.left = state.dates_session.left;
          if (state.dates_session.top) datesSession.style.top = state.dates_session.top;
          if (state.dates_session.width !== 'auto') datesSession.style.width = state.dates_session.width;
          if (state.dates_session.height !== 'auto') datesSession.style.height = state.dates_session.height;
          if (state.dates_session.color) datesSession.style.color = state.dates_session.color;
          if (state.dates_session.fontWeight) datesSession.style.fontWeight = state.dates_session.fontWeight;
          if (state.dates_session.fontStyle) datesSession.style.fontStyle = state.dates_session.fontStyle;
          if (state.dates_session.textDecoration) datesSession.style.textDecoration = state.dates_session.textDecoration;
          if (state.dates_session.textTransform) datesSession.style.textTransform = state.dates_session.textTransform;
        }
        
        // Update input fields
        document.getElementById("inpNom").value = state.nom.text;
        document.getElementById("inpNiveau").value = state.niveau.text;
        document.getElementById("inpPeriode").value = state.periode.text;
        
        // Restore background image
        if (state.background) {
          const certificat = document.getElementById("certificat");
          
          console.log('Restauration de l\'arrière-plan:', state.background);
          
          // Si c'est une image personnalisée avec des données sauvegardées
          if (state.customBackgroundData) {
            console.log('Restauration d\'une image personnalisée');
            certificat.style.backgroundImage = `url("${state.customBackgroundData}")`;
            certificat.dataset.customBackgroundData = state.customBackgroundData;
            document.getElementById("chooseBackgroundBtn").textContent = "📁 Choisir une image";
            document.getElementById("chooseBackgroundBtn").style.background = "linear-gradient(135deg, #a855f7, #7c3aed)";
          } else {
            console.log('Restauration d\'une image prédéfinie');
            certificat.style.backgroundImage = state.background;
          }
        }
        
        console.log('État restauré:', state);
        
        // Show notification
        const notification = document.createElement('div');
        notification.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: linear-gradient(135deg, #22c55e, #16a34a);
          color: white;
          padding: 15px 20px;
          border-radius: 10px;
          box-shadow: 0 4px 20px rgba(0,0,0,0.2);
          z-index: 10000;
          animation: slideInRight 0.5s ease;
        `;
        notification.innerHTML = '🔄 État précédent restauré !';
        document.body.appendChild(notification);
        
        setTimeout(() => {
          notification.remove();
        }, 3000);
      } else {
        console.log('Aucun état sauvegardé trouvé');
      }
    } catch (error) {
      console.error('Erreur lors du chargement de l\'état:', error);
    }
  }

  // Auto-resize certificate on window resize
  window.addEventListener('resize', () => {
    const certificat = document.getElementById("certificat");
    const container = document.querySelector(".container");
    const containerWidth = container.offsetWidth;
    
    if (containerWidth < 1200) {
      const scale = containerWidth / 1200;
      certificat.style.transform = `scale(${scale})`;
      certificat.style.transformOrigin = 'top center';
    } else {
      certificat.style.transform = 'scale(1)';
    }
  });
  
  // Load saved state when page loads
  window.addEventListener('load', loadSavedState);
  
  // Initial resize
  window.dispatchEvent(new Event('resize'));
   
   // Écouter les messages de la page parent (pour la génération automatique)
   window.addEventListener('message', async function(event) {
     if (event.data.type === 'restoreState') {
       try {
         const state = JSON.parse(event.data.state);
         applyState(state);
         console.log('État restauré via message:', state);
       } catch (error) {
         console.error('Erreur lors de la restauration de l\'état via message:', error);
       }
     } else if (event.data.type === 'generateCertificate') {
       try {
         // Masquer les éléments d'interface pour une image propre
         const fields = document.querySelectorAll('.field');
         fields.forEach(field => {
           field.style.border = 'none';
           field.style.background = 'none';
           const handles = field.querySelectorAll('.resize-handle');
           handles.forEach(handle => handle.style.display = 'none');
         });
         
         // Générer le certificat
         const canvas = await html2canvas(document.getElementById("certificat"), { 
           useCORS: true,
           allowTaint: true,
           backgroundColor: null,
           scale: 2,
           logging: false
         });
         
         // Créer le lien de téléchargement
         const link = document.createElement("a");
         const apprenant = document.querySelector('#nom').textContent || 'Apprenant';
         link.download = `certificat_${apprenant.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.png`;
         link.href = canvas.toDataURL("image/png", 1.0);
         link.click();
         
         // Restaurer les éléments d'interface
         fields.forEach(field => {
           field.style.border = '';
           field.style.background = '';
           const handles = field.querySelectorAll('.resize-handle');
           handles.forEach(handle => handle.style.display = '');
         });
         
         console.log('Certificat généré automatiquement');
         
       } catch (error) {
         console.error('Erreur lors de la génération automatique:', error);
       }
     }
   });
   
   // Fonction pour appliquer un état
   function applyState(state) {
     if (state.nom) {
       nom.textContent = state.nom.text;
       nom.style.fontSize = state.nom.fontSize;
       nom.style.left = state.nom.left;
       nom.style.top = state.nom.top;
       if (state.nom.width !== 'auto') nom.style.width = state.nom.width;
       if (state.nom.height !== 'auto') nom.style.height = state.nom.height;
       if (state.nom.color) nom.style.color = state.nom.color;
       if (state.nom.fontWeight) nom.style.fontWeight = state.nom.fontWeight;
       if (state.nom.fontStyle) nom.style.fontStyle = state.nom.fontStyle;
       if (state.nom.textDecoration) nom.style.textDecoration = state.nom.textDecoration;
       if (state.nom.textTransform) nom.style.textTransform = state.nom.textTransform;
     }
     
     if (state.niveau) {
       niveau.textContent = state.niveau.text;
       niveau.style.fontSize = state.niveau.fontSize;
       niveau.style.left = state.niveau.left;
       niveau.style.top = state.niveau.top;
       if (state.niveau.width !== 'auto') niveau.style.width = state.niveau.width;
       if (state.niveau.height !== 'auto') niveau.style.height = state.niveau.height;
       if (state.niveau.color) niveau.style.color = state.niveau.color;
       if (state.niveau.fontWeight) niveau.style.fontWeight = state.niveau.fontWeight;
       if (state.niveau.fontStyle) niveau.style.fontStyle = state.niveau.fontStyle;
       if (state.niveau.textDecoration) niveau.style.textDecoration = state.niveau.textDecoration;
       if (state.niveau.textTransform) niveau.style.textTransform = state.niveau.textTransform;
     }
     
     if (state.periode) {
       periode.textContent = state.periode.text;
       periode.style.fontSize = state.periode.fontSize;
       periode.style.top = state.periode.top;
       if (state.periode.left) periode.style.left = state.periode.left;
       if (state.periode.right) periode.style.right = state.periode.right;
       if (state.periode.width !== 'auto') periode.style.width = state.periode.width;
       if (state.periode.height !== 'auto') periode.style.height = state.periode.height;
       if (state.periode.color) periode.style.color = state.periode.color;
       if (state.periode.fontWeight) periode.style.fontWeight = state.periode.fontWeight;
       if (state.periode.fontStyle) periode.style.fontStyle = state.periode.fontStyle;
       if (state.periode.textDecoration) periode.style.textDecoration = state.periode.textDecoration;
       if (state.periode.textTransform) periode.style.textTransform = state.periode.textTransform;
     }
     if (state.dates_session && typeof datesSession !== 'undefined' && datesSession) {
       datesSession.textContent = state.dates_session.text;
       datesSession.style.fontSize = state.dates_session.fontSize;
       if (state.dates_session.left) datesSession.style.left = state.dates_session.left;
       if (state.dates_session.top) datesSession.style.top = state.dates_session.top;
       if (state.dates_session.width !== 'auto') datesSession.style.width = state.dates_session.width;
       if (state.dates_session.height !== 'auto') datesSession.style.height = state.dates_session.height;
       if (state.dates_session.color) datesSession.style.color = state.dates_session.color;
       if (state.dates_session.fontWeight) datesSession.style.fontWeight = state.dates_session.fontWeight;
       if (state.dates_session.fontStyle) datesSession.style.fontStyle = state.dates_session.fontStyle;
       if (state.dates_session.textDecoration) datesSession.style.textDecoration = state.dates_session.textDecoration;
       if (state.dates_session.textTransform) datesSession.style.textTransform = state.dates_session.textTransform;
     }
     
     if (state.background) {
       const certificat = document.getElementById("certificat");
       if (state.customBackgroundData) {
         certificat.style.backgroundImage = `url("${state.customBackgroundData}")`;
         certificat.dataset.customBackgroundData = state.customBackgroundData;
         document.getElementById("chooseBackgroundBtn").textContent = "📁 Choisir une image";
         document.getElementById("chooseBackgroundBtn").style.background = "linear-gradient(135deg, #a855f7, #7c3aed)";
       } else {
         certificat.style.backgroundImage = state.background;
       }
     }
   }
</script>

</body>
</html>

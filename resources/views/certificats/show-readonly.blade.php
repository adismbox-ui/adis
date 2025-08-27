@extends('apprenants.layout')

@section('content')
<style>
  /* Adapter la zone pour ne pas dépasser sous le header et rester visible à droite du sidebar */
  .generator-wrapper { max-width: calc(100vw - 260px); overflow-x: auto; }
  #certificat {
    position: relative;
    width: 1086px;
    height: 768px;
    background: url("{{ asset('MODELE CERTIFICAT DE FORMATION.jpg') }}") no-repeat center/cover;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    border-radius: 10px;
    overflow: hidden;
    margin: 20px auto;
  }
  .field {
    position: absolute;
    font-weight: bold;
    color: #1a1a1a;
    text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
    user-select: none;
    border: 2px dashed #22c55e;
    padding: 6px 10px;
    background: rgba(34, 197, 94, 0.08);
    transition: box-shadow .2s ease, background .2s ease, border-color .2s ease;
    min-width: 120px;
    min-height: 28px;
  }
  .field:hover { border-color: #16a34a; background: rgba(34,197,94,.15); box-shadow: 0 0 10px rgba(34,197,94,.3); }
  .field.dragging { cursor: grabbing; }
  .resize-handle {
    position: absolute;
    width: 12px; height: 12px;
    background: #22c55e; border: 2px solid #fff; border-radius: 50%;
  }
  .resize-handle.top-left { top: -8px; left: -8px; cursor: nw-resize; }
  .resize-handle.top-right { top: -8px; right: -8px; cursor: ne-resize; }
  .resize-handle.bottom-left { bottom: -8px; left: -8px; cursor: sw-resize; }
  .resize-handle.bottom-right { bottom: -8px; right: -8px; cursor: se-resize; }

  #nom { top: 230px; left: 50%; transform: translateX(-50%); font-size: 40px; color: #2d5f4f; text-align: center; }
  #niveau { top: 410px; left: 160px; font-size: 28px; text-align: left; color: #2d5f4f; }
  /* On initialise "periode" avec right, puis JS convertira en left pour permettre le drag uniformisé */
  #periode { top: 410px; right: 160px; font-size: 28px; text-align: left; color: #2d5f4f; }
  .btn-primary { display:block; width: 280px; margin: 10px auto; }
</style>

<div class="container py-4 generator-wrapper">
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title mb-3">🛠️ Ajuster la taille des textes</h5>
      <div class="row g-3 align-items-center">
        <div class="col-md-4">
          <label class="form-label mb-1">Nom</label>
          <div class="d-flex align-items-center gap-2">
            <input id="sizeNom" type="range" min="16" max="72" step="1" class="form-range" style="width:100%">
            <span id="sizeNomValue" class="badge bg-success">40px</span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label mb-1">Niveau</label>
          <div class="d-flex align-items-center gap-2">
            <input id="sizeNiveau" type="range" min="12" max="48" step="1" class="form-range" style="width:100%">
            <span id="sizeNiveauValue" class="badge bg-success">28px</span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label mb-1">Période</label>
          <div class="d-flex align-items-center gap-2">
            <input id="sizePeriode" type="range" min="12" max="48" step="1" class="form-range" style="width:100%">
            <span id="sizePeriodeValue" class="badge bg-success">28px</span>
          </div>
        </div>
      </div>
      <small class="text-muted">Astuce: placez la souris sur un champ et utilisez la molette pour ajuster la taille.</small>
    </div>
  </div>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">📋 Informations du Certificat</h5>
      <p class="mb-1"><strong>Apprenant:</strong> {{ $apprenant->prenom }} {{ $apprenant->nom }}</p>
      <p class="mb-1"><strong>Niveau de l'apprenant:</strong> {{ $niveauApprenant?->nom ?? 'Non défini' }}</p>
      <p class="mb-1"><strong>Date d'obtention:</strong> {{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}</p>
      <p class="mb-0"><strong>Numéro de certificat:</strong> {{ str_pad($certificat->id, 6, '0', STR_PAD_LEFT) }}</p>
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
      {{ $niveauApprenant?->nom ?? 'Non défini' }}
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
  </div>

  <button id="btnDownload" class="btn btn-primary"><i class="fas fa-download me-2"></i>Télécharger le certificat</button>
  <div class="text-center"><a href="{{ route('apprenants.dashboard') }}">← Retour au tableau de bord</a></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
  const container = document.getElementById('certificat');
  const fields = Array.from(container.querySelectorAll('.field'));

  // Convertir l'ancrage "right" de #periode en "left" pour un drag uniforme
  (function normalizePositions() {
    const periode = document.getElementById('periode');
    const rectC = container.getBoundingClientRect();
    const rect = periode.getBoundingClientRect();
    const left = rect.left - rectC.left;
    const top = rect.top - rectC.top;
    periode.style.left = left + 'px';
    periode.style.top = top + 'px';
    periode.style.right = 'auto';
  })();

  let isDragging = false;
  let isResizing = false;
  let activeField = null;
  let dragOffset = { x: 0, y: 0 };
  let startSize = { w: 0, h: 0 };
  let startMouse = { x: 0, y: 0 };
  let activeHandle = null;

  function onMouseDown(e) {
    const field = e.target.closest('.field');
    if (!field) return;

    const handle = e.target.closest('.resize-handle');
    if (handle) {
      // Resize
      isResizing = true;
      activeField = field;
      activeHandle = handle;
      const r = field.getBoundingClientRect();
      startSize = { w: r.width, h: r.height };
      startMouse = { x: e.clientX, y: e.clientY };
      document.addEventListener('mousemove', onMouseMove);
      document.addEventListener('mouseup', onMouseUp);
      e.preventDefault();
      return;
    }

    // Drag
    isDragging = true;
    activeField = field;
    const rectC = container.getBoundingClientRect();
    const rectF = field.getBoundingClientRect();
    dragOffset.x = e.clientX - rectF.left;
    dragOffset.y = e.clientY - rectF.top;
    field.classList.add('dragging');
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
    e.preventDefault();
  }

  function onMouseMove(e) {
    if (!activeField) return;
    const rectC = container.getBoundingClientRect();

    if (isDragging) {
      let newX = e.clientX - rectC.left - dragOffset.x;
      let newY = e.clientY - rectC.top - dragOffset.y;
      // Bornes
      newX = Math.max(0, Math.min(newX, rectC.width - activeField.offsetWidth));
      newY = Math.max(0, Math.min(newY, rectC.height - activeField.offsetHeight));
      activeField.style.left = newX + 'px';
      activeField.style.top = newY + 'px';
      activeField.style.transform = 'none';
    }

    if (isResizing) {
      const dx = e.clientX - startMouse.x;
      const dy = e.clientY - startMouse.y;
      let w = startSize.w;
      let h = startSize.h;
      if (activeHandle.classList.contains('bottom-right')) { w += dx; h += dy; }
      if (activeHandle.classList.contains('bottom-left'))  { w -= dx; h += dy; }
      if (activeHandle.classList.contains('top-right'))    { w += dx; h -= dy; }
      if (activeHandle.classList.contains('top-left'))     { w -= dx; h -= dy; }
      // Minimums
      w = Math.max(80, w);
      h = Math.max(24, h);
      // Bornes pour rester dans le conteneur
      const rectF = activeField.getBoundingClientRect();
      const left = rectF.left - rectC.left;
      const top = rectF.top - rectC.top;
      w = Math.min(w, rectC.width - left);
      h = Math.min(h, rectC.height - top);
      activeField.style.width = w + 'px';
      activeField.style.height = h + 'px';
    }
  }

  function onMouseUp() {
    isDragging = false;
    isResizing = false;
    if (activeField) activeField.classList.remove('dragging');
    activeField = null;
    activeHandle = null;
    document.removeEventListener('mousemove', onMouseMove);
    document.removeEventListener('mouseup', onMouseUp);
  }

  document.addEventListener('mousedown', onMouseDown);

  // Contrôles de taille
  const nomEl = document.getElementById('nom');
  const niveauEl = document.getElementById('niveau');
  const periodeEl = document.getElementById('periode');

  const sizeNom = document.getElementById('sizeNom');
  const sizeNiveau = document.getElementById('sizeNiveau');
  const sizePeriode = document.getElementById('sizePeriode');
  const sizeNomValue = document.getElementById('sizeNomValue');
  const sizeNiveauValue = document.getElementById('sizeNiveauValue');
  const sizePeriodeValue = document.getElementById('sizePeriodeValue');

  function initSizes() {
    const csNom = parseInt(getComputedStyle(nomEl).fontSize) || 40;
    const csNiv = parseInt(getComputedStyle(niveauEl).fontSize) || 28;
    const csPer = parseInt(getComputedStyle(periodeEl).fontSize) || 28;
    sizeNom.value = Math.min(Math.max(csNom, +sizeNom.min), +sizeNom.max);
    sizeNiveau.value = Math.min(Math.max(csNiv, +sizeNiveau.min), +sizeNiveau.max);
    sizePeriode.value = Math.min(Math.max(csPer, +sizePeriode.min), +sizePeriode.max);
    sizeNomValue.textContent = sizeNom.value + 'px';
    sizeNiveauValue.textContent = sizeNiveau.value + 'px';
    sizePeriodeValue.textContent = sizePeriode.value + 'px';
  }
  initSizes();

  sizeNom.addEventListener('input', e => {
    nomEl.style.fontSize = e.target.value + 'px';
    sizeNomValue.textContent = e.target.value + 'px';
  });
  sizeNiveau.addEventListener('input', e => {
    niveauEl.style.fontSize = e.target.value + 'px';
    sizeNiveauValue.textContent = e.target.value + 'px';
  });
  sizePeriode.addEventListener('input', e => {
    periodeEl.style.fontSize = e.target.value + 'px';
    sizePeriodeValue.textContent = e.target.value + 'px';
  });

  // Ajustement à la molette sur le champ pointé
  function clamp(val, min, max){ return Math.min(Math.max(val, min), max); }
  fields.forEach(f => {
    f.addEventListener('wheel', e => {
      e.preventDefault();
      const delta = e.deltaY < 0 ? 1 : -1;
      const current = parseInt(getComputedStyle(f).fontSize) || 24;
      const bounds = f.id === 'nom' ? {min:16, max:72} : {min:12, max:48};
      const next = clamp(current + delta, bounds.min, bounds.max);
      f.style.fontSize = next + 'px';
      if (f.id === 'nom') { sizeNom.value = next; sizeNomValue.textContent = next + 'px'; }
      if (f.id === 'niveau') { sizeNiveau.value = next; sizeNiveauValue.textContent = next + 'px'; }
      if (f.id === 'periode') { sizePeriode.value = next; sizePeriodeValue.textContent = next + 'px'; }
    }, { passive: false });
  });

  // Téléchargement sans bordures/poignées
  document.getElementById('btnDownload').addEventListener('click', async () => {
    const toggleGuides = (show) => {
      fields.forEach(f => {
        f.style.border = show ? '2px dashed #22c55e' : 'none';
        f.style.background = show ? 'rgba(34, 197, 94, 0.08)' : 'transparent';
        f.querySelectorAll('.resize-handle').forEach(h => h.style.display = show ? '' : 'none');
      });
    };
    toggleGuides(false);
    const canvas = await html2canvas(container, { useCORS:true, allowTaint:true, backgroundColor:null, scale:2 });
    toggleGuides(true);
    const link = document.createElement('a');
    link.download = 'certificat_{{ $apprenant->prenom }}_{{ $apprenant->nom }}_{{ date('Y-m-d') }}.png';
    link.href = canvas.toDataURL('image/png', 1.0);
    link.click();
  });
</script>
@endsection



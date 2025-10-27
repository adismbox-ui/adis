@extends('admin.layout')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Ajouter un média</h1>
    <a href="{{ route('admin.galeries.index') }}" class="btn btn-secondary">Retour</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.galeries.store') }}" enctype="multipart/form-data" class="card p-4">
    @csrf
    <div class="mb-3">
      <label class="form-label">Titre</label>
      <input type="text" name="titre" class="form-control" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Type</label>
      <select name="type" id="type" class="form-select" required>
        <option value="photo">Photo</option>
        <option value="video">Vidéo</option>
      </select>
    </div>
    <div class="mb-3" id="photoField">
      <label class="form-label">Fichier photo</label>
      <input type="file" name="media" class="form-control" accept="image/*" />
    </div>
    <div class="mb-3" id="videoField" style="display:none;">
      <label class="form-label">Fichier vidéo</label>
      <input type="file" name="video" class="form-control" accept="video/*" />
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Enregistrer</button>
  </form>
</div>

@push('scripts')
<script>
document.getElementById('type').addEventListener('change', function() {
  const isPhoto = this.value === 'photo';
  document.getElementById('photoField').style.display = isPhoto ? 'block' : 'none';
  document.getElementById('videoField').style.display = isPhoto ? 'none' : 'block';
});
</script>
@endpush
@endsection


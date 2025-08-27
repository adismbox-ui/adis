@extends('admin.layout')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Ajouter une entreprise partenaire</h1>
    <a href="{{ route('admin.partenaires.index') }}" class="btn btn-secondary">Retour</a>
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

  <form method="POST" action="{{ route('admin.partenaires.store') }}" class="card p-4">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nom de l'entreprise</label>
        <input type="text" name="nom" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Téléphone</label>
        <input type="text" name="telephone" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Site web</label>
        <input type="text" name="site_web" class="form-control" />
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4"></textarea>
      </div>
    </div>
    <div class="mt-3">
      <button type="submit" class="btn btn-success">Enregistrer</button>
    </div>
  </form>
</div>
@endsection


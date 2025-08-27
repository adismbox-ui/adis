@extends('apprenants.layout')
@section('content')
<div class="container py-4">
    <a href="{{ url()->previous() }}" class="btn btn-outline-primary mb-3"><i class="fas fa-arrow-left me-2"></i>Retour</a>
    @parent
</div>
@endsection 
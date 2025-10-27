@extends('apprenants.layout')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Présence - Apprenant</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    @if($latestOpen)
        <div class="card">
            <div class="card-body">
                <div class="mb-2"><strong>Séance:</strong> {{ $latestOpen->nom ?? ('Séance #' . $latestOpen->id) }}</div>
                <form method="POST" action="{{ route('apprenants.presence.mark') }}">
                    @csrf
                    <input type="hidden" name="presence_request_id" value="{{ $latestOpen->id }}">
                    <button class="btn btn-success" type="submit">Je suis présent</button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">Aucune présence ouverte pour le moment.</div>
    @endif
</div>
@endsection


@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">Confirmation d'inscription</div>
                <div class="card-body text-center">
                    <h4 class="mb-4">Inscription validée !</h4>
                    <p>Vous avez reçu un mail de confirmation.<br>
                    Merci de vérifier votre boîte de réception (et le dossier spam si besoin).</p>
                    <a href="/" class="btn btn-success mt-3">Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
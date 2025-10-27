@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white text-center">
                    <h3>Inscription Formateur - En attente de validation</h3>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-hourglass-half fa-3x text-info mb-3"></i>
                    <p class="lead">Votre inscription a bien été prise en compte.</p>
                    <p>Un administrateur va examiner votre demande.<br>
                    Vous recevrez un email dès que votre compte sera validé.<br>
                    Merci de votre patience !</p>
                    <a href="/" class="btn btn-outline-info mt-3">Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
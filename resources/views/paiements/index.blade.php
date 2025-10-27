<h1>Liste des paiements</h1>
<a href="{{ route('paiements.create') }}">Créer un paiement</a>
<ul>
    @foreach($paiements as $paiement)
        <li>
            <a href="{{ route('paiements.show', $paiement) }}">
                Paiement #{{ $paiement->id }}
            </a>
        </li>
    @endforeach
</ul> 
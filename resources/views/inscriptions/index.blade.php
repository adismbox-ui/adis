<h1>Liste des inscriptions</h1>
<a href="{{ route('inscriptions.create') }}">Créer une inscription</a>
<ul>
    @foreach($inscriptions as $inscription)
        <li>
            <a href="{{ route('inscriptions.show', $inscription) }}">
                Inscription #{{ $inscription->id }}
            </a>
        </li>
    @endforeach
</ul> 
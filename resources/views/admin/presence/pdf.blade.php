<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        h2 { font-size: 16px; margin: 16px 0 6px; }
        .meta { color: #666; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
        .section { margin-top: 14px; }
    </style>
    <title>Présence</title>
    </head>
<body>
    <h1>Feuille de présence</h1>
    <div class="meta">
        Séance: <strong>{{ $presence->nom ?? ('Séance #'.$presence->id) }}</strong><br>
        Formateur: <strong>{{ optional($presence->formateur)->utilisateur->nom ?? 'N/A' }} {{ optional($presence->formateur)->utilisateur->prenom ?? '' }}</strong><br>
        Date: <strong>{{ $presence->created_at->format('d/m/Y') }}</strong>
    </div>

    <div class="section">
        <h2>Étudiants présents</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Heure</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presents as $i => $apprenant)
                @php($nom = trim(($apprenant->nom ?? '').' '.($apprenant->prenom ?? '')))
                @if($nom === '' && $apprenant->utilisateur)
                    @php($nom = trim(($apprenant->utilisateur->nom ?? '').' '.($apprenant->utilisateur->prenom ?? '')))
                @endif
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $nom !== '' ? $nom : 'Nom indisponible' }}</td>
                    <td>{{ optional($presence->marks->firstWhere('apprenant_id', $apprenant->id))->present_at?->format('H:i') }}</td>
                    <td></td>
                </tr>
                @empty
                <tr><td colspan="4">Aucun étudiant présent.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Étudiants absents</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absents as $i => $apprenant)
                @php($nomA = trim(($apprenant->nom ?? '').' '.($apprenant->prenom ?? '')))
                @if($nomA === '' && $apprenant->utilisateur)
                    @php($nomA = trim(($apprenant->utilisateur->nom ?? '').' '.($apprenant->utilisateur->prenom ?? '')))
                @endif
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $nomA !== '' ? $nomA : 'Nom indisponible' }}</td>
                    <td></td>
                </tr>
                @empty
                <tr><td colspan="3">Aucun étudiant absent.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="meta" style="margin-top:12px;">
        Généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>
</body>
</html>


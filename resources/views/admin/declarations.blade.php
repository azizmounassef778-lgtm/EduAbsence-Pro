@extends('layouts.app')

@section('title', 'Déclarations d\'Absence Enseignants')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Déclarations d'Absence des Enseignants</h1>
        <p style="color: var(--text-muted);">Tâche 17 : Notifications automatiques et historique des signalements d'absence des formateurs</p>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Signalement</th>
                    <th>Enseignant</th>
                    <th>Date d'Absence</th>
                    <th>Séance</th>
                    <th>Groupe Impacté</th>
                    <th>Motif Déclaré</th>
                    <th>Modalités de Rattrapage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($declarations as $d)
                <tr>
                    <td>{{ $d->date_declaration->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $d->enseignant->name }}</strong>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">{{ $d->enseignant->email }}</div>
                    </td>
                    <td><strong style="color: var(--primary);">{{ $d->date_absence->format('d/m/Y') }}</strong></td>
                    <td><span class="badge badge-primary">{{ $d->seance->code_seance }} ({{ $d->seance->libelle }})</span></td>
                    <td><span class="badge badge-info">{{ $d->groupe->code_groupe }}</span></td>
                    <td>{{ $d->motif }}</td>
                    <td>
                        <span style="font-size: 0.85rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.35rem;">
                            <x-icon name="history" size="14" /> {{ $d->remarques_rattrapage ?? 'Non spécifié' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 2rem;">
                        Aucune déclaration d'absence reçue.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $declarations->links() }}
    </div>
</div>
@endsection

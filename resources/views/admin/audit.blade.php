@extends('layouts.app')

@section('title', 'Journal d\'Audit des Corrections')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Journal d'Audit des Corrections</h1>
        <p style="color: var(--text-muted);">Tâche 22 : Traçabilité complète des rectifications et suppressions d'absences saisies par erreur</p>
    </div>
    <a href="{{ route('admin.absences') }}" class="btn btn-secondary btn-sm">⬅️ Retour aux Absences</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Correction</th>
                    <th>Administrateur</th>
                    <th>ID Absence</th>
                    <th>Type d'Opération</th>
                    <th>Anciennes Valeurs</th>
                    <th>Nouvelles Valeurs</th>
                    <th>Motif de la Correction</th>
                </tr>
            </thead>
            <tbody>
                @forelse($corrections as $c)
                <tr>
                    <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $c->admin->name }}</strong>
                    </td>
                    <td><span class="badge badge-secondary">#{{ $c->absence_id ?? 'N/A' }}</span></td>
                    <td>
                        <span class="badge {{ str_contains($c->champ_modifie, 'ANNULATION') ? 'badge-danger' : 'badge-warning' }}">
                            {{ $c->champ_modifie }}
                        </span>
                    </td>
                    <td style="font-size: 0.8rem; color: var(--text-muted); max-width: 200px;">{{ $c->ancienne_valeur }}</td>
                    <td style="font-size: 0.8rem; font-weight: 600; color: var(--text-main); max-width: 200px;">{{ $c->nouvelle_valeur }}</td>
                    <td style="font-size: 0.85rem;">
                        <em>"{{ $c->motif_correction }}"</em>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 2rem;">
                        Aucune correction enregistrée dans le journal d'audit.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $corrections->links() }}
    </div>
</div>
@endsection

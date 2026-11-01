@extends('layouts.app')

@section('title', 'Consultation des Stagiaires')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Consultation des Stagiaires</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Accès aux listes des stagiaires des groupes qui vous sont spécifiquement attribués</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('enseignant.declarer-comportement') }}" class="btn btn-secondary btn-sm">
            <x-icon name="shield-alert" size="14" /> Signaler Comportement
        </a>
        <a href="{{ route('enseignant.saisir-absence') }}" class="btn btn-primary btn-sm">
            <x-icon name="user-x" size="14" /> Pointer une Absence
        </a>
    </div>
</div>

<!-- Filtre rapide par groupe attribué -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form action="{{ route('enseignant.stagiaires') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin-bottom: 0; min-width: 240px;">
            <label class="form-label">Filtrer par Groupe Attribué</label>
            <select name="groupe_id" class="form-select" onchange="this.form.submit()">
                <option value="">Tous mes groupes</option>
                @foreach($groupesAttribués as $eg)
                    <option value="{{ $eg->groupe_id }}" {{ request('groupe_id') == $eg->groupe_id ? 'selected' : '' }}>
                        {{ $eg->groupe->code_groupe }} - {{ $eg->matiere }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label class="form-label">Recherche par Nom ou CNE</label>
            <input type="text" name="search" class="form-control" placeholder="Rechercher un stagiaire..." value="{{ request('search') }}">
        </div>

        <button type="submit" class="btn btn-secondary btn-sm" style="height: 42px;">Filtrer</button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>CNE</th>
                    <th>Groupe</th>
                    <th>Email</th>
                    <th>Note de Discipline (sur 20)</th>
                    <th>Total Absences</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stagiaires as $st)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="{{ $st->user->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($st->user->name) }}" class="user-avatar" style="width: 32px; height: 32px;">
                            <div>
                                <strong>{{ $st->user->name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-info" style="font-family: var(--font-mono);">{{ $st->matricule_cne }}</span></td>
                    <td><span class="badge badge-primary">{{ $st->groupe->code_groupe }}</span></td>
                    <td style="font-size: 0.8rem; font-family: var(--font-mono);">{{ $st->user->email }}</td>
                    <td>
                        <strong style="color: {{ $st->note_discipline >= 16.0 ? 'var(--ofppt-green)' : ($st->note_discipline >= 12.0 ? 'var(--info)' : 'var(--danger)') }};">
                            {{ number_format($st->note_discipline, 2) }} / 20.00
                        </strong>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $st->absences->count() }}</span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.35rem;">
                            <a href="{{ route('enseignant.saisir-absence', ['preselect_stagiaire' => $st->id, 'preselect_groupe' => $st->groupe_id]) }}" class="btn btn-primary btn-sm" title="Déclarer absent">
                                <x-icon name="user-x" size="14" /> Absence
                            </a>
                            <a href="{{ route('enseignant.declarer-comportement') }}" class="btn btn-secondary btn-sm" title="Signaler un comportement">
                                <x-icon name="shield-alert" size="14" />
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 2rem;">
                        Aucun stagiaire trouvé pour ce critère.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $stagiaires->links() }}
    </div>
</div>
@endsection

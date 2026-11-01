@extends('layouts.app')

@section('title', 'Espace Enseignant')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
            <x-icon name="dashboard" size="26" class="text-primary" />
            Espace Formateur / Enseignant
        </h1>
        <p style="color: var(--text-muted);">Bienvenue, M./Mme {{ auth()->user()->name }}. Saisie des absences, signalements de comportement et déclarations.</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('enseignant.saisir-absence') }}" class="btn btn-primary btn-sm">
            <x-icon name="user-x" size="16" />
            Pointer une Absence
        </a>
        <a href="{{ route('enseignant.declarer-comportement') }}" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0b4f8a 100%);">
            <x-icon name="shield-alert" size="16" />
            Signaler Comportement
        </a>
        <a href="{{ route('enseignant.declarer-absence') }}" class="btn btn-secondary btn-sm">
            <x-icon name="alert-triangle" size="16" />
            Déclarer mon Absence
        </a>
    </div>
</div>

<div class="grid-stats">
    <div class="card stat-card">
        <div class="stat-icon primary">
            <x-icon name="tag" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $groupesAttribués->count() }}</span>
            <span class="stat-label">Groupes Assignés</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon success">
            <x-icon name="users" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $totalStagiaires }}</span>
            <span class="stat-label">Stagiaires Suivis</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon danger">
            <x-icon name="clock" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $mesAbsencesSaisies }}</span>
            <span class="stat-label">Absences Saisies</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon info">
            <x-icon name="bell" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $mesDeclarations }}</span>
            <span class="stat-label">Mes Déclarations</span>
        </div>
    </div>
</div>

<!-- Mes Groupes Attribués (Tâche 9) -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-title">
        <span style="display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="tag" size="18" />
            Mes Groupes & Modules Attribués
        </span>
        <a href="{{ route('enseignant.stagiaires') }}" class="badge badge-info">Voir listes complètes</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        @foreach($groupesAttribués as $eg)
        <div style="background: var(--bg-tertiary); padding: 1rem; border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                <span class="badge badge-primary">{{ $eg->groupe->code_groupe }}</span>
                <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $eg->groupe->stagiaires->count() }} stagiaires</span>
            </div>
            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">{{ $eg->groupe->nom_groupe }}</h4>
            <div style="font-size: 0.85rem; color: var(--accent-cyan); display: flex; align-items: center; gap: 0.4rem;">
                <x-icon name="file-text" size="14" />
                Module : <strong>{{ $eg->matiere }}</strong>
            </div>
            <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
                <a href="{{ route('enseignant.stagiaires', ['groupe_id' => $eg->groupe_id]) }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                    Stagiaires
                </a>
                <a href="{{ route('enseignant.declarer-comportement') }}" class="btn btn-secondary btn-sm" title="Signaler un comportement">
                    <x-icon name="shield-alert" size="14" />
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Dernières Absences Enregistrées par ce formateur -->
<div class="card">
    <div class="card-title">
        <span style="display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="calendar" size="18" />
            Dernières Absences que vous avez Saisies
        </span>
        <a href="{{ route('enseignant.saisir-absence') }}" class="btn btn-primary btn-sm">
            <x-icon name="plus" size="14" />
            Nouvelle Saisie
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Séance</th>
                    <th>Stagiaire</th>
                    <th>Groupe</th>
                    <th>Statut Justificatif</th>
                    <th>Motif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentesAbsences as $abs)
                <tr>
                    <td>{{ $abs->date_absence->format('d/m/Y') }}</td>
                    <td><span class="badge badge-primary">{{ $abs->seance->code_seance }}</span></td>
                    <td>
                        <strong>{{ $abs->stagiaire->user->name }}</strong>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">CNE: {{ $abs->stagiaire->matricule_cne }}</div>
                    </td>
                    <td><span class="badge badge-info">{{ $abs->stagiaire->groupe->code_groupe }}</span></td>
                    <td>
                        @if($abs->statut_absence === 'JUSTIFIEE_ACCEPTEE')
                            <span class="badge badge-success">Validée par Admin</span>
                        @elseif($abs->statut_absence === 'EN_ATTENTE_VALIDATION')
                            <span class="badge badge-warning">Certificat en attente</span>
                        @elseif($abs->statut_absence === 'JUSTIFIEE_REFUSEE')
                            <span class="badge badge-danger">Justificatif refusé</span>
                        @else
                            <span class="badge badge-danger">Non justifiée</span>
                        @endif
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $abs->motif_enseignant }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-dim); padding: 2rem;">
                        Vous n'avez pas encore enregistré d'absence récemment.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

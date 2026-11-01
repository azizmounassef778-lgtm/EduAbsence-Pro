@extends('layouts.app')

@section('title', 'Tableau de Bord Administrateur')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.5px; display: flex; align-items: center; gap: 0.6rem;">
            <x-icon name="dashboard" size="26" class="text-primary" />
            Espace Administrateur
        </h1>
        <p style="color: var(--text-muted);">Vue globale du suivi de l'assiduité, conseils de discipline, et alertes formateurs</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('admin.conseils.index') }}" class="btn btn-primary btn-sm">
            <x-icon name="scale" size="16" />
            Conseils Discipline ({{ $conseilsConvoques }})
        </a>
        <a href="{{ route('admin.absences') }}" class="btn btn-secondary btn-sm">
            <x-icon name="search" size="16" />
            Recherche multi-critères
        </a>
        <a href="{{ route('admin.justificatifs') }}" class="btn btn-secondary btn-sm">
            <x-icon name="file-text" size="16" />
            Justificatifs ({{ $certificatsEnAttente }})
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid-stats">
    <div class="card stat-card">
        <div class="stat-icon primary">
            <x-icon name="graduation-cap" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $totalStagiaires }}</span>
            <span class="stat-label">Stagiaires Inscrits</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon danger">
            <x-icon name="clock" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $totalAbsences }}</span>
            <span class="stat-label">Total Absences</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon warning">
            <x-icon name="scale" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $totalConseils }}</span>
            <span class="stat-label">Conseils Discipline (-5 pts)</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon info">
            <x-icon name="shield-alert" size="24" />
        </div>
        <div class="stat-content">
            <span class="stat-value">{{ $signalementsEnAttente }}</span>
            <span class="stat-label">Signalements Comportement</span>
        </div>
    </div>
</div>

<!-- Alertes Signalements Comportement & Conseils de Discipline -->
@if($derniersSignalements->count() > 0 || $derniersConseils->count() > 0)
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Signalements Comportement par les Formateurs -->
    <div class="card" style="border-left: 4px solid var(--accent-cyan);">
        <div class="card-title">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="shield-alert" size="18" class="text-cyan" />
                Signalements Comportement Formateurs
            </span>
            <a href="{{ route('admin.conseils.index') }}" class="badge badge-info">Gérer</a>
        </div>
        @if($derniersSignalements->isEmpty())
            <p style="color: var(--text-dim); font-size: 0.85rem;">Aucun signalement en attente.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($derniersSignalements as $sig)
                    <div style="background: var(--bg-tertiary); padding: 0.75rem; border-radius: var(--radius-md); border-left: 3px solid {{ $sig->gravite === 'GRAVE' ? 'var(--danger)' : 'var(--warning)' }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <strong>{{ $sig->stagiaire->user->name ?? 'Stagiaire' }}</strong>
                            <span class="badge {{ $sig->gravite === 'GRAVE' ? 'badge-danger' : 'badge-warning' }}">{{ $sig->gravite }}</span>
                        </div>
                        <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                            Signalé par <strong>{{ $sig->enseignant->name ?? 'Formateur' }}</strong> : "{{ $sig->motif }}"
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-dim);">
                            <span>{{ $sig->date_incident->format('d/m/Y') }}</span>
                            <a href="{{ route('admin.conseils.index') }}" class="btn btn-sm btn-primary" style="padding: 0.15rem 0.45rem; font-size: 0.7rem;">
                                Convoquer Conseil (-5 pts)
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Conseils de Discipline Récents -->
    <div class="card" style="border-left: 4px solid var(--danger);">
        <div class="card-title">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="scale" size="18" />
                Derniers Conseils de Discipline
            </span>
            <a href="{{ route('admin.conseils.index') }}" class="badge badge-danger">Voir Registre</a>
        </div>
        @if($derniersConseils->isEmpty())
            <p style="color: var(--text-dim); font-size: 0.85rem;">Aucun conseil de discipline programmé.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($derniersConseils as $cd)
                    <div style="background: var(--bg-tertiary); padding: 0.75rem; border-radius: var(--radius-md); border-left: 3px solid var(--danger);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <strong>{{ $cd->stagiaire->user->name ?? 'Stagiaire' }}</strong>
                            <span class="penalty-pill">-5.00 pts</span>
                        </div>
                        <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                            {{ $cd->motif }}
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-dim);">
                            <span>Convocation : {{ $cd->date_conseil->format('d/m/Y') }}</span>
                            <span class="badge {{ $cd->statut === 'CONVOQUE' ? 'badge-warning' : 'badge-info' }}">{{ $cd->statut }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Alertes d'absences des formateurs (Tâche 17) -->
    <div class="card">
        <div class="card-title">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="bell" size="18" />
                Alertes d'Absences Enseignants
            </span>
            <a href="{{ route('admin.declarations') }}" class="badge badge-info">Voir tout</a>
        </div>
        @if($dernieresDeclarations->isEmpty())
            <p style="color: var(--text-dim); font-size: 0.9rem;">Aucune déclaration récente de formateur.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($dernieresDeclarations as $decl)
                    <div style="background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-md); border-left: 4px solid var(--info);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                            <strong style="color: var(--text-main);">{{ $decl->enseignant->name }}</strong>
                            <span class="badge badge-info">{{ $decl->date_absence->format('d/m/Y') }} ({{ $decl->seance->code_seance }})</span>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.3rem;">
                            <strong>Groupe :</strong> {{ $decl->groupe->code_groupe }} • <strong>Motif :</strong> {{ $decl->motif }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-dim); display: flex; align-items: center; gap: 0.3rem;">
                            <x-icon name="history" size="14" />
                            <strong>Rattrapage :</strong> {{ $decl->remarques_rattrapage ?? 'Non spécifié' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Justificatifs en attente de décision (Tâche 15) -->
    <div class="card">
        <div class="card-title">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="clock" size="18" />
                Certificats en Attente de Décision
            </span>
            <a href="{{ route('admin.justificatifs') }}" class="badge badge-warning">Examen ({{ $justificatifsAValider->count() }})</a>
        </div>
        @if($justificatifsAValider->isEmpty())
            <p style="color: var(--text-dim); font-size: 0.9rem;">Tous les justificatifs ont été traités.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($justificatifsAValider as $justif)
                    <div style="background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <strong>{{ $justif->absence->stagiaire->user->name }}</strong>
                            <span class="badge badge-warning">En attente</span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                            Absence du {{ $justif->absence->date_absence->format('d/m/Y') }} ({{ $justif->absence->seance->code_seance }}) • Fichier : <code>{{ $justif->nom_fichier }}</code>
                        </div>
                        <form action="{{ route('admin.justificatifs.valider', $justif->id) }}" method="POST" style="display: flex; gap: 0.5rem;">
                            @csrf
                            <input type="text" name="motif_decision" placeholder="Motif ou remarque..." class="form-control" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                            <button type="submit" name="decision" value="ACCEPTE" class="btn btn-success btn-sm">Accepter</button>
                            <button type="submit" name="decision" value="REFUSE" class="btn btn-danger btn-sm">Refuser</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Dernières Absences Enregistrées -->
<div class="card">
    <div class="card-title">
        <span style="display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="calendar" size="18" />
            Dernières Absences Enregistrées
        </span>
        <a href="{{ route('admin.absences') }}" class="btn btn-secondary btn-sm">Toutes les absences</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Séance</th>
                    <th>Stagiaire</th>
                    <th>Groupe</th>
                    <th>Enseignant</th>
                    <th>Statut</th>
                    <th>Justificatif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dernieresAbsences as $abs)
                <tr>
                    <td>{{ $abs->date_absence->format('d/m/Y') }}</td>
                    <td><span class="badge badge-primary">{{ $abs->seance->code_seance }}</span></td>
                    <td>
                        <strong>{{ $abs->stagiaire->user->name }}</strong>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">CNE: {{ $abs->stagiaire->matricule_cne }}</div>
                    </td>
                    <td>{{ $abs->stagiaire->groupe->code_groupe }}</td>
                    <td>{{ $abs->enseignant->name }}</td>
                    <td>
                        @if($abs->statut_absence === 'JUSTIFIEE_ACCEPTEE')
                            <span class="badge badge-success">Validée</span>
                        @elseif($abs->statut_absence === 'EN_ATTENTE_VALIDATION')
                            <span class="badge badge-warning">En attente</span>
                        @elseif($abs->statut_absence === 'JUSTIFIEE_REFUSEE')
                            <span class="badge badge-danger">Refusée</span>
                        @else
                            <span class="badge badge-danger">Non justifiée</span>
                        @endif
                    </td>
                    <td>
                        @if($abs->justificatif)
                            <a href="{{ asset($abs->justificatif->chemin_fichier) }}" target="_blank" class="badge badge-info" title="Consulter le fichier" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                <x-icon name="file-text" size="12" />
                                {{ Str::limit($abs->justificatif->nom_fichier, 16) }}
                            </a>
                        @else
                            <span style="color: var(--text-dim); font-size: 0.8rem;">Aucun</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.absences', ['search_stagiaire' => $abs->stagiaire->matricule_cne]) }}" class="btn btn-secondary btn-sm">Détails</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

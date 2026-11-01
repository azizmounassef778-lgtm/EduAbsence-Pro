@extends('layouts.app')

@section('title', 'Espace Stagiaire')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
            <x-icon name="dashboard" size="26" class="text-primary" />
            Espace Stagiaire
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Bienvenue, <strong>{{ auth()->user()->name }}</strong> (CNE : <code>{{ $stagiaire->matricule_cne }}</code> • Groupe : <span class="badge badge-primary">{{ $stagiaire->groupe->code_groupe }}</span>)
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('stagiaire.absences') }}" class="btn btn-primary btn-sm">
            <x-icon name="clock" size="16" />
            Mes Absences & Dépôt
        </a>
        <a href="{{ route('stagiaire.note-discipline') }}" class="btn btn-secondary btn-sm">
            <x-icon name="award" size="16" />
            Détails Note de Discipline
        </a>
    </div>
</div>

<!-- Alerte Seuil 15h d'absences non justifiées -->
@php
    $heuresNonJustif = $statsDiscipline['totalHeuresNonJustifiees'] ?? 0;
    $pourcentage15h = min(100, round(($heuresNonJustif / 15.0) * 100));
@endphp

@if($heuresNonJustif >= 15.0)
<div class="alert alert-danger" style="margin-bottom: 1.5rem;">
    <x-icon name="alert-triangle" size="22" />
    <div>
        <strong>Seuil critique de 15h d'absences non justifiées atteint ({{ number_format($heuresNonJustif, 1) }}h) !</strong><br>
        Conformément au règlement, un <strong>Conseil de Discipline</strong> a été automatiquement convoqué à votre nom. Une pénalité de <strong>-5.00 points</strong> a été déduite de votre note de comportement.
    </div>
</div>
@elseif($heuresNonJustif >= 10.0)
<div class="alert alert-danger" style="margin-bottom: 1.5rem; background: rgba(217, 119, 6, 0.15); border-color: rgba(217, 119, 6, 0.4); color: #f59e0b;">
    <x-icon name="alert-triangle" size="22" />
    <div>
        <strong>Attention : Vigilance assiduité !</strong> Vous cumulez <strong>{{ number_format($heuresNonJustif, 1) }}h / 15.0h</strong> d'absences sans justification. À 15h, vous serez automatiquement convoqué en conseil de discipline avec <strong>-5 points</strong> de pénalité ferme.
    </div>
</div>
@endif

<!-- CARTE NOTE GLOBALE DE COMPORTEMENT (SUR 20) -->
<div class="card discipline-card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <h2 style="font-size: 1.4rem; font-weight: 800; margin-top: 0.2rem; display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="award" size="22" />
                Note Globale de Comportement
            </h2>
            <p style="color: var(--text-muted); font-size: 0.82rem;">Barème : 20.00 / 20 points initiaux (-0,5 pt par absence non justifiée • -5 pts par conseil de discipline)</p>
        </div>
        <span class="badge {{ $statsDiscipline['badgeClass'] }}" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">
            {{ $statsDiscipline['appreciation'] }}
        </span>
    </div>

    <div class="score-display">
        <!-- Boîte du Score Numérique -->
        <div class="score-number-box">
            <div class="score-value">{{ number_format($statsDiscipline['note'], 2) }}</div>
            <div class="score-max">sur 20 points</div>
        </div>

        <!-- Détails et Barre de Progression -->
        <div class="score-details">
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700;">
                <span style="color: var(--text-muted);">Assiduité globale</span>
                <span style="color: var(--accent-green);">{{ $statsDiscipline['pourcentage'] }}%</span>
            </div>

            <div class="score-bar-container">
                <div class="score-bar-fill" style="width: {{ $statsDiscipline['pourcentage'] }}%;"></div>
            </div>

            <!-- Décomposition analytique de la note -->
            <div class="score-breakdown">
                <div class="breakdown-item">
                    <div class="breakdown-val" style="color: var(--text-main);">20.00</div>
                    <div class="breakdown-label">Note Initiale</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-val" style="color: var(--success);">{{ $statsDiscipline['nbJustifieesAcceptees'] }}</div>
                    <div class="breakdown-label">Justifiées (0 pt perdu)</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-val" style="color: var(--danger);">-{{ $statsDiscipline['penaliteAbsences'] }} pt</div>
                    <div class="breakdown-label">Pénalité Absences</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-val" style="color: var(--danger);">-{{ $statsDiscipline['penaliteConseils'] }} pt</div>
                    <div class="breakdown-label">Conseils ({{ $statsDiscipline['nbConseilsDiscipline'] }} x -5 pts)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conseils de Discipline convoqués -->
@if($conseils->count() > 0)
<div class="card" style="margin-bottom: 2rem; border-left: 4px solid var(--danger);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; color: var(--danger);">
            <x-icon name="scale" size="20" />
            Conseil(s) de Discipline Convoqué(s) ({{ $conseils->count() }})
        </h2>
        <span class="penalty-pill">-{{ number_format($conseils->sum('reduction_points'), 1) }} pts déduits</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Type de Convocation</th>
                    <th>Motif</th>
                    <th>Date Prévue</th>
                    <th>Impact Note</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conseils as $c)
                <tr>
                    <td>
                        @if($c->type === 'ABSENCE_15H')
                            <span class="badge-conseil badge-conseil-auto">
                                <x-icon name="alert-triangle" size="12" />
                                Automatique (15h d'absences)
                            </span>
                        @else
                            <span class="badge-conseil badge-conseil-manuel">
                                <x-icon name="shield-alert" size="12" />
                                {{ $c->type }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $c->motif }}</div>
                        @if($c->description)
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">{{ $c->description }}</div>
                        @endif
                    </td>
                    <td style="font-size: 0.85rem; white-space: nowrap;">
                        <x-icon name="calendar" size="14" />
                        {{ $c->date_conseil->format('d/m/Y') }}
                    </td>
                    <td>
                        <span class="penalty-pill">-5.00 pts</span>
                    </td>
                    <td>
                        <span class="badge badge-warning">{{ $c->statut }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Mes Absences Récentes et Compte à Rebours 48h -->
    <div class="card">
        <div class="card-title">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="clock" size="18" />
                Mes Absences Récentes & Délais de Dépôt (48h)
            </span>
            <a href="{{ route('stagiaire.absences') }}" class="btn btn-secondary btn-sm">Voir tout & Déposer</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date & Séance</th>
                        <th>Formateur</th>
                        <th>Statut Justificatif</th>
                        <th>Délai Restant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absencesWithDelai as $abs)
                    <tr>
                        <td>
                            <strong>{{ $abs->date_absence->format('d/m/Y') }}</strong>
                            <div><span class="badge badge-primary">{{ $abs->seance->code_seance }} ({{ $abs->seance->libelle }})</span></div>
                        </td>
                        <td style="font-size: 0.85rem;">{{ $abs->enseignant->name }}</td>
                        <td>
                            @if($abs->statut_absence === 'JUSTIFIEE_ACCEPTEE')
                                <span class="badge badge-success">
                                    <x-icon name="check-circle" size="12" /> Accepté (0 pt perdu)
                                </span>
                            @elseif($abs->statut_absence === 'EN_ATTENTE_VALIDATION')
                                <span class="badge badge-warning">
                                    <x-icon name="clock" size="12" /> En examen admin
                                </span>
                            @elseif($abs->statut_absence === 'JUSTIFIEE_REFUSEE')
                                <span class="badge badge-danger">
                                    <x-icon name="x-circle" size="12" /> Refusé (-0.5 pt)
                                </span>
                            @else
                                <span class="badge badge-danger">Non justifiée</span>
                            @endif
                        </td>
                        <td>
                            @if($abs->justificatif)
                                <span class="badge badge-info">Document déposé</span>
                            @else
                                <span class="badge {{ $abs->delaiInfo['badge_class'] }}">
                                    {{ $abs->delaiInfo['texte'] }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('stagiaire.absences') }}" class="btn btn-secondary btn-sm">
                                <x-icon name="file-text" size="14" />
                                Gérer
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-dim); padding: 2rem;">
                            Félicitations ! Aucune absence enregistrée à votre nom.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notifications -->
    <div class="card">
        <div class="card-title">
            <span style="display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="bell" size="18" />
                Mes Notifications
            </span>
        </div>

        @if($notifications->isEmpty())
            <p style="color: var(--text-dim); font-size: 0.85rem;">Aucune nouvelle notification.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($notifications as $notif)
                    <div style="background: var(--bg-tertiary); padding: 0.75rem; border-radius: var(--radius-md); border-left: 3px solid {{ $notif->est_lue ? 'var(--text-dim)' : 'var(--ofppt-blue)' }};">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.25rem;">
                            <strong style="font-size: 0.85rem; color: var(--text-main);">{{ $notif->titre }}</strong>
                            @if(!$notif->est_lue)
                                <span class="badge badge-danger" style="font-size: 0.65rem;">Nouveau</span>
                            @endif
                        </div>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.4rem;">
                            {{ $notif->message }}
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.7rem; color: var(--text-dim);">
                            <span>{{ $notif->created_at->diffForHumans() }}</span>
                            @if(!$notif->est_lue)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">Marquer lu</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

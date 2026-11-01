@extends('layouts.app')

@section('title', 'Détails Note de Discipline (20/20)')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
            <x-icon name="award" size="26" class="text-primary" />
            Note de Discipline & Assiduité
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Barème officiel de 20 / 20 points, suivi automatique des déductions et conseils de discipline.
        </p>
    </div>
    <a href="{{ route('stagiaire.absences') }}" class="btn btn-secondary btn-sm">
        <x-icon name="clock" size="16" />
        Voir mes Absences
    </a>
</div>

<!-- Score Card sur 20 -->
<div class="card discipline-card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <h2 style="font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="award" size="22" />
                Note Actuelle de Discipline
            </h2>
            <p style="color: var(--text-muted); font-size: 0.82rem;">Calculée en temps réel selon le barème officiel de l'établissement</p>
        </div>
        <span class="badge {{ $statsDiscipline['badgeClass'] }}" style="font-size: 0.9rem; padding: 0.4rem 0.85rem;">
            {{ $statsDiscipline['appreciation'] }}
        </span>
    </div>

    <div class="score-display">
        <div class="score-number-box">
            <div class="score-value">{{ number_format($statsDiscipline['note'], 2) }}</div>
            <div class="score-max">sur 20.00 points</div>
        </div>

        <div class="score-details">
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700;">
                <span style="color: var(--text-muted);">Taux d'assiduité</span>
                <span style="color: var(--accent-green);">{{ $statsDiscipline['pourcentage'] }}%</span>
            </div>

            <div class="score-bar-container" style="height: 16px;">
                <div class="score-bar-fill" style="width: {{ $statsDiscipline['pourcentage'] }}%;"></div>
            </div>

            <div class="score-breakdown">
                <div class="breakdown-item">
                    <div class="breakdown-val">20.00</div>
                    <div class="breakdown-label">Note Initiale</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-val" style="color: var(--danger);">-{{ $statsDiscipline['penaliteAbsences'] }} pt</div>
                    <div class="breakdown-label">Absences (-0.5 pt)</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-val" style="color: var(--danger);">-{{ $statsDiscipline['penaliteConseils'] }} pt</div>
                    <div class="breakdown-label">Conseils ({{ $statsDiscipline['nbConseilsDiscipline'] }} x -5 pts)</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-val" style="color: var(--success);">{{ $statsDiscipline['nbJustifieesAcceptees'] }}</div>
                    <div class="breakdown-label">Absences Justifiées</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Suivi du Seuil Critique de 15h d'absences non justifiées -->
@php
    $heuresNonJustif = $statsDiscipline['totalHeuresNonJustifiees'] ?? 0;
    $pourcentage15h = min(100, round(($heuresNonJustif / 15.0) * 100));
@endphp
<div class="card" style="margin-bottom: 2rem; border-left: 4px solid {{ $heuresNonJustif >= 15.0 ? 'var(--danger)' : ($heuresNonJustif >= 10.0 ? 'var(--warning)' : 'var(--ofppt-blue)') }};">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <h3 style="font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                <x-icon name="clock" size="20" />
                Seuil Réglementaire : 15 Heures d'Absences sans Justification
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                Règle : Tout stagiaire accumulant <strong>15 heures</strong> d'absences sans justification valable est automatiquement convoqué en <strong>Conseil de Discipline</strong> et écope d'un retrait automatique de <strong>-5 points</strong> sur sa note.
            </p>
        </div>
        <div style="font-size: 1.3rem; font-weight: 800; color: {{ $heuresNonJustif >= 15.0 ? 'var(--danger)' : ($heuresNonJustif >= 10.0 ? 'var(--warning)' : 'var(--accent-green)') }};">
            {{ number_format($heuresNonJustif, 1) }}h / 15.0h
        </div>
    </div>

    <div class="score-bar-container" style="height: 12px; margin-bottom: 0.5rem; background: var(--bg-tertiary);">
        <div class="score-bar-fill" style="width: {{ $pourcentage15h }}%; background: {{ $heuresNonJustif >= 15.0 ? 'linear-gradient(90deg, #f59e0b, #ef4444)' : 'linear-gradient(90deg, #0b4f8a, #00875a)' }};"></div>
    </div>

    @if($heuresNonJustif >= 15.0)
        <div style="font-size: 0.85rem; color: #f87171; font-weight: 700; display: flex; align-items: center; gap: 0.4rem;">
            <x-icon name="alert-triangle" size="16" />
            Seuil de 15h atteint. Conseil de discipline automatique convoqué (-5 points appliqués).
        </div>
    @elseif($heuresNonJustif >= 10.0)
        <div style="font-size: 0.85rem; color: #fbbf24; font-weight: 700; display: flex; align-items: center; gap: 0.4rem;">
            <x-icon name="alert-triangle" size="16" />
            Vigilance : Plus que {{ number_format(15.0 - $heuresNonJustif, 1) }}h avant convocation automatique en conseil de discipline.
        </div>
    @else
        <div style="font-size: 0.85rem; color: var(--text-dim); display: flex; align-items: center; gap: 0.4rem;">
            <x-icon name="check-circle" size="16" class="text-success" />
            Situation normale : Vous disposez d'une marge de sécurité de {{ number_format(15.0 - $heuresNonJustif, 1) }}h.
        </div>
    @endif
</div>

<!-- Registre des Conseils de Discipline pour ce stagiaire -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-title">
        <span style="display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="scale" size="20" />
            Conseils de Discipline du Stagiaire ({{ $conseils->count() }})
        </span>
    </div>

    @if($conseils->count() > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Réf</th>
                    <th>Type de Conseil</th>
                    <th>Motif & Explication</th>
                    <th>Date Prévue</th>
                    <th>Pénalité Appliquée</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conseils as $c)
                <tr>
                    <td style="font-family: var(--font-mono); font-weight: 700;">#CD-{{ $c->id }}</td>
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
    @else
    <div style="text-align: center; padding: 2rem 1rem; color: var(--text-dim);">
        <x-icon name="check-circle" size="38" style="color: var(--accent-green); margin-bottom: 0.5rem;" />
        <div style="font-weight: 700; color: var(--text-main);">Dossier disciplinaire exemplaire</div>
        <div style="font-size: 0.85rem; margin-top: 0.2rem;">Aucun conseil de discipline ni avertissement de comportement à votre encontre.</div>
    </div>
    @endif
</div>

<!-- Rappel du Règlement Officiel -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-title">
        <span style="display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="file-text" size="18" />
            Barème et Règles de Calcul
        </span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
        <div style="background: var(--bg-tertiary); padding: 1rem; border-radius: var(--radius-md); border-left: 3px solid var(--ofppt-blue);">
            <strong style="color: #38bdf8;">1. Note Initiale</strong>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                Chaque apprenant débute son année avec la note maximale de <strong>20,00 points</strong>.
            </p>
        </div>

        <div style="background: var(--bg-tertiary); padding: 1rem; border-radius: var(--radius-md); border-left: 3px solid var(--danger);">
            <strong style="color: var(--danger);">2. Retrait par Absence</strong>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                Chaque absence non justifiée (séance de 2,5 heures) retire <strong>0,50 point</strong> de la note de 20.
            </p>
        </div>

        <div style="background: var(--bg-tertiary); padding: 1rem; border-radius: var(--radius-md); border-left: 3px solid var(--danger);">
            <strong style="color: #f87171;">3. Conseil de Discipline</strong>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                Tout conseil de discipline (déclenché à 15h d'absences ou pour comportement) déduit automatiquement <strong>-5.00 points</strong>.
            </p>
        </div>

        <div style="background: var(--bg-tertiary); padding: 1rem; border-radius: var(--radius-md); border-left: 3px solid var(--ofppt-green);">
            <strong style="color: var(--ofppt-green);">4. Justificatif Validé</strong>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                Une absence dont le certificat est accepté par la direction ne provoque <strong>aucune perte de point (0 pt)</strong>.
            </p>
        </div>
    </div>
</div>

<!-- Historique Évolutif Séance par Séance -->
<div class="card">
    <div class="card-title">
        <span style="display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="history" size="18" />
            Historique des Absences et Impact sur la Note
        </span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date d'Absence</th>
                    <th>Séance (2h30)</th>
                    <th>Formateur</th>
                    <th>Statut de l'Absence</th>
                    <th>État Justificatif</th>
                    <th>Impact sur la Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absences as $abs)
                <tr>
                    <td><strong>{{ $abs->date_absence->format('d/m/Y') }}</strong></td>
                    <td><span class="badge badge-primary">{{ $abs->seance->code_seance }} ({{ $abs->seance->libelle }})</span></td>
                    <td>{{ $abs->enseignant->name }}</td>
                    <td>
                        @if($abs->statut_absence === 'JUSTIFIEE_ACCEPTEE')
                            <span class="badge badge-success">Justifiée & Validée</span>
                        @elseif($abs->statut_absence === 'EN_ATTENTE_VALIDATION')
                            <span class="badge badge-warning">En cours d'examen admin</span>
                        @elseif($abs->statut_absence === 'JUSTIFIEE_REFUSEE')
                            <span class="badge badge-danger">Justificatif Refusé</span>
                        @else
                            <span class="badge badge-danger">Non Justifiée</span>
                        @endif
                    </td>
                    <td>
                        @if($abs->justificatif)
                            <span style="font-size: 0.85rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.35rem;">
                                <x-icon name="file-text" size="14" /> {{ $abs->justificatif->nom_fichier }}
                            </span>
                        @else
                            <span style="font-size: 0.8rem; color: var(--text-dim);">Non fourni</span>
                        @endif
                    </td>
                    <td>
                        @if($abs->statut_absence === 'JUSTIFIEE_ACCEPTEE')
                            <strong style="color: var(--ofppt-green);">0,00 pt (Préservée)</strong>
                        @elseif($abs->statut_absence === 'EN_ATTENTE_VALIDATION')
                            <span style="color: var(--warning);">En attente de décision</span>
                        @else
                            <strong style="color: var(--danger);">-0,50 pt</strong>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-dim); padding: 2rem;">
                        Aucune absence répertoriée. Votre assiduité est à son maximum.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

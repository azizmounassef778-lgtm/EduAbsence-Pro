@extends('layouts.app')

@section('title', 'Conseils de Discipline')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
            <x-icon name="scale" size="26" class="text-primary" />
            Conseils de Discipline & Sanctions
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
            Gestion des conseils de discipline (automatiques à 15h d'absences non justifiées ou programmés pour comportement).
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="#nouveauConseil" class="btn btn-primary btn-sm">
            <x-icon name="plus" size="16" />
            Nouveau Conseil de Discipline
        </a>
    </div>
</div>

<!-- Statistiques Clés -->
<div class="grid-stats">
    <div class="card stat-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Total Conseils</span>
            <span class="brand-icon" style="width: 32px; height: 32px; background: rgba(11, 79, 138, 0.2); color: var(--ofppt-blue);">
                <x-icon name="scale" size="18" />
            </span>
        </div>
        <div style="font-size: 2rem; font-weight: 800; margin-top: 0.5rem;">
            {{ $conseils->total() }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.25rem;">
            Sanction : -5 points automatiques par conseil
        </div>
    </div>

    <div class="card stat-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Convoqués / En Attente</span>
            <span class="brand-icon" style="width: 32px; height: 32px; background: rgba(217, 119, 6, 0.2); color: var(--warning);">
                <x-icon name="clock" size="18" />
            </span>
        </div>
        <div style="font-size: 2rem; font-weight: 800; margin-top: 0.5rem; color: var(--warning);">
            {{ \App\Models\ConseilDiscipline::where('statut', 'CONVOQUE')->count() }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.25rem;">
            Séances disciplinaires à tenir
        </div>
    </div>

    <div class="card stat-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Seuil 15h Automatique</span>
            <span class="brand-icon" style="width: 32px; height: 32px; background: rgba(220, 38, 38, 0.2); color: var(--danger);">
                <x-icon name="alert-triangle" size="18" />
            </span>
        </div>
        <div style="font-size: 2rem; font-weight: 800; margin-top: 0.5rem; color: var(--danger);">
            {{ \App\Models\ConseilDiscipline::where('type', 'ABSENCE_15H')->count() }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.25rem;">
            Déclenchés pour cumul d'absences sans motif
        </div>
    </div>

    <div class="card stat-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Signalements Formateurs</span>
            <span class="brand-icon" style="width: 32px; height: 32px; background: rgba(2, 132, 199, 0.2); color: var(--info);">
                <x-icon name="shield-alert" size="18" />
            </span>
        </div>
        <div style="font-size: 2rem; font-weight: 800; margin-top: 0.5rem; color: var(--info);">
            {{ $signalements->where('statut', 'EN_ATTENTE')->count() }}
        </div>
        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.25rem;">
            Comportements signalés en attente
        </div>
    </div>
</div>

<!-- Section Signalements Formateurs récents -->
@if($signalements->count() > 0)
<div class="card" style="margin-bottom: 2rem; border-left: 4px solid var(--accent-cyan);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="shield-alert" size="20" class="text-cyan" />
            Signalements de comportement soumis par les formateurs
        </h2>
        <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #38bdf8;">{{ $signalements->count() }} signalement(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Incident</th>
                    <th>Stagiaire</th>
                    <th>Formateur</th>
                    <th>Motif & Gravité</th>
                    <th>Détails</th>
                    <th>Statut</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($signalements as $sig)
                <tr>
                    <td style="font-size: 0.85rem; white-space: nowrap;">
                        <x-icon name="calendar" size="14" />
                        {{ $sig->date_incident->format('d/m/Y') }}
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $sig->stagiaire->user->name ?? 'N/A' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">
                            {{ $sig->stagiaire->matricule_cne ?? '' }} • {{ $sig->stagiaire->groupe->code_groupe ?? '' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem; font-weight: 600;">{{ $sig->enseignant->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $sig->motif }}</div>
                        @if($sig->gravite === 'GRAVE')
                            <span class="badge badge-danger">Gravité Élevée</span>
                        @elseif($sig->gravite === 'MOYENNE')
                            <span class="badge badge-warning">Gravité Moyenne</span>
                        @else
                            <span class="badge badge-info">Gravité Faible</span>
                        @endif
                    </td>
                    <td style="max-width: 280px; font-size: 0.82rem; color: var(--text-muted);">
                        {{ Str::limit($sig->description, 100) }}
                    </td>
                    <td>
                        @if($sig->statut === 'EN_ATTENTE')
                            <span class="badge badge-warning">En attente</span>
                        @elseif($sig->statut === 'CONSEIL_PROGRAMME')
                            <span class="badge badge-success">Conseil convoqué</span>
                        @else
                            <span class="badge badge-secondary">Classé</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($sig->statut === 'EN_ATTENTE')
                            <button class="btn btn-sm btn-primary" onclick="preparerConseil('{{ $sig->stagiaire_id }}', '{{ addslashes($sig->motif) }}', '{{ $sig->id }}', '{{ addslashes($sig->description) }}')">
                                <x-icon name="scale" size="14" />
                                Convoquer Conseil (-5 pts)
                            </button>
                        @else
                            <span style="font-size: 0.8rem; color: var(--text-dim);">Traité</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Formulaire d'Ajout d'un Conseil de Discipline (Comportement ou Manuel) -->
<div class="card" id="nouveauConseil" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
        <x-icon name="plus" size="18" />
        Ajouter un Conseil de Discipline (Réduction automatique de -5 points)
    </h2>

    <div class="alert alert-info" style="margin-bottom: 1.25rem;">
        <x-icon name="info" size="18" />
        <div>
            <strong>Règle disciplinaire :</strong> Tout conseil de discipline enregistré entraîne immédiatement et automatiquement une déduction de <strong>-5.00 points</strong> sur la note globale de comportement du stagiaire. L'administration et le stagiaire reçoivent une notification officielle.
        </div>
    </div>

    <form action="{{ route('admin.conseils.store') }}" method="POST">
        @csrf
        <input type="hidden" name="signalement_id" id="form_signalement_id" value="">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label class="form-label">Stagiaire Concerné *</label>
                <select name="stagiaire_id" id="form_stagiaire_id" class="form-control" required>
                    <option value="">-- Sélectionner un stagiaire --</option>
                    @foreach($stagiaires as $st)
                        <option value="{{ $st->id }}">
                            {{ $st->user->name ?? 'N/A' }} ({{ $st->matricule_cne }}) - {{ $st->groupe->code_groupe ?? 'Sans groupe' }} [Note actuelle : {{ $st->note_discipline }}/20]
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Type de Conseil *</label>
                <select name="type" id="form_type" class="form-control" required>
                    <option value="COMPORTEMENT" selected>Comportement (Indiscipline, Refus, Perturbation)</option>
                    <option value="ABSENCE_15H">Absence (15h sans justification)</option>
                    <option value="INFRACTION_REGLEMENT">Infraction au Règlement Intérieur</option>
                </select>
            </div>

            <div>
                <label class="form-label">Date Prévue de Convocation / Conseil *</label>
                <input type="date" name="date_conseil" id="form_date_conseil" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label class="form-label">Motif du Conseil de Discipline *</label>
            <input type="text" name="motif" id="form_motif" class="form-control" placeholder="Ex: Comportement perturbateur en séance d'atelier, insubordination..." required>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <label class="form-label">Description / Rapport préliminaire des faits (Optionnel)</label>
            <textarea name="description" id="form_description" class="form-control" rows="3" placeholder="Circonstances, témoins, rapports formateurs, antécédents..."></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="submit" class="btn btn-primary">
                <x-icon name="check" size="16" />
                Enregistrer le Conseil & Appliquer la Sanction (-5 pts)
            </button>
        </div>
    </form>
</div>

<!-- Filtres de Recherche -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.conseils.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label class="form-label">Rechercher Stagiaire (Nom ou CNE)</label>
            <div style="position: relative;">
                <input type="text" name="search_stagiaire" class="form-control" placeholder="Ex: Chihab, M130..." value="{{ request('search_stagiaire') }}">
            </div>
        </div>

        <div style="min-width: 170px;">
            <label class="form-label">Type</label>
            <select name="type" class="form-control">
                <option value="">Tous les types</option>
                <option value="ABSENCE_15H" {{ request('type') === 'ABSENCE_15H' ? 'selected' : '' }}>Absence 15h</option>
                <option value="COMPORTEMENT" {{ request('type') === 'COMPORTEMENT' ? 'selected' : '' }}>Comportement</option>
                <option value="INFRACTION_REGLEMENT" {{ request('type') === 'INFRACTION_REGLEMENT' ? 'selected' : '' }}>Règlement</option>
            </select>
        </div>

        <div style="min-width: 170px;">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-control">
                <option value="">Tous les statuts</option>
                <option value="CONVOQUE" {{ request('statut') === 'CONVOQUE' ? 'selected' : '' }}>Convoqué</option>
                <option value="TENU" {{ request('statut') === 'TENU' ? 'selected' : '' }}>Tenu</option>
                <option value="SANCTIONNE" {{ request('statut') === 'SANCTIONNE' ? 'selected' : '' }}>Sanctionné</option>
                <option value="CLOTURE" {{ request('statut') === 'CLOTURE' ? 'selected' : '' }}>Clôturé</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-secondary">
                <x-icon name="filter" size="16" />
                Filtrer
            </button>
            <a href="{{ route('admin.conseils.index') }}" class="btn btn-secondary" title="Réinitialiser">
                <x-icon name="x" size="16" />
            </a>
        </div>
    </form>
</div>

<!-- Table des Conseils de Discipline -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="scale" size="20" />
            Registre des Conseils de Discipline ({{ $conseils->total() }})
        </h2>
    </div>

    @if($conseils->count() > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Réf</th>
                    <th>Stagiaire</th>
                    <th>Type & Origine</th>
                    <th>Date Prévue</th>
                    <th>Motif & Observations</th>
                    <th>Impact Note</th>
                    <th>Statut</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conseils as $c)
                <tr>
                    <td style="font-family: var(--font-mono); font-size: 0.85rem; font-weight: 700;">
                        #CD-{{ $c->id }}
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $c->stagiaire->user->name ?? 'N/A' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">
                            {{ $c->stagiaire->matricule_cne ?? '' }} • {{ $c->stagiaire->groupe->code_groupe ?? '' }}
                        </div>
                        <div style="font-size: 0.72rem; color: var(--accent-green); font-weight: 700; margin-top: 0.15rem;">
                            Note comportement actuelle : {{ $c->stagiaire->note_discipline }}/20
                        </div>
                    </td>
                    <td>
                        @if($c->type === 'ABSENCE_15H')
                            <span class="badge-conseil badge-conseil-auto">
                                <x-icon name="alert-triangle" size="12" />
                                Automatique (15h)
                            </span>
                            <div style="font-size: 0.72rem; color: var(--text-dim); margin-top: 0.2rem;">Détecté par système</div>
                        @else
                            <span class="badge-conseil badge-conseil-manuel">
                                <x-icon name="shield-alert" size="12" />
                                {{ $c->type }}
                            </span>
                            <div style="font-size: 0.72rem; color: var(--text-dim); margin-top: 0.2rem;">
                                Par : {{ $c->createur->name ?? 'Administration' }}
                            </div>
                        @endif
                    </td>
                    <td style="font-size: 0.85rem; white-space: nowrap;">
                        <x-icon name="calendar" size="14" />
                        {{ $c->date_conseil->format('d/m/Y') }}
                    </td>
                    <td>
                        <div style="font-weight: 700; font-size: 0.9rem;">{{ $c->motif }}</div>
                        @if($c->description)
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; max-width: 320px;">
                                {{ $c->description }}
                            </div>
                        @endif
                        @if($c->signalement)
                            <div style="font-size: 0.75rem; color: var(--accent-cyan); margin-top: 0.25rem;">
                                Rapport formateur : {{ $c->signalement->enseignant->name ?? 'Formateur' }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span class="penalty-pill">
                            -{{ number_format($c->reduction_points, 1) }} pts
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.conseils.statut', $c->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <select name="statut" onchange="this.form.submit()" class="form-control form-control-sm" style="padding: 0.2rem 0.5rem; font-size: 0.8rem; font-weight: 700;">
                                <option value="CONVOQUE" {{ $c->statut === 'CONVOQUE' ? 'selected' : '' }}>🟡 Convoqué</option>
                                <option value="TENU" {{ $c->statut === 'TENU' ? 'selected' : '' }}>🔵 Tenu</option>
                                <option value="SANCTIONNE" {{ $c->statut === 'SANCTIONNE' ? 'selected' : '' }}>🔴 Sanctionné</option>
                                <option value="CLOTURE" {{ $c->statut === 'CLOTURE' ? 'selected' : '' }}>⚪ Clôturé</option>
                            </select>
                        </form>
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.conseils.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Confirmer l\'annulation de ce conseil de discipline ? Les 5 points seront restitués au stagiaire.');" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-secondary" title="Annuler le conseil et restituer les 5 points" style="color: #f87171;">
                                <x-icon name="trash" size="14" />
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $conseils->links() }}
    </div>
    @else
    <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-dim);">
        <x-icon name="scale" size="42" class="text-muted" style="margin-bottom: 0.75rem;" />
        <div style="font-weight: 600; font-size: 1rem;">Aucun conseil de discipline enregistré.</div>
        <div style="font-size: 0.85rem; margin-top: 0.25rem;">Les conseils générés pour 15h d'absences ou saisis manuellement apparaîtront ici.</div>
    </div>
    @endif
</div>

<script>
    function preparerConseil(stagiaireId, motif, signalementId, description) {
        document.getElementById('form_stagiaire_id').value = stagiaireId;
        document.getElementById('form_type').value = 'COMPORTEMENT';
        document.getElementById('form_motif').value = motif;
        document.getElementById('form_signalement_id').value = signalementId;
        document.getElementById('form_description').value = description;

        // Scroll vers le formulaire
        document.getElementById('nouveauConseil').scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endsection

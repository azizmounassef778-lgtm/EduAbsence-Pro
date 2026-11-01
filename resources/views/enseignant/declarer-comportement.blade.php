@extends('layouts.app')

@section('title', 'Signaler un Comportement')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.6rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
        <x-icon name="shield-alert" size="26" class="text-primary" />
        Signalement d'un Mauvais Comportement
    </h1>
    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
        Signaler un manquement à la discipline ou un comportement inacceptable. Cette déclaration sera immédiatement transmise aux notifications de l'administration.
    </p>
</div>

<!-- Formulaire de Signalement -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="alert alert-info" style="margin-bottom: 1.5rem;">
        <x-icon name="info" size="18" />
        <div>
            Votre rapport sera envoyé directement au bureau de la direction et des surveillants généraux. L'administration pourra alors convoquer le stagiaire à un <strong>Conseil de Discipline</strong> entraînant automatiquement une déduction de <strong>-5 points</strong> sur sa note de comportement.
        </div>
    </div>

    <form action="{{ route('enseignant.declarer-comportement.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
            <div>
                <label class="form-label">Stagiaire Concerné *</label>
                <select name="stagiaire_id" class="form-control" required>
                    <option value="">-- Sélectionner un stagiaire de vos groupes --</option>
                    @foreach($groupesAttribués as $ga)
                        <optgroup label="Groupe {{ $ga->groupe->code_groupe }} - {{ $ga->groupe->nom_groupe }}">
                            @foreach($ga->groupe->stagiaires as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->user->name ?? 'N/A' }} ({{ $st->matricule_cne }}) - Note actuelle : {{ $st->note_discipline }}/20
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Date de l'Incident *</label>
                <input type="date" name="date_incident" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
            </div>

            <div>
                <label class="form-label">Séance de Cours (Optionnel)</label>
                <select name="seance_id" class="form-control">
                    <option value="">-- Préciser la séance --</option>
                    @foreach($seances as $seance)
                        <option value="{{ $seance->id }}">{{ $seance->code_seance }} ({{ $seance->libelle }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Niveau de Gravité *</label>
                <select name="gravite" class="form-control" required>
                    <option value="MOYENNE" selected>Moyenne (Perturbation récurrente, refus de travail)</option>
                    <option value="FAIBLE">Faible (Avertissement verbal préalable ignoré)</option>
                    <option value="GRAVE">Grave (Insolence, agression verbale, dégradation de matériel)</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <label class="form-label">Motif du Signalement *</label>
            <input type="text" name="motif" class="form-control" placeholder="Ex: Perturbation flagrante du cours, insubordination, utilisation interdite de téléphone..." required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label class="form-label">Description Détaillée des Faits *</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Expliquez précisément ce qui s'est passé : déroulement des événements, avertissements donnés, attitude du stagiaire..." required minlength="10"></textarea>
            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.35rem;">
                Donnez un maximum de précisions pour aider l'administration dans sa prise de décision.
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('enseignant.dashboard') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <x-icon name="shield-alert" size="16" />
                Transmettre le Signalement à l'Administration
            </button>
        </div>
    </form>
</div>

<!-- Historique des Signalements de ce Professeur -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            <x-icon name="history" size="18" />
            Historique de Vos Signalements Transmis ({{ $mesSignalements->total() }})
        </h2>
    </div>

    @if($mesSignalements->count() > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Incident</th>
                    <th>Stagiaire</th>
                    <th>Séance</th>
                    <th>Motif</th>
                    <th>Gravité</th>
                    <th>Statut Prise en Charge</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mesSignalements as $sig)
                <tr>
                    <td style="white-space: nowrap; font-size: 0.85rem;">
                        <x-icon name="calendar" size="14" />
                        {{ $sig->date_incident->format('d/m/Y') }}
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $sig->stagiaire->user->name ?? 'N/A' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">
                            {{ $sig->stagiaire->matricule_cne ?? '' }} • {{ $sig->stagiaire->groupe->code_groupe ?? '' }}
                        </div>
                    </td>
                    <td style="font-size: 0.85rem;">
                        {{ $sig->seance ? $sig->seance->code_seance . ' (' . $sig->seance->libelle . ')' : 'Non spécifiée' }}
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $sig->motif }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; max-width: 300px;">
                            {{ Str::limit($sig->description, 90) }}
                        </div>
                    </td>
                    <td>
                        @if($sig->gravite === 'GRAVE')
                            <span class="badge badge-danger">Grave</span>
                        @elseif($sig->gravite === 'MOYENNE')
                            <span class="badge badge-warning">Moyenne</span>
                        @else
                            <span class="badge badge-info">Faible</span>
                        @endif
                    </td>
                    <td>
                        @if($sig->statut === 'EN_ATTENTE')
                            <span class="badge badge-warning">Transmis • En examen</span>
                        @elseif($sig->statut === 'CONSEIL_PROGRAMME')
                            <span class="badge badge-success">Conseil de discipline convoqué</span>
                        @else
                            <span class="badge badge-secondary">Classé</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $mesSignalements->links() }}
    </div>
    @else
    <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-dim);">
        <x-icon name="check-circle" size="38" style="margin-bottom: 0.5rem; color: var(--accent-green);" />
        <div style="font-weight: 600;">Aucun signalement de comportement émis.</div>
        <div style="font-size: 0.85rem; margin-top: 0.25rem;">Tous vos stagiaires ont maintenu une bonne conduite en classe.</div>
    </div>
    @endif
</div>
@endsection

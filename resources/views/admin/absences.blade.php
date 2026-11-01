@extends('layouts.app')

@section('title', 'Recherche & Filtres des Absences')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800;">Recherche & Filtres des Absences</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Tâche 21 : Filtrage multi-critères avancé et Tâche 22 : Correction d'erreurs avec journal d'audit
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-secondary btn-sm">
            <x-icon name="download" size="14" /> Exporter en CSV
        </a>
        <a href="{{ route('admin.absences') }}" class="btn btn-secondary btn-sm">
            <x-icon name="x" size="14" /> Réinitialiser filtres
        </a>
    </div>
</div>

<!-- Formulaire de recherche et filtres multi-critères (Tâche 21) -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form action="{{ route('admin.absences') }}" method="GET">
        <div class="form-row">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Groupe / Classe</label>
                <select name="groupe_id" class="form-select">
                    <option value="">Tous les groupes</option>
                    @foreach($groupes as $g)
                        <option value="{{ $g->id }}" {{ request('groupe_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->code_groupe }} - {{ $g->nom_groupe }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Stagiaire (Nom ou CNE)</label>
                <input type="text" name="search_stagiaire" class="form-control" placeholder="Rechercher par nom, CNE..." value="{{ request('search_stagiaire') }}">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Enseignant</label>
                <select name="enseignant_id" class="form-select">
                    <option value="">Tous les enseignants</option>
                    @foreach($enseignants as $e)
                        <option value="{{ $e->id }}" {{ request('enseignant_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Statut</label>
                <select name="statut_absence" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="NON_JUSTIFIEE" {{ request('statut_absence') == 'NON_JUSTIFIEE' ? 'selected' : '' }}>Non justifiée</option>
                    <option value="EN_ATTENTE_VALIDATION" {{ request('statut_absence') == 'EN_ATTENTE_VALIDATION' ? 'selected' : '' }}>En attente de validation</option>
                    <option value="JUSTIFIEE_ACCEPTEE" {{ request('statut_absence') == 'JUSTIFIEE_ACCEPTEE' ? 'selected' : '' }}>Justifiée et Acceptée ✅</option>
                    <option value="JUSTIFIEE_REFUSEE" {{ request('statut_absence') == 'JUSTIFIEE_REFUSEE' ? 'selected' : '' }}>Justifiée mais Refusée ❌</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Date Début</label>
                <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Date Fin</label>
                <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
            </div>
        </div>

        <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary btn-sm">
                <x-icon name="search" size="14" /> Appliquer les critères de recherche
            </button>
        </div>
    </form>
</div>

<!-- Tableau des Résultats -->
<div class="card">
    <div class="card-title">
        <span>Résultats : {{ $absences->total() }} absences trouvées</span>
        <a href="{{ route('admin.audit') }}" class="btn btn-secondary btn-sm">
            <x-icon name="history" size="14" /> Voir Journal d'Audit
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Séance</th>
                    <th>Stagiaire</th>
                    <th>Groupe</th>
                    <th>Enseignant</th>
                    <th>Statut</th>
                    <th>Délai 48h / Justificatif</th>
                    <th>Actions (Correction)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absences as $abs)
                @php $delai = $abs->delai48h(); @endphp
                <tr>
                    <td>#{{ $abs->id }}</td>
                    <td>
                        {{ $abs->date_absence->format('d/m/Y') }}
                        @if($abs->est_corrigee)
                            <span class="badge badge-warning" style="font-size: 0.65rem;" title="Absence corrigée par l'administration">Corrigée</span>
                        @endif
                    </td>
                    <td><span class="badge badge-primary">{{ $abs->seance->code_seance }}</span></td>
                    <td>
                        <strong>{{ $abs->stagiaire->user->name }}</strong>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">CNE: {{ $abs->stagiaire->matricule_cne }}</div>
                    </td>
                    <td>{{ $abs->stagiaire->groupe->code_groupe }}</td>
                    <td>{{ $abs->enseignant->name }}</td>
                    <td>
                        @if($abs->statut_absence === 'JUSTIFIEE_ACCEPTEE')
                            <span class="badge badge-success">Validée (0 pt déduit)</span>
                        @elseif($abs->statut_absence === 'EN_ATTENTE_VALIDATION')
                            <span class="badge badge-warning">En cours d'examen</span>
                        @elseif($abs->statut_absence === 'JUSTIFIEE_REFUSEE')
                            <span class="badge badge-danger">Refusée (-0.5 pt)</span>
                        @else
                            <span class="badge badge-danger">Non justifiée (-0.5 pt)</span>
                        @endif
                    </td>
                    <td>
                        @if($abs->justificatif)
                            <span class="badge badge-info" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                <x-icon name="file-text" size="12" /> {{ $abs->justificatif->nom_fichier }}
                            </span>
                        @else
                            <span class="badge {{ $delai['badge_class'] }}">
                                {{ $delai['texte'] }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <!-- Bouton Déclencher Modal de Correction (Tâche 22) -->
                        <button class="btn btn-secondary btn-sm" onclick="openCorrectionModal({{ $abs->id }}, '{{ $abs->date_absence->format('Y-m-d') }}', {{ $abs->seance_id }}, '{{ addslashes($abs->motif_enseignant ?? '') }}')">
                            <x-icon name="edit" size="14" /> Corriger
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--text-dim); padding: 2rem;">
                        Aucune absence ne correspond aux critères de recherche sélectionnés.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $absences->links() }}
    </div>
</div>

<!-- Modal de Correction d'une Absence (Tâche 22) -->
<div id="correctionModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Correction Administrative d'Absence</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeCorrectionModal()">✕</button>
        </div>
        <form id="correctionForm" method="POST">
            @csrf
            <div class="modal-body">
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.25rem;">
                    Conformément aux règles administratives, toute correction est consignée dans le <strong>Journal d'audit</strong> et la note de comportement du stagiaire sera <strong>automatiquement recalculée</strong>.
                </p>

                <div class="form-group">
                    <label class="form-label">Date de l'absence</label>
                    <input type="date" id="modal_date_absence" name="date_absence" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Séance</label>
                    <select id="modal_seance_id" name="seance_id" class="form-select" required>
                        @foreach($seances as $s)
                            <option value="{{ $s->id }}">{{ $s->code_seance }} - {{ $s->libelle }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Remarque / Motif enseignant</label>
                    <textarea id="modal_motif_enseignant" name="motif_enseignant" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" style="color: var(--primary);">Motif obligatoire de la correction (Journal d'audit)</label>
                    <textarea name="motif_correction" class="form-control" rows="2" placeholder="Ex: Erreur de pointage de séance confirmée sur la feuille d'appel..." required></textarea>
                </div>

                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                    <label style="color: var(--danger); font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="action_supprimer" value="1">
                        <strong>Annuler et supprimer complètement cette absence saisie par erreur</strong>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCorrectionModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer la correction & Recalculer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCorrectionModal(id, date, seanceId, motif) {
        document.getElementById('correctionForm').action = '/admin/absences/' + id + '/corriger';
        document.getElementById('modal_date_absence').value = date;
        document.getElementById('modal_seance_id').value = seanceId;
        document.getElementById('modal_motif_enseignant').value = motif;
        document.getElementById('correctionModal').classList.add('active');
    }
    function closeCorrectionModal() {
        document.getElementById('correctionModal').classList.remove('active');
    }
</script>
@endsection

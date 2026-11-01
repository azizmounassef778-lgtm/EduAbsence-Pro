@extends('layouts.app')

@section('title', 'Mes Absences & Justificatifs')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Mes Absences & Dépôt des Justificatifs</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Suivi de vos absences, application du délai réglementaire de 48h et téléversement des certificats médicaux
        </p>
    </div>
    <a href="{{ route('stagiaire.note-discipline') }}" class="btn btn-secondary btn-sm">
        <x-icon name="award" size="16" /> Consulter ma note de discipline (20/20)
    </a>
</div>

<!-- Rappel de la règle des 48 heures & Barème de Discipline -->
<div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid var(--warning); background: rgba(217, 119, 6, 0.06);">
    <div style="display: flex; align-items: flex-start; gap: 1rem;">
        <span style="color: var(--warning); display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(217, 119, 6, 0.15); border-radius: var(--radius-md);">
            <x-icon name="clock" size="24" />
        </span>
        <div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--warning); margin-bottom: 0.25rem;">
                Règlement des Absences & Délai de 48 Heures
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                Vous disposez d'un <strong>délai strict de 48 heures</strong> à compter de l'enregistrement de l'absence pour téléverser votre certificat médical ou convocation officielle (formats acceptés : PDF, PNG, JPG - Max 5 Mo). À l'expiration des 48h, le système désactive automatiquement le dépôt et une déduction ferme de <strong>0,50 point</strong> est retirée de votre note de comportement sur <strong>20</strong> pour chaque séance de 2h30 non justifiée.
            </p>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date d'Absence</th>
                    <th>Séance (2h30)</th>
                    <th>Formateur</th>
                    <th>Horodatage</th>
                    <th>Délai Restant (48h)</th>
                    <th>État du Justificatif</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absences as $abs)
                @php $delai = $abs->delaiInfo; @endphp
                <tr>
                    <td>
                        <strong style="font-size: 0.95rem;">{{ $abs->date_absence->format('d/m/Y') }}</strong>
                        @if($abs->motif_enseignant)
                            <div style="font-size: 0.75rem; color: var(--text-dim);"><em>"{{ $abs->motif_enseignant }}"</em></div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-primary">{{ $abs->seance->code_seance }}</span>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $abs->seance->libelle }}</div>
                    </td>
                    <td style="font-size: 0.85rem;">{{ $abs->enseignant->name }}</td>
                    <td style="font-size: 0.8rem; color: var(--text-dim); font-family: var(--font-mono);">
                        {{ $abs->heure_enregistrement->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        @if($abs->justificatif)
                            <span class="badge badge-info">Document Déposé</span>
                        @else
                            <span class="badge {{ $delai['badge_class'] }}">
                                {{ $delai['texte'] }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(!$abs->justificatif)
                            @if($delai['est_expire'])
                                <span class="badge badge-danger">Non justifiée (Délai dépassé)</span>
                                <div style="font-size: 0.75rem; color: var(--danger); margin-top: 0.2rem;">-0.50 pt sur la note de 20</div>
                            @else
                                <span class="badge badge-warning">En attente de votre dépôt</span>
                            @endif
                        @else
                            @if($abs->justificatif->statut_decision === 'ACCEPTE')
                                <span class="badge badge-success">
                                    <x-icon name="check-circle" size="12" /> Accepté par la Direction
                                </span>
                                <div style="font-size: 0.75rem; color: var(--ofppt-green); margin-top: 0.2rem;">Aucun point retiré (0 pt)</div>
                            @elseif($abs->justificatif->statut_decision === 'REFUSE')
                                <span class="badge badge-danger">
                                    <x-icon name="x-circle" size="12" /> Refusé par la Direction
                                </span>
                                <div style="font-size: 0.75rem; color: var(--danger); margin-top: 0.2rem;">
                                    Motif : "{{ $abs->justificatif->motif_decision }}" (-0.5 pt)
                                </div>
                            @else
                                <span class="badge badge-warning">
                                    <x-icon name="clock" size="12" /> En cours d'examen
                                </span>
                                <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.2rem;">Fichier : {{ $abs->justificatif->nom_fichier }}</div>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(!$delai['est_expire'])
                            <button class="btn btn-primary btn-sm" onclick="openUploadModal({{ $abs->id }}, '{{ $abs->date_absence->format('d/m/Y') }}', '{{ $abs->seance->code_seance }}', '{{ $delai['heures_restantes'] }}h {{ $delai['minutes_restantes'] }}m')">
                                <x-icon name="upload" size="14" /> {{ $abs->justificatif ? 'Remplacer' : 'Déposer Certificat' }}
                            </button>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled title="{{ $delai['message'] }}">
                                <x-icon name="x" size="14" /> Dépôt Bloqué (>48h)
                            </button>
                            <div style="font-size: 0.7rem; color: var(--danger); margin-top: 0.2rem;">
                                Délai 48h dépassé
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 2.5rem;">
                        Aucune absence répertoriée dans votre dossier académique.
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

<!-- Modal Téléversement Justificatif -->
<div id="uploadModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Téléversement du Certificat Médical</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeUploadModal()">✕</button>
        </div>
        <form id="uploadForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div style="background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                    <div style="font-size: 0.85rem;">
                        <strong>Absence concernée :</strong> <span id="modal_absence_info"></span>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--warning); margin-top: 0.25rem; display: flex; align-items: center; gap: 0.35rem;">
                        <x-icon name="clock" size="14" /> Temps restant pour déposer : <strong id="modal_time_remaining"></strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Sélectionner votre document justificatif</label>
                    <input type="file" name="fichier" class="form-control" accept=".pdf,.png,.jpg,.jpeg" required style="padding: 0.5rem;">
                    <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.35rem;">
                        Formats acceptés : <strong>PDF, PNG, JPG, JPEG</strong> • Taille max : <strong>5 Mo</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Commentaire ou motif pour l'administration (optionnel)</label>
                    <textarea name="commentaire" class="form-control" rows="2" placeholder="Précisez le nom du médecin ou la raison..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeUploadModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="upload" size="14" /> Téléverser et Soumettre
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUploadModal(absenceId, date, seance, timeRemaining) {
        document.getElementById('uploadForm').action = '/stagiaire/absences/' + absenceId + '/justificatif';
        document.getElementById('modal_absence_info').textContent = date + ' (' + seance + ')';
        document.getElementById('modal_time_remaining').textContent = timeRemaining;
        document.getElementById('uploadModal').classList.add('active');
    }
    function closeUploadModal() {
        document.getElementById('uploadModal').classList.remove('active');
    }
</script>
@endsection

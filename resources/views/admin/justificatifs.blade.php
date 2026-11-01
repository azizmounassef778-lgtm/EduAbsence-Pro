@extends('layouts.app')

@section('title', 'Validation des Justificatifs')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Validation des Justificatifs Médicaux & Certificats</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Examen, consultation et décision sur les pièces justificatives déposées par les stagiaires
        </p>
    </div>
</div>

<div class="card">
    <div class="card-title">
        <span>Liste des Certificats Soumis</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date Dépôt</th>
                    <th>Stagiaire</th>
                    <th>Groupe</th>
                    <th>Absence Concernée</th>
                    <th>Certificat</th>
                    <th>Taille</th>
                    <th>Statut</th>
                    <th>Actions & Décision</th>
                </tr>
            </thead>
            <tbody>
                @forelse($justificatifs as $j)
                <tr>
                    <td>{{ $j->date_depot->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $j->absence->stagiaire->user->name }}</strong>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">CNE: {{ $j->absence->stagiaire->matricule_cne }}</div>
                    </td>
                    <td><span class="badge badge-primary">{{ $j->absence->stagiaire->groupe->code_groupe }}</span></td>
                    <td>
                        <strong>{{ $j->absence->date_absence->format('d/m/Y') }}</strong> ({{ $j->absence->seance->code_seance }})
                        <div style="font-size: 0.75rem; color: var(--text-dim);">Formateur : {{ $j->absence->enseignant->name }}</div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <x-icon name="file-text" size="20" class="text-primary" />
                            <div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openCertifModal('{{ asset($j->chemin_fichier) }}', '{{ addslashes($j->nom_fichier) }}', '{{ $j->type_fichier }}', '{{ addslashes($j->absence->stagiaire->user->name) }}', '{{ $j->absence->date_absence->format('d/m/Y') }}', '{{ $j->id }}', '{{ $j->statut_decision }}')" style="font-size: 0.78rem;">
                                    <x-icon name="eye" size="14" /> Consulter la pièce
                                </button>
                                <div style="font-size: 0.7rem; color: var(--text-dim); margin-top: 0.2rem;">{{ $j->nom_fichier }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $j->tailleLisible() }}</td>
                    <td>
                        @if($j->statut_decision === 'ACCEPTE')
                            <span class="badge badge-success">
                                <x-icon name="check-circle" size="12" /> Accepté
                            </span>
                        @elseif($j->statut_decision === 'REFUSE')
                            <span class="badge badge-danger">
                                <x-icon name="x-circle" size="12" /> Refusé
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <x-icon name="clock" size="12" /> En attente
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($j->statut_decision === 'EN_ATTENTE')
                            <form action="{{ route('admin.justificatifs.valider', $j->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.35rem; min-width: 200px;">
                                @csrf
                                <input type="text" name="motif_decision" class="form-control" placeholder="Remarque ou motif..." style="font-size: 0.75rem; padding: 0.3rem 0.5rem;">
                                <div style="display: flex; gap: 0.35rem;">
                                    <button type="submit" name="decision" value="ACCEPTE" class="btn btn-success btn-sm" style="flex: 1;">
                                        Accepter (0 pt)
                                    </button>
                                    <button type="submit" name="decision" value="REFUSE" class="btn btn-danger btn-sm" style="flex: 1;">
                                        Refuser (-0.5 pt)
                                    </button>
                                </div>
                            </form>
                        @else
                            <div style="font-size: 0.8rem;">
                                <div><strong>Validateur :</strong> {{ $j->adminValidateur->name ?? 'Direction' }}</div>
                                <div style="color: var(--text-muted); font-size: 0.72rem;">{{ $j->date_decision ? $j->date_decision->format('d/m/Y H:i') : '-' }}</div>
                                <div style="font-style: italic; color: var(--text-dim); margin-top: 0.15rem;">"{{ $j->motif_decision }}"</div>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-dim); padding: 2.5rem;">
                        Aucun justificatif médical en attente de décision.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $justificatifs->links() }}
    </div>
</div>

<!-- Modal Consultation Complète du Certificat -->
<div id="certifModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 700px;">
        <div class="modal-header">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Examen de la Pièce Justificative</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeCertifModal()">✕</button>
        </div>
        <div class="modal-body">
            <div style="background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.85rem;">
                <div><strong>Stagiaire :</strong> <span id="modal_st_name"></span></div>
                <div><strong>Date de l'absence :</strong> <span id="modal_abs_date"></span></div>
                <div><strong>Fichier :</strong> <span id="modal_file_name"></span></div>
            </div>

            <!-- Zone de Prévisualisation -->
            <div id="previewContainer" style="background: #0b1120; border-radius: var(--radius-md); padding: 1rem; text-align: center; min-height: 250px; display: flex; align-items: center; justify-content: center; flex-direction: column; border: 1px solid var(--border-color);">
                <div id="imagePreviewWrapper" style="display: none; width: 100%;">
                    <img id="certifImage" src="" alt="Certificat" style="max-width: 100%; max-height: 400px; border-radius: var(--radius-sm); object-fit: contain;">
                </div>
                <div id="pdfPreviewWrapper" style="display: none; width: 100%;">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">📄</div>
                    <p style="font-size: 0.9rem; color: var(--text-main); font-weight: 600;">Document PDF Numérique</p>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">Cliquez ci-dessous pour ouvrir ou télécharger le document officiel</p>
                </div>
                <a id="downloadCertifBtn" href="" target="_blank" class="btn btn-primary btn-sm" style="margin-top: 0.75rem;">
                    📥 Ouvrir le document dans un nouvel onglet
                </a>
            </div>

            <!-- Formulaire de décision directe dans la modal -->
            <form id="modalDecisionForm" method="POST" style="margin-top: 1.25rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label">Motif ou remarque administrative</label>
                    <input type="text" name="motif_decision" class="form-control" placeholder="Ex: Certificat médical de 48h conforme et validé...">
                </div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="submit" name="decision" value="REFUSE" class="btn btn-danger">
                        <x-icon name="x-circle" size="14" /> Refuser (-0.5 pt)
                    </button>
                    <button type="submit" name="decision" value="ACCEPTE" class="btn btn-success">
                        <x-icon name="check-circle" size="14" /> Accepter & Valider (0 pt perdu)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCertifModal(fileUrl, fileName, mimeType, studentName, absDate, justifId, status) {
        document.getElementById('modal_st_name').textContent = studentName;
        document.getElementById('modal_abs_date').textContent = absDate;
        document.getElementById('modal_file_name').textContent = fileName;
        document.getElementById('downloadCertifBtn').href = fileUrl;
        document.getElementById('modalDecisionForm').action = '/admin/justificatifs/' + justifId + '/valider';

        const isImage = mimeType.includes('image') || fileName.match(/\.(jpg|jpeg|png|webp|gif)$/i);
        if (isImage) {
            document.getElementById('imagePreviewWrapper').style.display = 'block';
            document.getElementById('pdfPreviewWrapper').style.display = 'none';
            document.getElementById('certifImage').src = fileUrl;
        } else {
            document.getElementById('imagePreviewWrapper').style.display = 'none';
            document.getElementById('pdfPreviewWrapper').style.display = 'block';
        }

        document.getElementById('certifModal').classList.add('active');
    }

    function closeCertifModal() {
        document.getElementById('certifModal').classList.remove('active');
    }
</script>
@endsection

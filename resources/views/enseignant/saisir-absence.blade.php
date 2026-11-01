@extends('layouts.app')

@section('title', 'Saisie d\'une Absence')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.85rem; font-weight: 800;">Enregistrement d'une Absence</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Saisie de l'absence par séance et horodatage automatique pour le délai de 48 heures
        </p>
    </div>

    <div class="card">
        <form action="{{ route('enseignant.saisir-absence.store') }}" method="POST">
            @csrf

            <!-- Sélection du Groupe -->
            <div class="form-group">
                <label class="form-label">Groupe d'enseignement</label>
                <select id="groupeSelect" class="form-select" onchange="updateStagiairesList()" required>
                    <option value="">Sélectionnez un groupe attribué</option>
                    @foreach($groupesAttribués as $eg)
                        <option value="{{ $eg->groupe_id }}" {{ request('preselect_groupe') == $eg->groupe_id ? 'selected' : '' }}>
                            {{ $eg->groupe->code_groupe }} - {{ $eg->groupe->nom_groupe }} ({{ $eg->matiere }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sélection du Stagiaire -->
            <div class="form-group">
                <label class="form-label">Stagiaire concerné</label>
                <select name="stagiaire_id" id="stagiaireSelect" class="form-select" required>
                    <option value="">Sélectionnez d'abord un groupe ci-dessus</option>
                </select>
            </div>

            <div class="form-row">
                <!-- Date de l'absence -->
                <div class="form-group">
                    <label class="form-label">Date de l'absence</label>
                    <input type="date" name="date_absence" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Séance -->
                <div class="form-group">
                    <label class="form-label">Séance / Créneau horaire</label>
                    <select name="seance_id" class="form-select" required>
                        @foreach($seances as $s)
                            <option value="{{ $s->id }}">{{ $s->code_seance }} ({{ $s->libelle }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Motif de l'enseignant -->
            <div class="form-group">
                <label class="form-label">Remarque / Motif du formateur</label>
                <textarea name="motif_enseignant" class="form-control" rows="3" placeholder="Ex: Absence constatée lors de l'appel au début du TP..." required></textarea>
            </div>

            <!-- Note explicative sur la règle des 48h -->
            <div style="background: rgba(11, 79, 138, 0.12); border: 1px solid rgba(11, 79, 138, 0.35); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.85rem;">
                <div style="font-weight: 700; color: #38bdf8; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
                    <x-icon name="clock" size="16" /> Règle Réglementaire des 48 Heures
                </div>
                Dès l'enregistrement, le système horodate l'absence et notifie instantanément le stagiaire. Celui-ci disposera d'un compte à rebours strict de <strong>48 heures</strong> pour téléverser son certificat médical. Si 15 heures non justifiées sont atteintes, un conseil de discipline automatique est déclenché (-5 points).
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('enseignant.dashboard') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" size="16" /> Enregistrer l'Absence & Alerter le Stagiaire
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Dictionnaire des stagiaires par groupe
    const stagiairesByGroup = {
        @foreach($groupesAttribués as $eg)
            "{{ $eg->groupe_id }}": [
                @foreach($eg->groupe->stagiaires as $st)
                    { id: {{ $st->id }}, name: "{{ addslashes($st->user->name) }}", cne: "{{ $st->matricule_cne }}" },
                @endforeach
            ],
        @endforeach
    };

    function updateStagiairesList() {
        const groupeId = document.getElementById('groupeSelect').value;
        const stagiaireSelect = document.getElementById('stagiaireSelect');
        stagiaireSelect.innerHTML = '<option value="">Sélectionnez un stagiaire</option>';

        if (groupeId && stagiairesByGroup[groupeId]) {
            stagiairesByGroup[groupeId].forEach(st => {
                const opt = document.createElement('option');
                opt.value = st.id;
                opt.textContent = `${st.name} (CNE: ${st.cne})`;
                if ("{{ request('preselect_stagiaire') }}" == st.id) {
                    opt.selected = true;
                }
                stagiaireSelect.appendChild(opt);
            });
        }
    }

    // Initialiser au chargement si un groupe est pré-sélectionné
    document.addEventListener('DOMContentLoaded', () => {
        updateStagiairesList();
    });
</script>
@endsection

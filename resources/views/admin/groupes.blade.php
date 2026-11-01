@extends('layouts.app')

@section('title', 'Gestion des Groupes')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Gestion des Groupes & Classes</h1>
        <p style="color: var(--text-muted);">Tâche 7 : Organisation des stagiaires par filière, promotion et groupe d'apprentissage</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateGroupeModal()">
        <x-icon name="plus" size="16" /> Ajouter un Groupe
    </button>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    @foreach($groupes as $g)
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <div>
                <span class="badge badge-primary" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">{{ $g->code_groupe }}</span>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-top: 0.5rem;">{{ $g->nom_groupe }}</h3>
            </div>
            <span class="badge badge-info">{{ $g->annee_formation }}e Année</span>
        </div>

        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">
            {{ $g->description ?? 'Aucune description spécifique.' }}
        </p>

        <div style="background: var(--bg-tertiary); padding: 0.75rem 1rem; border-radius: var(--radius-md); font-size: 0.85rem; margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                <span style="color: var(--text-dim);">Filière :</span>
                <strong>{{ $g->filiere }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                <span style="color: var(--text-dim);">Effectif :</span>
                <span class="badge badge-success">{{ $g->stagiaires->count() }} Stagiaires</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-dim);">Responsable :</span>
                <strong>{{ $g->responsable->name ?? 'Non assigné' }}</strong>
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.stagiaires', ['groupe_id' => $g->id]) }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                <x-icon name="users" size="14" /> Voir les stagiaires
            </a>
            <a href="{{ route('admin.absences', ['groupe_id' => $g->id]) }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                <x-icon name="search" size="14" /> Absences du groupe
            </a>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Créer Groupe -->
<div id="createGroupeModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Ajouter un Nouveau Groupe</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeCreateGroupeModal()">✕</button>
        </div>
        <form action="{{ route('admin.groupes.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Code du Groupe (Unique)</label>
                    <input type="text" name="code_groupe" class="form-control" placeholder="Ex: DEV103" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Intitulé du Groupe</label>
                    <input type="text" name="nom_groupe" class="form-control" placeholder="Ex: Développement Fullstack - Groupe 3" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Filière</label>
                    <input type="text" name="filiere" class="form-control" placeholder="Ex: Développement Digital" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Année de Formation</label>
                    <select name="annee_formation" class="form-select" required>
                        <option value="1">1ère Année</option>
                        <option value="2">2ème Année</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Enseignant Responsable</label>
                    <select name="id_responsable" class="form-select">
                        <option value="">Sélectionner un formateur (optionnel)</option>
                        @foreach($enseignants as $e)
                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description / Remarques</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCreateGroupeModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le Groupe</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateGroupeModal() {
        document.getElementById('createGroupeModal').classList.add('active');
    }
    function closeCreateGroupeModal() {
        document.getElementById('createGroupeModal').classList.remove('active');
    }
</script>
@endsection

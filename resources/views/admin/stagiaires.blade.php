@extends('layouts.app')

@section('title', 'Gestion des Stagiaires')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Gestion des Dossiers Stagiaires</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Enregistrement, affectation aux groupes, CNE et suivi de la discipline</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateStagiaireModal()">
        <x-icon name="plus" size="16" /> Inscrire un Stagiaire
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>CNE / Matricule</th>
                    <th>Groupe</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Note Discipline (sur 20)</th>
                    <th>Total Absences</th>
                    <th style="text-align: right;">Conseil Discipline</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stagiaires as $st)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="{{ $st->user->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($st->user->name) }}" class="user-avatar" style="width: 32px; height: 32px;">
                            <div>
                                <strong>{{ $st->user->name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-info" style="font-family: var(--font-mono);">{{ $st->matricule_cne }}</span></td>
                    <td><span class="badge badge-primary">{{ $st->groupe->code_groupe }}</span></td>
                    <td style="font-size: 0.8rem; font-family: var(--font-mono);">{{ $st->user->email }}</td>
                    <td>{{ $st->user->telephone ?? 'Non renseigné' }}</td>
                    <td>
                        <strong style="color: {{ $st->note_discipline >= 16.0 ? 'var(--ofppt-green)' : ($st->note_discipline >= 12.0 ? 'var(--info)' : 'var(--danger)') }}; font-size: 1rem;">
                            {{ number_format($st->note_discipline, 2) }} / 20.00
                        </strong>
                    </td>
                    <td>
                        <span class="badge badge-secondary">{{ $st->absences->count() }} absences</span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.conseils.index', ['search_stagiaire' => $st->matricule_cne]) }}" class="btn btn-secondary btn-sm" title="Voir les conseils de discipline">
                            <x-icon name="scale" size="14" />
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $stagiaires->links() }}
    </div>
</div>

<!-- Modal Inscription Stagiaire -->
<div id="createStagiaireModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Inscrire un Nouveau Stagiaire</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeCreateStagiaireModal()">✕</button>
        </div>
        <form action="{{ route('admin.stagiaires.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom et Prénom</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Youssef El Mansouri" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Numéro CNE / Matricule</label>
                        <input type="text" name="matricule_cne" class="form-control" placeholder="Ex: M139045812" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Groupe d'affectation</label>
                        <select name="groupe_id" class="form-select" required>
                            @foreach($groupes as $g)
                                <option value="{{ $g->id }}">{{ $g->code_groupe }} - {{ $g->nom_groupe }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Adresse Email</label>
                        <input type="email" name="email" class="form-control" placeholder="youssef@edu.ma" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mot de passe initial (enregistré en BDD)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" placeholder="+212 6 XX XX XX XX">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date de Naissance</label>
                        <input type="date" name="date_naissance" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCreateStagiaireModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le Dossier & Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateStagiaireModal() {
        document.getElementById('createStagiaireModal').classList.add('active');
    }
    function closeCreateStagiaireModal() {
        document.getElementById('createStagiaireModal').classList.remove('active');
    }
</script>
@endsection

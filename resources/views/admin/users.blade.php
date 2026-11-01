@extends('layouts.app')

@section('title', 'Gestion des Comptes')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Gestion des Comptes Utilisateurs</h1>
        <p style="color: var(--text-muted);">Tâche 5 : Création, gestion des rôles, droits d'accès et activation/désactivation</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateUserModal()">
        <x-icon name="plus" size="16" /> Créer un Utilisateur
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Téléphone</th>
                    <th>Date Création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img
                                src="{{ $u->avatar ?: 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($u->name) }}"
                                alt="Photo de {{ $u->name }}"
                                class="user-avatar"
                                style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;"
                                onerror="this.onerror=null; this.src='https://api.dicebear.com/7.x/initials/svg?seed={{ urlencode($u->name) }}';"

>

                            <div>
                                <strong>{{ $u->name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td style="font-family: var(--font-mono); font-size: 0.8rem;">{{ $u->email }}</td>
                    <td>
                        @if($u->role === 'admin')
                            <span class="badge badge-info" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                <x-icon name="user-check" size="12" /> Administrateur
                            </span>
                        @elseif($u->role === 'enseignant')
                            <span class="badge badge-primary" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                <x-icon name="users" size="12" /> Enseignant
                            </span>
                        @else
                            <span class="badge badge-success" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                <x-icon name="graduation-cap" size="12" /> Stagiaire
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($u->statut_compte === 'actif')
                            <span class="badge badge-success">Actif</span>
                        @else
                            <span class="badge badge-danger">Désactivé</span>
                        @endif
                    </td>
                    <td>{{ $u->telephone ?? 'Non renseigné' }}</td>
                    <td>{{ $u->created_at->format('d/m/Y') }}</td>
                    <td>
    <form action="{{ route('admin.users.toggle', $u->id) }}"
          method="POST"
          style="margin: 0; display: inline;">
        @csrf


    <button type="submit"
            class="btn btn-secondary btn-sm"
            {{ $u->id === auth()->id() ? 'disabled' : '' }}>
        {{ $u->statut_compte === 'actif' ? 'Désactiver' : 'Activer' }}
    </button>
</form>

<form action="{{ route('admin.users.destroy', $u->id) }}"
      method="POST"
      style="margin: 0; display: inline;"
      onsubmit="return confirm('Voulez-vous vraiment supprimer définitivement cet utilisateur ?');">
    @csrf
    @method('DELETE')

    <button type="submit"
            class="btn btn-danger btn-sm"
            {{ $u->id === auth()->id() ? 'disabled' : '' }}>
        Supprimer
    </button>
</form>


</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $users->links() }}
    </div>
</div>

<!-- Modal Création Utilisateur -->
<div id="createUserModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Créer un Nouvel Utilisateur</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeCreateUserModal()">✕</button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nom Complet</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Karim Benjelloun" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse Email</label>
                    <input type="email" name="email" class="form-control" placeholder="karim@edu.ma" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mot de passe (hashé directement par Bcrypt en BDD)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Rôle</label>
                    <select name="role" class="form-select" required>
                        <option value="admin">Administrateur</option>
                        <option value="enseignant">Enseignant</option>
                        <option value="stagiaire" selected>Stagiaire</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control" placeholder="+212 6 XX XX XX XX">
                </div>
                <div class="form-group">
                    <label class="form-label">Photo de profil</label>

                    <input
                        type="file"
                        name="avatar_file"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                     >

                    <small style="color: var(--text-muted);">
                          Formats acceptés : JPG, PNG, WEBP, GIF. Taille maximale : 3 Mo.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCreateUserModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer dans la Base de Données</button>
            </div>
        </form>
        
    </div>
</div>


<script>
    function openCreateUserModal() {
        document.getElementById('createUserModal').classList.add('active');
    }
    function closeCreateUserModal() {
        document.getElementById('createUserModal').classList.remove('active');
    }
</script>
@endsection

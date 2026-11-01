@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.85rem; font-weight: 800;">Mon Profil Utilisateur</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Consultez vos informations personnelles et mettez à jour votre photo de profil
        </p>
    </div>

    <!-- Carte Profil & Modification Photo -->
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div style="position: relative;">
                <img id="avatarPreview" src="{{ $user->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($user->name) }}" 
                     alt="Photo de profil" 
                     class="user-avatar" 
                     style="width: 96px; height: 96px; border-width: 3px; box-shadow: var(--shadow-md);">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0;">{{ $user->name }}</h2>
                    @if($user->isAdmin())
                        <span class="badge badge-info">Administrateur</span>
                    @elseif($user->isEnseignant())
                        <span class="badge badge-primary">Formateur / Enseignant</span>
                    @else
                        <span class="badge badge-success">Stagiaire</span>
                    @endif
                </div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.3rem;">
                    {{ $user->email }} • {{ $user->telephone ?? 'Téléphone non renseigné' }}
                </div>
                @if($stagiaire)
                    <div style="font-size: 0.85rem; color: var(--accent-green); margin-top: 0.3rem; font-weight: 600;">
                        Matricule CNE : <code>{{ $stagiaire->matricule_cne }}</code> • Groupe : <span class="badge badge-primary">{{ $stagiaire->groupe->code_groupe }}</span>
                    </div>
                @endif
            </div>
        </div>

        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">
            Changer ma photo de profil
        </h3>

        <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Téléversement d'un fichier -->
            <div class="form-group">
                <label class="form-label">Importer une image depuis votre appareil (JPG, PNG, WEBP)</label>
                <input type="file" name="avatar_file" id="avatarInput" class="form-control" accept="image/*" onchange="previewImage(event)" style="padding: 0.5rem;">
                <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.4rem;">
                    Taille maximale recommandée : 3 Mo. Formats supportés : JPG, PNG, WEBP.
                </div>
            </div>

            <!-- Avatars prédéfinis modernes -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label">Ou sélectionner un avatar pré-défini</label>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.5rem;">
                    @php
                        $presets = [
                            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
                            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
                            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
                            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
                            'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=150&q=80',
                            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=150&q=80',
                        ];
                    @endphp
                    @foreach($presets as $p)
                        <label style="cursor: pointer;">
                            <input type="radio" name="avatar_preset" value="{{ $p }}" style="display: none;" onchange="selectPreset('{{ $p }}')">
                            <img src="{{ $p }}" class="user-avatar preset-avatar" style="width: 48px; height: 48px; border: 2px solid transparent; transition: var(--transition);">
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">
                    💾 Enregistrer la nouvelle photo
                </button>
            </div>
        </form>
    </div>

    <!-- Informations Complémentaires -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">
            Détails du Compte
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem;">
            <div style="background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-md);">
                <div style="color: var(--text-dim); font-size: 0.75rem;">IDENTIFIANT</div>
                <div style="font-weight: 700; margin-top: 0.2rem;">#{{ $user->id }}</div>
            </div>

            <div style="background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-md);">
                <div style="color: var(--text-dim); font-size: 0.75rem;">STATUT DU COMPTE</div>
                <div style="font-weight: 700; margin-top: 0.2rem; color: var(--success);">
                    {{ strtoupper($user->statut_compte) }}
                </div>
            </div>

            <div style="background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-md);">
                <div style="color: var(--text-dim); font-size: 0.75rem;">MEMBRE DEPUIS</div>
                <div style="font-weight: 700; margin-top: 0.2rem;">{{ $user->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function selectPreset(url) {
        document.getElementById('avatarPreview').src = url;
        document.querySelectorAll('.preset-avatar').forEach(img => {
            img.style.borderColor = 'transparent';
        });
        event.target.closest('label').querySelector('img').style.borderColor = 'var(--primary)';
    }
</script>
@endsection

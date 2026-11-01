@extends('layouts.app')

@section('title', 'Déclarer une Absence Enseignant')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.85rem; font-weight: 800;">Déclaration d'Absence Enseignant</h1>
        <p style="color: var(--text-muted);">
            Tâche 11 : Déclaration anticipée de votre absence et transmission d'une alerte immédiate à l'administration
        </p>
    </div>

    <div class="card">
        <form action="{{ route('enseignant.declarer-absence.store') }}" method="POST">
            @csrf

            <!-- Sélection du Groupe Impacté -->
            <div class="form-group">
                <label class="form-label">Groupe d'étudiants impacté</label>
                <select name="groupe_id" class="form-select" required>
                    <option value="">Sélectionnez le groupe concerné</option>
                    @foreach($groupesAttribués as $eg)
                        <option value="{{ $eg->groupe_id }}">
                            {{ $eg->groupe->code_groupe }} - {{ $eg->groupe->nom_groupe }} ({{ $eg->matiere }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <!-- Date de l'absence -->
                <div class="form-group">
                    <label class="form-label">Date de l'absence prévue</label>
                    <input type="date" name="date_absence" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Séance -->
                <div class="form-group">
                    <label class="form-label">Séance / Horaire</label>
                    <select name="seance_id" class="form-select" required>
                        @foreach($seances as $s)
                            <option value="{{ $s->id }}">{{ $s->code_seance }} ({{ $s->libelle }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Motif de l'absence -->
            <div class="form-group">
                <label class="form-label">Motif de votre absence</label>
                <textarea name="motif" class="form-control" rows="3" placeholder="Ex: Déplacement officiel, participation à un jury de soutenance, impératif médical..." required></textarea>
            </div>

            <!-- Modalités de rattrapage -->
            <div class="form-group">
                <label class="form-label">Proposition de rattrapage (Séance / Date proposée)</label>
                <textarea name="remarques_rattrapage" class="form-control" rows="2" placeholder="Ex: Séance de rattrapage programmée le samedi matin de 09h00 à 11h00 en salle 4..."></textarea>
            </div>

            <!-- Alerte explicative (Tâche 17) -->
            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.85rem;">
                <div style="font-weight: 700; color: var(--info); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
                    <x-icon name="bell" size="16" /> Alerte Administration Immédiate (Tâche 17)
                </div>
                Une notification prioritaire sera instantanément envoyée sur le tableau de bord de la direction et de l'administration pour réaménager les salles et informer les délégués de groupe.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('enseignant.dashboard') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="upload" size="16" /> Envoyer la Déclaration à l'Administration
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

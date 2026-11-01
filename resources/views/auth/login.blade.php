@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div style="max-width: 440px; margin: 3rem auto;">
    <div class="card" style="padding: 2.25rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="{{ asset('images/ofppt-logo.png') }}" alt="Logo OFPPT" class="login-ofppt-logo">
            <h1 style="font-size: 1.65rem; font-weight: 800; margin-bottom: 0.35rem;">Espace de Connexion</h1>
            <p style="color: var(--text-muted); font-size: 0.88rem;">
                Portail Officiel OFPPT • Gestion des Absences & Note de Discipline
            </p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <x-icon name="alert-triangle" size="20" />
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">Adresse Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="exemple@edu.ma" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.82rem;">
                <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; color: var(--text-muted);">
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>
                <span style="color: var(--text-dim);">Accès sécurisé</span>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.95rem;">
                Se connecter
            </button>
        </form>
    </div>
</div>
@endsection

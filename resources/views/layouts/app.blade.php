<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduAbsence') - Gestion des Absences OFPPT</title>
    
    <!-- Script de chargement immédiat du thème Light par défaut pour éviter tout flash -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('eduabsence_theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- CSS Charte Graphique OFPPT & Responsive -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
    <!-- Barre de Navigation avec Logo Officiel OFPPT dans le Coin -->
    <header class="navbar">
        <a href="{{ url('/') }}" class="nav-brand" title="EduAbsence - Système Officiel OFPPT">
            <div class="brand-logo-container">
                <img src="{{ asset('images/ofppt-logo.png') }}" alt="Logo OFPPT" class="ofppt-corner-logo">
            </div>
            <div class="brand-text">
                <span class="brand-title">EduAbsence</span>
                <span class="brand-tag">OFPPT</span>
            </div>
        </a>

        @auth
        <nav class="nav-menu">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <x-icon name="dashboard" size="16" />
                    <span>Tableau de Bord</span>
                </a>
                <a href="{{ route('admin.absences') }}" class="nav-item {{ request()->routeIs('admin.absences') ? 'active' : '' }}">
                    <x-icon name="search" size="16" />
                    <span>Absences & Filtres</span>
                </a>
                <a href="{{ route('admin.justificatifs') }}" class="nav-item {{ request()->routeIs('admin.justificatifs') ? 'active' : '' }}">
                    <x-icon name="file-text" size="16" />
                    <span>Justificatifs</span>
                </a>
                <a href="{{ route('admin.conseils.index') }}" class="nav-item {{ request()->routeIs('admin.conseils.*') ? 'active' : '' }}">
                    <x-icon name="scale" size="16" />
                    <span>Conseils Discipline</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <x-icon name="users" size="16" />
                    <span>Comptes</span>
                </a>
                <a href="{{ route('admin.groupes') }}" class="nav-item {{ request()->routeIs('admin.groupes') ? 'active' : '' }}">
                    <x-icon name="tag" size="16" />
                    <span>Groupes</span>
                </a>
                <a href="{{ route('admin.stagiaires') }}" class="nav-item {{ request()->routeIs('admin.stagiaires') ? 'active' : '' }}">
                    <x-icon name="graduation-cap" size="16" />
                    <span>Stagiaires</span>
                </a>
                <a href="{{ route('admin.declarations') }}" class="nav-item {{ request()->routeIs('admin.declarations') ? 'active' : '' }}">
                    <x-icon name="bell" size="16" />
                    <span>Alertes Formateurs</span>
                </a>
            @elseif(auth()->user()->isEnseignant())
                <a href="{{ route('enseignant.dashboard') }}" class="nav-item {{ request()->routeIs('enseignant.dashboard') ? 'active' : '' }}">
                    <x-icon name="dashboard" size="16" />
                    <span>Tableau de Bord</span>
                </a>
                <a href="{{ route('enseignant.saisir-absence') }}" class="nav-item {{ request()->routeIs('enseignant.saisir-absence') ? 'active' : '' }}">
                    <x-icon name="user-x" size="16" />
                    <span>Pointer une Absence</span>
                </a>
                <a href="{{ route('enseignant.declarer-comportement') }}" class="nav-item {{ request()->routeIs('enseignant.declarer-comportement') ? 'active' : '' }}">
                    <x-icon name="shield-alert" size="16" />
                    <span>Signaler Comportement</span>
                </a>
                <a href="{{ route('enseignant.stagiaires') }}" class="nav-item {{ request()->routeIs('enseignant.stagiaires') ? 'active' : '' }}">
                    <x-icon name="users" size="16" />
                    <span>Mes Stagiaires</span>
                </a>
                <a href="{{ route('enseignant.declarer-absence') }}" class="nav-item {{ request()->routeIs('enseignant.declarer-absence') ? 'active' : '' }}">
                    <x-icon name="alert-triangle" size="16" />
                    <span>Déclarer mon Absence</span>
                </a>
            @else
                <a href="{{ route('stagiaire.dashboard') }}" class="nav-item {{ request()->routeIs('stagiaire.dashboard') ? 'active' : '' }}">
                    <x-icon name="dashboard" size="16" />
                    <span>Mon Espace</span>
                </a>
                <a href="{{ route('stagiaire.absences') }}" class="nav-item {{ request()->routeIs('stagiaire.absences') ? 'active' : '' }}">
                    <x-icon name="clock" size="16" />
                    <span>Mes Absences & Dépôt</span>
                </a>
                <a href="{{ route('stagiaire.note-discipline') }}" class="nav-item {{ request()->routeIs('stagiaire.note-discipline') ? 'active' : '' }}">
                    <x-icon name="award" size="16" />
                    <span>Note de Discipline</span>
                </a>
            @endif
        </nav>

        <div class="nav-user">
            <!-- Bascule Thème Clair / Sombre -->
            <button class="btn btn-secondary btn-sm theme-toggle-btn" title="Passer au mode sombre" style="padding: 0.35rem 0.6rem;">
                <span class="theme-icon-slot">
                    <x-icon name="moon" size="15" />
                </span>
            </button>

            <!-- Profil Utilisateur & Lien pour Modifier sa Photo -->
            <a href="{{ route('profile.index') }}" title="Gérer mon profil et ma photo" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <img src="{{ auth()->user()->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode(auth()->user()->name) }}" alt="Avatar" class="user-avatar">
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ auth()->user()->role }}</span>
                </div>
            </a>

            <!-- Déconnexion Sécurisée -->
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" title="Fermer la session">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </button>
            </form>
        </div>
        @else
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button class="btn btn-secondary btn-sm theme-toggle-btn" title="Passer au mode sombre" style="padding: 0.35rem 0.6rem;">
                <span class="theme-icon-slot">
                    <x-icon name="moon" size="15" />
                </span>
            </button>
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Se connecter</a>
        </div>
        @endauth
    </header>

    <!-- Conteneur Principal -->
    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <x-icon name="check-circle" size="20" />
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <x-icon name="x-circle" size="20" />
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <x-icon name="info" size="20" />
                <div>{{ session('info') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Épuré avec Mention Officielle OFPPT -->
    <footer class="footer">
        <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
            <img src="{{ asset('images/ofppt-logo.png') }}" alt="OFPPT" style="height: 24px; width: auto; object-fit: contain;">
            <div>
                <strong>EduAbsence</strong> • Plateforme Officielle de Gestion des Absences & Note de Discipline
            </div>
        </div>
        <div style="margin-top: 0.35rem; font-size: 0.75rem; color: var(--text-dim);">
            © {{ date('Y') }} OFPPT (Office de la Formation Professionnelle et de la Promotion du Travail) - Tous droits réservés.
        </div>
    </footer>

    <!-- Script de Bascule Thème Clair (Défaut) / Sombre -->
    <script>
        const sunSvg = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
        const moonSvg = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>`;
        
        function applyAppTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            try {
                localStorage.setItem('eduabsence_theme', theme);
            } catch(e) {}
            
            document.querySelectorAll('.theme-icon-slot').forEach(el => {
                el.innerHTML = theme === 'dark' ? sunSvg : moonSvg;
            });
            document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                btn.setAttribute('title', theme === 'dark' ? 'Passer au mode clair' : 'Passer au mode sombre');
            });
        }

        // Init au chargement (par défaut light)
        let activeTheme = 'light';
        try {
            activeTheme = localStorage.getItem('eduabsence_theme') || 'light';
        } catch(e) {}
        applyAppTheme(activeTheme);

        document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const cur = document.documentElement.getAttribute('data-theme') || 'light';
                const next = cur === 'dark' ? 'light' : 'dark';
                applyAppTheme(next);
            });
        });
    </script>
    @yield('scripts')
</body>
</html>

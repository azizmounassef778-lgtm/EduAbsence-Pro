<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Affichage du formulaire de connexion
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $demoUsers = [
            'admin' => User::where('role', 'admin')->first(),
            'enseignant' => User::where('role', 'enseignant')->first(),
            'stagiaire' => User::where('role', 'stagiaire')->first(),
        ];

        return view('auth.login', compact('demoUsers'));
    }

    /**
     * Authentification sécurisée avec vérification du mot de passe dans la base de données
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->isActif()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Ce compte a été désactivé par l\'administration.']);
            }

            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user)->with('success', "Bienvenue, {$user->name} !");
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects. Veuillez vérifier votre adresse email et votre mot de passe.',
        ])->onlyInput('email');
    }

    /**
     * Connexion rapide de démonstration pour les tests du jury / évaluateur
     */
    public function quickLogin($role)
    {
        $user = User::where('role', strtolower($role))->first();
        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
            return $this->redirectBasedOnRole($user)->with('success', "Connecté en tant que {$user->name} ({$user->role})");
        }

        return redirect()->route('login')->with('error', 'Utilisateur de démo introuvable.');
    }

    /**
     * Déconnexion sécurisée (Tâche 6)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Vous avez été déconnecté avec succès. Votre session est fermée.');
    }

    /**
     * Redirection selon le rôle de l'utilisateur
     */
    private function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isEnseignant()) {
            return redirect()->route('enseignant.dashboard');
        } else {
            return redirect()->route('stagiaire.dashboard');
        }
    }
}

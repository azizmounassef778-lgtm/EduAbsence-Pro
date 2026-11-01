<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = auth()->user();

        if (!$user->isActif()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Votre compte a été désactivé par l\'administrateur.');
        }

        if (!in_array(strtolower($user->role), array_map('strtolower', $roles))) {
            // Rediriger vers l'espace correspondant au rôle de l'utilisateur
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Accès non autorisé.');
            } elseif ($user->isEnseignant()) {
                return redirect()->route('enseignant.dashboard')->with('error', 'Accès non autorisé.');
            } else {
                return redirect()->route('stagiaire.dashboard')->with('error', 'Accès non autorisé.');
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Affichage du profil utilisateur
     */
    public function index()
    {
        $user = Auth::user();
        $stagiaire = $user->isStagiaire() ? $user->stagiaireProfile()->with('groupe')->first() : null;

        return view('profile.index', compact('user', 'stagiaire'));
    }

    /**
     * Mise à jour de la photo de profil (Avatar)
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072', // max 3 Mo
            'avatar_preset' => 'nullable|string',
        ], [
            'avatar_file.image' => 'Le fichier doit être une image valide.',
            'avatar_file.mimes' => 'Formats acceptés : JPG, PNG, WEBP, GIF.',
            'avatar_file.max' => 'La taille maximale autorisée est de 3 Mo.',
        ]);

        $user = Auth::user();

        // 1. Cas téléversement d'une nouvelle photo
        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            $destination = public_path('uploads/avatars');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $fileName);
            $user->avatar = asset('uploads/avatars/' . $fileName);
            $user->save();

            return back()->with('success', 'Votre photo de profil a été mise à jour avec succès !');
        }

        // 2. Cas choix d'un avatar pré-défini
        if ($request->filled('avatar_preset')) {
            $user->avatar = $request->avatar_preset;
            $user->save();

            return back()->with('success', 'Votre avatar a été mis à jour avec succès !');
        }

        return back()->with('error', 'Veuillez sélectionner une image à téléverser ou choisir un avatar.');
    }
}

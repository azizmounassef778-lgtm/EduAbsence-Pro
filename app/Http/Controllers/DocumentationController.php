<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Tâches 1, 2, 3 du Sprint 1 : Cahier des charges, MCD/MLD, Script SQL DDL
     * et Product Backlog complet des 22 tâches
     */
    public function index()
    {
        return view('docs.index');
    }

    /**
     * Téléchargement du script SQL DDL
     */
    public function downloadSql()
    {
        $sqlPath = public_path('sql/db_gestion_absences.sql');
        if (file_exists($sqlPath)) {
            return response()->download($sqlPath, 'db_gestion_absences.sql', [
                'Content-Type' => 'text/plain',
            ]);
        }

        return back()->with('error', 'Fichier SQL introuvable.');
    }
}

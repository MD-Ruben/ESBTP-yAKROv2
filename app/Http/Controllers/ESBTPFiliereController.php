<?php

namespace App\Http\Controllers;

use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ESBTPFiliereController extends Controller
{
    /**
     * Affiche la liste des filières.
     *
     * Cette méthode récupère toutes les filières principales (sans parent)
     * et leurs options (filières enfants) pour les afficher dans une liste.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filieres = ESBTPFiliere::with(['parent', 'options', 'niveauxEtudes'])->get();
        return view('esbtp.filieres.index', compact('filieres'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle filière.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Récupérer toutes les filières pour le select de filière parente
        $filieres = ESBTPFiliere::all();

        // Récupérer tous les niveaux d'études
        $niveauxEtudes = ESBTPNiveauEtude::all();

        return view('esbtp.filieres.create', compact('filieres', 'niveauxEtudes'));
    }

    /**
     * Enregistre une nouvelle filière dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:esbtp_filieres,code',
            'description' => 'nullable|string',
            'option_filiere' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'niveau_etude_ids' => 'nullable|array',
            'niveau_etude_ids.*' => 'exists:esbtp_niveau_etudes,id'
        ]);

        // Créer la filière
        $filiere = ESBTPFiliere::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'option_filiere' => $validated['option_filiere'] ?? null,
            'is_active' => isset($validated['is_active']) ? true : false,
        ]);

        // Associer les niveaux d'études si spécifiés
        if (isset($validated['niveau_etude_ids']) && is_array($validated['niveau_etude_ids'])) {
            $filiere->niveauxEtudes()->attach($validated['niveau_etude_ids']);
        }

        return redirect()->route('esbtp.filieres.index')
            ->with('success', 'Filière créée avec succès.');
    }

    /**
     * Affiche les détails d'une filière spécifique.
     *
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPFiliere $filiere)
    {
        // Charger les relations
        $filiere->load(['parent', 'options', 'niveauxEtudes']);

        return view('esbtp.filieres.show', compact('filiere'));
    }

    /**
     * Affiche le formulaire de modification d'une filière.
     *
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPFiliere $filiere)
    {
        // Récupérer toutes les filières sauf celle en cours d'édition
        $filieres = ESBTPFiliere::where('id', '!=', $filiere->id)->get();

        // Récupérer tous les niveaux d'études
        $niveauxEtudes = ESBTPNiveauEtude::all();

        // Charger les relations nécessaires
        $filiere->load(['niveauxEtudes', 'options', 'classes']);

        return view('esbtp.filieres.edit', compact('filiere', 'filieres', 'niveauxEtudes'));
    }

    /**
     * Met à jour la filière spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPFiliere $filiere)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:esbtp_filieres,code,' . $filiere->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:esbtp_filieres,id',
            'is_active' => 'sometimes|boolean',
            'niveau_etude_ids' => 'nullable|array',
            'niveau_etude_ids.*' => 'exists:esbtp_niveau_etudes,id'
        ]);

        // Mettre à jour la filière
        $filiere->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => isset($validated['is_active']) ? true : false,
        ]);

        // Mettre à jour les niveaux d'études associés
        if (isset($validated['niveau_etude_ids'])) {
            $filiere->niveauxEtudes()->sync($validated['niveau_etude_ids']);
        } else {
            $filiere->niveauxEtudes()->detach();
        }

        return redirect()->route('esbtp.filieres.index')
            ->with('success', 'Filière mise à jour avec succès.');
    }

    /**
     * Supprime la filière spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPFiliere $filiere)
    {
        $filiere->delete();
        return redirect()->route('esbtp.filieres.index')
            ->with('success', 'Filière supprimée avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ESBTPMatiere;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPFormation;
use App\Models\ESBTPUniteEnseignement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ESBTPMatiereController extends Controller
{
    /**
     * Affiche la liste des matières.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $matieres = ESBTPMatiere::with(['uniteEnseignement', 'filieres', 'niveaux'])->get();
        
        return view('esbtp.matieres.index', compact('matieres'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle matière.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $filieres = ESBTPFiliere::all();
        $niveaux = ESBTPNiveauEtude::all();
        $formations = ESBTPFormation::all();
        $unitesEnseignement = ESBTPUniteEnseignement::all();
        
        return view('esbtp.matieres.create', compact('filieres', 'niveaux', 'formations', 'unitesEnseignement'));
    }

    /**
     * Enregistre une nouvelle matière dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_matieres,code',
            'description' => 'nullable|string',
            'unite_enseignement_id' => 'nullable|exists:esbtp_unites_enseignement,id',
            'coefficient_default' => 'required|numeric|min:0',
            'total_heures_default' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'filiere_ids' => 'nullable|array',
            'filiere_ids.*' => 'exists:esbtp_filieres,id',
            'niveau_ids' => 'nullable|array',
            'niveau_ids.*' => 'exists:esbtp_niveau_etudes,id',
        ]);
        
        // Ajouter l'identifiant de l'utilisateur courant
        $validatedData['created_by'] = Auth::id();
        $validatedData['updated_by'] = Auth::id();
        
        // Créer la nouvelle matière
        $matiere = ESBTPMatiere::create($validatedData);
        
        // Attacher les filières si présentes
        if ($request->has('filiere_ids')) {
            $matiere->filieres()->attach($request->filiere_ids);
        }
        
        // Attacher les niveaux d'études si présents
        if ($request->has('niveau_ids')) {
            $matiere->niveaux()->attach($request->niveau_ids);
        }
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.matieres.index')
            ->with('success', 'La matière a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une matière spécifique.
     *
     * @param  \App\Models\ESBTPMatiere  $matiere
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPMatiere $matiere)
    {
        // Charger les relations
        $matiere->load(['uniteEnseignement', 'filieres', 'niveaux', 'createdBy', 'updatedBy']);
        
        return view('esbtp.matieres.show', compact('matiere'));
    }

    /**
     * Affiche le formulaire de modification d'une matière.
     *
     * @param  \App\Models\ESBTPMatiere  $matiere
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPMatiere $matiere)
    {
        // Charger les relations
        $matiere->load(['uniteEnseignement', 'filieres', 'niveaux']);
        
        // Récupérer les données pour les listes déroulantes
        $filieres = ESBTPFiliere::all();
        $niveaux = ESBTPNiveauEtude::all();
        $formations = ESBTPFormation::all();
        $unitesEnseignement = ESBTPUniteEnseignement::all();
        
        // Obtenir les IDs actuellement associés
        $filiereIds = $matiere->filieres->pluck('id')->toArray();
        $niveauIds = $matiere->niveaux->pluck('id')->toArray();
        
        return view('esbtp.matieres.edit', compact(
            'matiere', 
            'filieres', 
            'niveaux', 
            'formations',
            'unitesEnseignement',
            'filiereIds', 
            'niveauIds'
        ));
    }

    /**
     * Met à jour la matière spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPMatiere  $matiere
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPMatiere $matiere)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_matieres,code,' . $matiere->id,
            'description' => 'nullable|string',
            'unite_enseignement_id' => 'nullable|exists:esbtp_unites_enseignement,id',
            'coefficient_default' => 'required|numeric|min:0',
            'total_heures_default' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'filiere_ids' => 'nullable|array',
            'filiere_ids.*' => 'exists:esbtp_filieres,id',
            'niveau_ids' => 'nullable|array',
            'niveau_ids.*' => 'exists:esbtp_niveau_etudes,id',
        ]);
        
        // Ajouter l'identifiant de l'utilisateur courant
        $validatedData['updated_by'] = Auth::id();
        
        // Mettre à jour la matière
        $matiere->update($validatedData);
        
        // Synchroniser les filières
        if ($request->has('filiere_ids')) {
            $matiere->filieres()->sync($request->filiere_ids);
        } else {
            $matiere->filieres()->detach();
        }
        
        // Synchroniser les niveaux d'études
        if ($request->has('niveau_ids')) {
            $matiere->niveaux()->sync($request->niveau_ids);
        } else {
            $matiere->niveaux()->detach();
        }
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.matieres.index')
            ->with('success', 'La matière a été mise à jour avec succès.');
    }

    /**
     * Supprime la matière spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPMatiere  $matiere
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPMatiere $matiere)
    {
        // Détacher toutes les relations
        $matiere->filieres()->detach();
        $matiere->niveaux()->detach();
        $matiere->classes()->detach();
        $matiere->enseignants()->detach();
        
        // Supprimer la matière
        $matiere->delete();
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.matieres.index')
            ->with('success', 'La matière a été supprimée avec succès.');
    }
} 
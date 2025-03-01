<?php

namespace App\Http\Controllers;

use App\Models\ESBTPFormation;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPMatiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ESBTPFormationController extends Controller
{
    /**
     * Affiche la liste des formations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $formations = ESBTPFormation::with(['filieres', 'niveauxEtudes', 'matieres'])->get();
        
        return view('esbtp.formations.index', compact('formations'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle formation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $filieres = ESBTPFiliere::all();
        $niveaux = ESBTPNiveauEtude::all();
        $matieres = ESBTPMatiere::all();
        
        return view('esbtp.formations.create', compact('filieres', 'niveaux', 'matieres'));
    }

    /**
     * Enregistre une nouvelle formation dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:esbtp_formations,name',
            'code' => 'required|string|max:50|unique:esbtp_formations,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'filiere_ids' => 'nullable|array',
            'filiere_ids.*' => 'exists:esbtp_filieres,id',
            'niveau_ids' => 'nullable|array',
            'niveau_ids.*' => 'exists:esbtp_niveau_etudes,id',
            'matiere_ids' => 'nullable|array',
            'matiere_ids.*' => 'exists:esbtp_matieres,id',
        ]);
        
        // Ajouter l'identifiant de l'utilisateur courant
        $validatedData['created_by'] = Auth::id();
        $validatedData['updated_by'] = Auth::id();
        
        // Créer la nouvelle formation
        $formation = ESBTPFormation::create($validatedData);
        
        // Attacher les filières si présentes
        if ($request->has('filiere_ids')) {
            $formation->filieres()->attach($request->filiere_ids);
        }
        
        // Attacher les niveaux d'études si présents
        if ($request->has('niveau_ids')) {
            $formation->niveauxEtudes()->attach($request->niveau_ids);
        }
        
        // Attacher les matières si présentes
        if ($request->has('matiere_ids')) {
            $formation->matieres()->attach($request->matiere_ids);
        }
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.formations.index')
            ->with('success', 'La formation a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une formation spécifique.
     *
     * @param  \App\Models\ESBTPFormation  $formation
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPFormation $formation)
    {
        // Charger les relations
        $formation->load(['filieres', 'niveauxEtudes', 'matieres', 'createdBy', 'updatedBy']);
        
        return view('esbtp.formations.show', compact('formation'));
    }

    /**
     * Affiche le formulaire de modification d'une formation.
     *
     * @param  \App\Models\ESBTPFormation  $formation
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPFormation $formation)
    {
        // Charger les relations
        $formation->load(['filieres', 'niveauxEtudes', 'matieres']);
        
        // Récupérer les données pour les listes déroulantes
        $filieres = ESBTPFiliere::all();
        $niveaux = ESBTPNiveauEtude::all();
        $matieres = ESBTPMatiere::all();
        
        // Obtenir les IDs actuellement associés
        $filiereIds = $formation->filieres->pluck('id')->toArray();
        $niveauIds = $formation->niveauxEtudes->pluck('id')->toArray();
        $matiereIds = $formation->matieres->pluck('id')->toArray();
        
        return view('esbtp.formations.edit', compact('formation', 'filieres', 'niveaux', 'matieres', 'filiereIds', 'niveauIds', 'matiereIds'));
    }

    /**
     * Met à jour la formation spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPFormation  $formation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPFormation $formation)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:esbtp_formations,name,' . $formation->id,
            'code' => 'required|string|max:50|unique:esbtp_formations,code,' . $formation->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'filiere_ids' => 'nullable|array',
            'filiere_ids.*' => 'exists:esbtp_filieres,id',
            'niveau_ids' => 'nullable|array',
            'niveau_ids.*' => 'exists:esbtp_niveau_etudes,id',
            'matiere_ids' => 'nullable|array',
            'matiere_ids.*' => 'exists:esbtp_matieres,id',
        ]);
        
        // Ajouter l'identifiant de l'utilisateur courant
        $validatedData['updated_by'] = Auth::id();
        
        // Mettre à jour la formation
        $formation->update($validatedData);
        
        // Synchroniser les filières
        if ($request->has('filiere_ids')) {
            $formation->filieres()->sync($request->filiere_ids);
        } else {
            $formation->filieres()->detach();
        }
        
        // Synchroniser les niveaux d'études
        if ($request->has('niveau_ids')) {
            $formation->niveauxEtudes()->sync($request->niveau_ids);
        } else {
            $formation->niveauxEtudes()->detach();
        }
        
        // Synchroniser les matières
        if ($request->has('matiere_ids')) {
            $formation->matieres()->sync($request->matiere_ids);
        } else {
            $formation->matieres()->detach();
        }
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.formations.index')
            ->with('success', 'La formation a été mise à jour avec succès.');
    }

    /**
     * Supprime la formation spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPFormation  $formation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPFormation $formation)
    {
        // Détacher toutes les relations
        $formation->filieres()->detach();
        $formation->niveauxEtudes()->detach();
        $formation->matieres()->detach();
        
        // Supprimer la formation
        $formation->delete();
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.formations.index')
            ->with('success', 'La formation a été supprimée avec succès.');
    }
} 
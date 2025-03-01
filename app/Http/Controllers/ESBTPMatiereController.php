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
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'heures_cm' => 'required|integer|min:0',
            'heures_td' => 'required|integer|min:0',
            'heures_tp' => 'required|integer|min:0',
            'heures_stage' => 'required|integer|min:0',
            'heures_perso' => 'required|integer|min:0',
            'niveau_etude_id' => 'nullable|exists:esbtp_niveau_etudes,id',
            'filiere_id' => 'nullable|exists:esbtp_filieres,id',
            'type_formation' => 'required|in:generale,technologique_professionnelle',
            'couleur' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);
        
        // Ajouter l'identifiant de l'utilisateur courant
        $validatedData['created_by'] = Auth::id();
        $validatedData['updated_by'] = Auth::id();
        
        // Créer la nouvelle matière
        $matiere = ESBTPMatiere::create($validatedData);
        
        // Attacher les filières si présentes
        if ($request->has('filiere_id')) {
            $matiere->filieres()->attach($request->filiere_id);
        }
        
        // Attacher les niveaux d'études si présents
        if ($request->has('niveau_etude_id')) {
            $matiere->niveaux()->attach($request->niveau_etude_id);
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
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'heures_cm' => 'required|integer|min:0',
            'heures_td' => 'required|integer|min:0',
            'heures_tp' => 'required|integer|min:0',
            'heures_stage' => 'required|integer|min:0',
            'heures_perso' => 'required|integer|min:0',
            'niveau_etude_id' => 'nullable|exists:esbtp_niveau_etudes,id',
            'filiere_id' => 'nullable|exists:esbtp_filieres,id',
            'type_formation' => 'required|in:generale,technologique_professionnelle',
            'couleur' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);
        
        // Ajouter l'identifiant de l'utilisateur courant
        $validatedData['updated_by'] = Auth::id();
        
        // Mettre à jour la matière
        $matiere->update($validatedData);
        
        // Synchroniser les filières
        if ($request->has('filiere_id')) {
            $matiere->filieres()->sync($request->filiere_id);
        } else {
            $matiere->filieres()->detach();
        }
        
        // Synchroniser les niveaux d'études
        if ($request->has('niveau_etude_id')) {
            $matiere->niveaux()->sync($request->niveau_etude_id);
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
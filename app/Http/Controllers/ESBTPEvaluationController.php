<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPEtudiant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ESBTPEvaluationController extends Controller
{
    /**
     * Affiche la liste des évaluations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evaluations = ESBTPEvaluation::with(['classe', 'matiere', 'user'])
            ->orderBy('date', 'desc')
            ->get();
        
        return view('esbtp.evaluations.index', compact('evaluations'));
    }

    /**
     * Affiche le formulaire de création d'une évaluation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $matieres = ESBTPMatiere::orderBy('nom')->get();
        
        return view('esbtp.evaluations.create', compact('classes', 'matieres'));
    }

    /**
     * Enregistre une nouvelle évaluation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:devoir,examen,projet,tp,controle',
            'date' => 'required|date',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'matiere_id' => 'required|exists:esbtp_matieres,id',
            'coefficient' => 'required|numeric|min:0',
            'bareme' => 'required|numeric|min:0',
            'duree' => 'nullable|integer|min:0',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'type.required' => 'Le type d\'évaluation est obligatoire',
            'date.required' => 'La date est obligatoire',
            'classe_id.required' => 'La classe est obligatoire',
            'matiere_id.required' => 'La matière est obligatoire',
            'coefficient.required' => 'Le coefficient est obligatoire',
            'bareme.required' => 'Le barème est obligatoire',
        ]);

        try {
            $evaluation = new ESBTPEvaluation();
            $evaluation->titre = $request->titre;
            $evaluation->description = $request->description;
            $evaluation->type = $request->type;
            $evaluation->date = $request->date;
            $evaluation->coefficient = $request->coefficient;
            $evaluation->bareme = $request->bareme;
            $evaluation->duree = $request->duree;
            $evaluation->classe_id = $request->classe_id;
            $evaluation->matiere_id = $request->matiere_id;
            $evaluation->user_id = Auth::id();
            $evaluation->save();
            
            return redirect()->route('evaluations.show', $evaluation)
                ->with('success', 'L\'évaluation a été créée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'évaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche une évaluation spécifique.
     *
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPEvaluation $evaluation)
    {
        $evaluation->load(['classe', 'matiere', 'user', 'notes.etudiant']);
        
        // Récupérer les étudiants qui n'ont pas encore de note pour cette évaluation
        $etudiantsAvecNote = $evaluation->notes->pluck('etudiant_id')->toArray();
        $etudiantsSansNote = ESBTPEtudiant::whereHas('inscriptions', function($query) use ($evaluation) {
                $query->where('classe_id', $evaluation->classe_id);
            })
            ->whereNotIn('id', $etudiantsAvecNote)
            ->orderBy('nom')
            ->get();
        
        return view('esbtp.evaluations.show', compact('evaluation', 'etudiantsSansNote'));
    }

    /**
     * Affiche le formulaire de modification d'une évaluation.
     *
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPEvaluation $evaluation)
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $matieres = ESBTPMatiere::orderBy('nom')->get();
        
        return view('esbtp.evaluations.edit', compact('evaluation', 'classes', 'matieres'));
    }

    /**
     * Met à jour une évaluation spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPEvaluation $evaluation)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:devoir,examen,projet,tp,controle',
            'date' => 'required|date',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'matiere_id' => 'required|exists:esbtp_matieres,id',
            'coefficient' => 'required|numeric|min:0',
            'bareme' => 'required|numeric|min:0',
            'duree' => 'nullable|integer|min:0',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'type.required' => 'Le type d\'évaluation est obligatoire',
            'date.required' => 'La date est obligatoire',
            'classe_id.required' => 'La classe est obligatoire',
            'matiere_id.required' => 'La matière est obligatoire',
            'coefficient.required' => 'Le coefficient est obligatoire',
            'bareme.required' => 'Le barème est obligatoire',
        ]);

        try {
            $hasNotes = $evaluation->notes()->count() > 0;
            
            // Si l'évaluation a déjà des notes et que l'utilisateur essaie de changer la classe ou la matière
            if ($hasNotes && ($evaluation->classe_id != $request->classe_id || $evaluation->matiere_id != $request->matiere_id)) {
                return redirect()->back()
                    ->with('error', 'Impossible de modifier la classe ou la matière car des notes sont déjà associées à cette évaluation')
                    ->withInput();
            }
            
            $evaluation->titre = $request->titre;
            $evaluation->description = $request->description;
            $evaluation->type = $request->type;
            $evaluation->date = $request->date;
            $evaluation->coefficient = $request->coefficient;
            $evaluation->bareme = $request->bareme;
            $evaluation->duree = $request->duree;
            
            // Mettre à jour la classe et la matière uniquement s'il n'y a pas de notes
            if (!$hasNotes) {
                $evaluation->classe_id = $request->classe_id;
                $evaluation->matiere_id = $request->matiere_id;
            }
            
            $evaluation->save();
            
            return redirect()->route('evaluations.show', $evaluation)
                ->with('success', 'L\'évaluation a été mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'évaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime une évaluation spécifique.
     *
     * @param  \App\Models\ESBTPEvaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPEvaluation $evaluation)
    {
        try {
            // Vérifier si l'évaluation a des notes
            if ($evaluation->notes()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer cette évaluation car des notes sont associées');
            }
            
            $evaluation->delete();
            return redirect()->route('evaluations.index')
                ->with('success', 'L\'évaluation a été supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'évaluation: ' . $e->getMessage());
        }
    }
} 
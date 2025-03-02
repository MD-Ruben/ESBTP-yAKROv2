<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPNote;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ESBTPAnneeUniversitaire;

class ESBTPNoteController extends Controller
{
    /**
     * Affiche la liste des notes avec filtre par classe et matière
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $classe_id = $request->input('classe_id');
        $matiere_id = $request->input('matiere_id');
        
        $query = ESBTPNote::with(['etudiant', 'matiere', 'evaluation']);
        
        if ($classe_id) {
            $query->whereHas('etudiant', function ($q) use ($classe_id) {
                $q->where('classe_id', $classe_id);
            });
        }
        
        if ($matiere_id) {
            $query->where('matiere_id', $matiere_id);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->get();
        $classes = ESBTPClasse::orderBy('nom')->get();
        $matieres = ESBTPMatiere::orderBy('nom')->get();
        
        return view('esbtp.notes.index', compact('notes', 'classes', 'matieres', 'classe_id', 'matiere_id'));
    }

    /**
     * Affiche le formulaire de création d'une note.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $evaluations = ESBTPEvaluation::with(['classe', 'matiere'])
            ->orderBy('date', 'desc')
            ->get();
        $etudiants = ESBTPEtudiant::orderBy('nom')->get();
        
        return view('esbtp.notes.create', compact('evaluations', 'etudiants'));
    }

    /**
     * Enregistre une nouvelle note.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'evaluation_id' => 'required|exists:esbtp_evaluations,id',
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'valeur' => 'required|numeric|min:0',
            'commentaire' => 'nullable|string',
        ], [
            'evaluation_id.required' => 'L\'évaluation est obligatoire',
            'etudiant_id.required' => 'L\'étudiant est obligatoire',
            'valeur.required' => 'La valeur de la note est obligatoire',
            'valeur.numeric' => 'La valeur doit être un nombre',
            'valeur.min' => 'La valeur doit être positive',
        ]);

        try {
            // Vérifier que l'étudiant est bien dans la classe associée à l'évaluation
            $evaluation = ESBTPEvaluation::findOrFail($request->evaluation_id);
            $etudiant = ESBTPEtudiant::findOrFail($request->etudiant_id);
            
            $estInscrit = $etudiant->inscriptions()
                ->where('classe_id', $evaluation->classe_id)
                ->where('is_active', true)
                ->exists();
                
            if (!$estInscrit) {
                return redirect()->back()
                    ->with('error', 'L\'étudiant n\'est pas inscrit dans la classe associée à cette évaluation')
                    ->withInput();
            }
            
            // Vérifier que l'étudiant n'a pas déjà une note pour cette évaluation
            $noteExistante = ESBTPNote::where('evaluation_id', $request->evaluation_id)
                ->where('etudiant_id', $request->etudiant_id)
                ->exists();
                
            if ($noteExistante) {
                return redirect()->back()
                    ->with('error', 'L\'étudiant a déjà une note pour cette évaluation')
                    ->withInput();
            }
            
            // Vérifier que la note ne dépasse pas le barème
            if ($request->valeur > $evaluation->bareme) {
                return redirect()->back()
                    ->with('error', 'La note ne peut pas dépasser le barème de l\'évaluation (' . $evaluation->bareme . ')')
                    ->withInput();
            }
            
            $note = new ESBTPNote();
            $note->evaluation_id = $request->evaluation_id;
            $note->etudiant_id = $request->etudiant_id;
            $note->valeur = $request->valeur;
            $note->commentaire = $request->commentaire;
            $note->user_id = Auth::id();
            $note->save();
            
            return redirect()->route('evaluations.show', $note->evaluation_id)
                ->with('success', 'La note a été ajoutée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'ajout de la note: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche une note spécifique.
     *
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPNote $note)
    {
        $note->load(['evaluation.matiere', 'evaluation.classe', 'etudiant', 'user']);
        return view('esbtp.notes.show', compact('note'));
    }

    /**
     * Affiche le formulaire de modification d'une note.
     *
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPNote $note)
    {
        $note->load(['evaluation.matiere', 'evaluation.classe', 'etudiant']);
        return view('esbtp.notes.edit', compact('note'));
    }

    /**
     * Met à jour une note spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPNote $note)
    {
        $request->validate([
            'valeur' => 'required|numeric|min:0',
            'commentaire' => 'nullable|string',
        ], [
            'valeur.required' => 'La valeur de la note est obligatoire',
            'valeur.numeric' => 'La valeur doit être un nombre',
            'valeur.min' => 'La valeur doit être positive',
        ]);

        try {
            // Vérifier que la note ne dépasse pas le barème
            if ($request->valeur > $note->evaluation->bareme) {
                return redirect()->back()
                    ->with('error', 'La note ne peut pas dépasser le barème de l\'évaluation (' . $note->evaluation->bareme . ')')
                    ->withInput();
            }
            
            $note->valeur = $request->valeur;
            $note->commentaire = $request->commentaire;
            $note->save();
            
            return redirect()->route('evaluations.show', $note->evaluation_id)
                ->with('success', 'La note a été mise à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la note: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime une note spécifique.
     *
     * @param  \App\Models\ESBTPNote  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPNote $note)
    {
        try {
            $note->delete();
            return redirect()->route('esbtp.notes.index')->with('success', 'Note supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression de la note: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche la page de saisie rapide des notes pour une évaluation.
     * 
     * @param ESBTPEvaluation $evaluation
     * @return \Illuminate\Http\Response
     */
    public function saisieRapide(ESBTPEvaluation $evaluation)
    {
        $evaluation->load(['classe', 'matiere', 'notes.etudiant']);
        
        // Récupérer tous les étudiants de la classe
        $etudiants = ESBTPEtudiant::whereHas('inscriptions', function($query) use ($evaluation) {
                $query->where('classe_id', $evaluation->classe_id);
            })
            ->with(['notes' => function($query) use ($evaluation) {
                $query->where('evaluation_id', $evaluation->id);
            }])
            ->orderBy('nom')
            ->get();
        
        return view('esbtp.notes.saisie-rapide', compact('evaluation', 'etudiants'));
    }
    
    /**
     * Enregistre les notes saisies en masse pour une évaluation.
     * 
     * @param Request $request
     * @param ESBTPEvaluation $evaluation
     * @return \Illuminate\Http\Response
     */
    public function enregistrerSaisieRapide(Request $request, ESBTPEvaluation $evaluation)
    {
        $request->validate([
            'notes' => 'required|array',
            'notes.*.etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'notes.*.valeur' => 'nullable|numeric|min:0|max:' . $evaluation->bareme,
            'notes.*.commentaire' => 'nullable|string',
        ], [
            'notes.*.valeur.numeric' => 'La valeur doit être un nombre',
            'notes.*.valeur.min' => 'La valeur doit être positive',
            'notes.*.valeur.max' => 'La valeur ne peut pas dépasser le barème de ' . $evaluation->bareme,
        ]);
        
        DB::beginTransaction();
        try {
            foreach ($request->notes as $noteData) {
                // Ignorer les entrées sans valeur
                if (!isset($noteData['valeur']) || $noteData['valeur'] === null || $noteData['valeur'] === '') {
                    continue;
                }
                
                $etudiantId = $noteData['etudiant_id'];
                
                // Vérifier si l'étudiant a déjà une note pour cette évaluation
                $note = ESBTPNote::where('evaluation_id', $evaluation->id)
                    ->where('etudiant_id', $etudiantId)
                    ->first();
                
                if ($note) {
                    // Mise à jour de la note existante
                    $note->valeur = $noteData['valeur'];
                    $note->commentaire = $noteData['commentaire'] ?? null;
                    $note->save();
                } else {
                    // Création d'une nouvelle note
                    $note = new ESBTPNote();
                    $note->evaluation_id = $evaluation->id;
                    $note->etudiant_id = $etudiantId;
                    $note->valeur = $noteData['valeur'];
                    $note->commentaire = $noteData['commentaire'] ?? null;
                    $note->user_id = Auth::id();
                    $note->save();
                }
            }
            
            DB::commit();
            return redirect()->route('evaluations.show', $evaluation)
                ->with('success', 'Les notes ont été enregistrées avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des notes: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les notes de l'étudiant connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function studentGrades(Request $request)
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();
        
        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }
        
        $notes = ESBTPNote::where('etudiant_id', $etudiant->id)
            ->with(['evaluation', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('etudiants.notes', compact('notes', 'etudiant'));
    }

    /**
     * Affiche le formulaire de saisie rapide des notes.
     *
     * @return \Illuminate\Http\Response
     */
    public function saisieRapideForm()
    {
        return view('esbtp.notes.saisie-rapide-form');
    }
} 
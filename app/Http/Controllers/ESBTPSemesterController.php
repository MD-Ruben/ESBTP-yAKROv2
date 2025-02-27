<?php

namespace App\Http\Controllers;

use App\Models\ESBTPSemester;
use App\Models\ESBTPStudyYear;
use Illuminate\Http\Request;

class ESBTPSemesterController extends Controller
{
    /**
     * Affiche la liste des semestres.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer tous les semestres, y compris les supprimés
        $semesters = ESBTPSemester::withTrashed()->with('studyYear')->latest()->get();
        
        return view('esbtp.semesters.index', compact('semesters'));
    }

    /**
     * Affiche le formulaire de création d'un semestre.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Récupérer les années d'études pour la liste déroulante
        $studyYears = ESBTPStudyYear::where('is_active', true)->get();
        
        return view('esbtp.semesters.create', compact('studyYears'));
    }

    /**
     * Enregistre un nouveau semestre dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1',
            'study_year_id' => 'required|exists:esbtp_study_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);
        
        // Créer le semestre
        $semester = ESBTPSemester::create($validated);
        
        return redirect()->route('esbtp.semesters.index')
            ->with('success', 'Semestre créé avec succès.');
    }

    /**
     * Affiche les détails d'un semestre spécifique.
     *
     * @param  \App\Models\ESBTPSemester  $semester
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPSemester $semester)
    {
        // Charger les relations nécessaires
        $semester->load(['studyYear', 'courses']);
        
        return view('esbtp.semesters.show', compact('semester'));
    }

    /**
     * Affiche le formulaire d'édition d'un semestre.
     *
     * @param  \App\Models\ESBTPSemester  $semester
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPSemester $semester)
    {
        // Récupérer les années d'études pour la liste déroulante
        $studyYears = ESBTPStudyYear::all();
        
        return view('esbtp.semesters.edit', compact('semester', 'studyYears'));
    }

    /**
     * Met à jour le semestre spécifié dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPSemester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPSemester $semester)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|integer|min:1',
            'study_year_id' => 'required|exists:esbtp_study_years,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);
        
        // Mettre à jour le semestre
        $semester->update($validated);
        
        return redirect()->route('esbtp.semesters.index')
            ->with('success', 'Semestre mis à jour avec succès.');
    }

    /**
     * Supprime le semestre spécifié (soft delete).
     *
     * @param  \App\Models\ESBTPSemester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPSemester $semester)
    {
        $semester->delete();
        
        return redirect()->route('esbtp.semesters.index')
            ->with('success', 'Semestre supprimé avec succès.');
    }
    
    /**
     * Restaure un semestre précédemment supprimé.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $semester = ESBTPSemester::withTrashed()->findOrFail($id);
        $semester->restore();
        
        return redirect()->route('esbtp.semesters.index')
            ->with('success', 'Semestre restauré avec succès.');
    }
    
    /**
     * Supprime définitivement un semestre de la base de données.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $semester = ESBTPSemester::withTrashed()->findOrFail($id);
        $semester->forceDelete();
        
        return redirect()->route('esbtp.semesters.index')
            ->with('success', 'Semestre supprimé définitivement.');
    }
} 
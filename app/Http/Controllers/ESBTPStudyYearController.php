<?php

namespace App\Http\Controllers;

use App\Models\ESBTPStudyYear;
use App\Models\ESBTPCycle;
use App\Models\ESBTPSpecialty;
use Illuminate\Http\Request;

class ESBTPStudyYearController extends Controller
{
    /**
     * Affiche la liste des années d'études.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer toutes les années d'études, y compris les supprimées
        $studyYears = ESBTPStudyYear::withTrashed()->with(['cycle', 'specialty'])->latest()->get();
        
        return view('esbtp.study-years.index', compact('studyYears'));
    }

    /**
     * Affiche le formulaire de création d'une année d'études.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Récupérer les cycles et spécialités pour les listes déroulantes
        $cycles = ESBTPCycle::where('is_active', true)->get();
        $specialties = ESBTPSpecialty::where('is_active', true)->get();
        
        return view('esbtp.study-years.create', compact('cycles', 'specialties'));
    }

    /**
     * Enregistre une nouvelle année d'études dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'cycle_id' => 'required|exists:esbtp_cycles,id',
            'specialty_id' => 'required|exists:esbtp_specialties,id',
            'num_semesters' => 'required|integer|min:1|max:4',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);
        
        // Créer l'année d'études
        $studyYear = ESBTPStudyYear::create($validated);
        
        // Créer automatiquement les semestres associés
        for ($i = 1; $i <= $validated['num_semesters']; $i++) {
            $studyYear->semesters()->create([
                'name' => 'Semestre ' . $i,
                'number' => $i,
                'is_active' => true,
            ]);
        }
        
        return redirect()->route('esbtp.study-years.index')
            ->with('success', 'Année d\'études créée avec succès.');
    }

    /**
     * Affiche les détails d'une année d'études spécifique.
     *
     * @param  \App\Models\ESBTPStudyYear  $studyYear
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPStudyYear $studyYear)
    {
        // Charger les relations nécessaires
        $studyYear->load(['cycle', 'specialty', 'semesters']);
        
        return view('esbtp.study-years.show', compact('studyYear'));
    }

    /**
     * Affiche le formulaire d'édition d'une année d'études.
     *
     * @param  \App\Models\ESBTPStudyYear  $studyYear
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPStudyYear $studyYear)
    {
        // Récupérer les cycles et spécialités pour les listes déroulantes
        $cycles = ESBTPCycle::all();
        $specialties = ESBTPSpecialty::all();
        
        return view('esbtp.study-years.edit', compact('studyYear', 'cycles', 'specialties'));
    }

    /**
     * Met à jour l'année d'études spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPStudyYear  $studyYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPStudyYear $studyYear)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'cycle_id' => 'required|exists:esbtp_cycles,id',
            'specialty_id' => 'required|exists:esbtp_specialties,id',
            'num_semesters' => 'required|integer|min:1|max:4',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);
        
        // Mettre à jour l'année d'études
        $studyYear->update($validated);
        
        // Gérer les semestres si le nombre a changé
        $currentSemesterCount = $studyYear->semesters()->count();
        $newSemesterCount = $validated['num_semesters'];
        
        if ($newSemesterCount > $currentSemesterCount) {
            // Ajouter des semestres
            for ($i = $currentSemesterCount + 1; $i <= $newSemesterCount; $i++) {
                $studyYear->semesters()->create([
                    'name' => 'Semestre ' . $i,
                    'number' => $i,
                    'is_active' => true,
                ]);
            }
        } elseif ($newSemesterCount < $currentSemesterCount) {
            // Supprimer les semestres excédentaires
            $studyYear->semesters()->where('number', '>', $newSemesterCount)->delete();
        }
        
        return redirect()->route('esbtp.study-years.index')
            ->with('success', 'Année d\'études mise à jour avec succès.');
    }

    /**
     * Supprime l'année d'études spécifiée (soft delete).
     *
     * @param  \App\Models\ESBTPStudyYear  $studyYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPStudyYear $studyYear)
    {
        $studyYear->delete();
        
        return redirect()->route('esbtp.study-years.index')
            ->with('success', 'Année d\'études supprimée avec succès.');
    }
    
    /**
     * Restaure une année d'études précédemment supprimée.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $studyYear = ESBTPStudyYear::withTrashed()->findOrFail($id);
        $studyYear->restore();
        
        return redirect()->route('esbtp.study-years.index')
            ->with('success', 'Année d\'études restaurée avec succès.');
    }
    
    /**
     * Supprime définitivement une année d'études de la base de données.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $studyYear = ESBTPStudyYear::withTrashed()->findOrFail($id);
        
        // Supprimer les semestres associés
        $studyYear->semesters()->forceDelete();
        
        $studyYear->forceDelete();
        
        return redirect()->route('esbtp.study-years.index')
            ->with('success', 'Année d\'études supprimée définitivement.');
    }
} 
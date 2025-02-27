<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPSpecialty;
use App\Models\ESBTPCycle;
use App\Models\Department;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ESBTPSpecialtyController extends Controller
{
    /**
     * Affiche la liste des spécialités
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer les spécialités actives, inactives et archivées
        $activeSpecialties = ESBTPSpecialty::where('is_active', true)->whereNull('deleted_at')->get();
        $inactiveSpecialties = ESBTPSpecialty::where('is_active', false)->whereNull('deleted_at')->get();
        $archivedSpecialties = ESBTPSpecialty::onlyTrashed()->get();
        
        return view('esbtp.specialties.index', compact('activeSpecialties', 'inactiveSpecialties', 'archivedSpecialties'));
    }

    /**
     * Affiche le formulaire de création d'une spécialité
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $cycles = ESBTPCycle::where('is_active', true)->pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        
        return view('esbtp.specialties.create', compact('cycles', 'departments'));
    }

    /**
     * Enregistre une nouvelle spécialité dans la base de données
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:esbtp_specialties,code',
                'cycle_id' => 'required|exists:esbtp_cycles,id',
                'department_id' => 'required|exists:departments,id',
                'coordinator_name' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
                'career_opportunities' => 'nullable|string',
            ]);
            
            // Créer la spécialité
            $specialty = ESBTPSpecialty::create($validatedData);
            
            return redirect()->route('esbtp.specialties.show', $specialty)
                ->with('success', 'La spécialité a été créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la spécialité: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la spécialité.');
        }
    }

    /**
     * Affiche les détails d'une spécialité spécifique
     * 
     * @param  \App\Models\ESBTPSpecialty  $specialty
     * @return \Illuminate\View\View
     */
    public function show(ESBTPSpecialty $specialty)
    {
        // Charger les relations
        $specialty->load(['cycle', 'department', 'studyYears']);
        
        return view('esbtp.specialties.show', compact('specialty'));
    }

    /**
     * Affiche le formulaire de modification d'une spécialité
     * 
     * @param  \App\Models\ESBTPSpecialty  $specialty
     * @return \Illuminate\View\View
     */
    public function edit(ESBTPSpecialty $specialty)
    {
        $cycles = ESBTPCycle::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        
        return view('esbtp.specialties.edit', compact('specialty', 'cycles', 'departments'));
    }

    /**
     * Met à jour une spécialité existante dans la base de données
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPSpecialty  $specialty
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ESBTPSpecialty $specialty)
    {
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:esbtp_specialties,code,' . $specialty->id,
                'cycle_id' => 'required|exists:esbtp_cycles,id',
                'department_id' => 'required|exists:departments,id',
                'coordinator_name' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
                'career_opportunities' => 'nullable|string',
            ]);
            
            // Mettre à jour la spécialité
            $specialty->update($validatedData);
            
            return redirect()->route('esbtp.specialties.show', $specialty)
                ->with('success', 'La spécialité a été mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la spécialité: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la spécialité.');
        }
    }

    /**
     * Supprime temporairement (soft delete) une spécialité
     * 
     * @param  \App\Models\ESBTPSpecialty  $specialty
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ESBTPSpecialty $specialty)
    {
        try {
            $specialty->delete();
            return redirect()->route('esbtp.specialties.index')
                ->with('success', 'La spécialité a été archivée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la spécialité: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'archivage de la spécialité.');
        }
    }

    /**
     * Restaure une spécialité supprimée temporairement
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        try {
            $specialty = ESBTPSpecialty::withTrashed()->findOrFail($id);
            $specialty->restore();
            
            return redirect()->route('esbtp.specialties.index')
                ->with('success', 'La spécialité a été restaurée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration de la spécialité: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la restauration de la spécialité.');
        }
    }

    /**
     * Supprime définitivement une spécialité
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        try {
            $specialty = ESBTPSpecialty::withTrashed()->findOrFail($id);
            
            // Vérifier si la spécialité a des années d'études
            if ($specialty->studyYears()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer cette spécialité car elle possède des années d\'études associées.');
            }
            
            $specialty->forceDelete();
            
            return redirect()->route('esbtp.specialties.index')
                ->with('success', 'La spécialité a été supprimée définitivement.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression définitive de la spécialité: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression définitive de la spécialité.');
        }
    }
}

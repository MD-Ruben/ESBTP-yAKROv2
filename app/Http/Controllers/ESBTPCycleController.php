<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPCycle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ESBTPCycleController extends Controller
{
    /**
     * Affiche la liste des cycles de formation
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer les cycles actifs, inactifs et archivés
        $activeCycles = ESBTPCycle::where('is_active', true)->whereNull('deleted_at')->get();
        $inactiveCycles = ESBTPCycle::where('is_active', false)->whereNull('deleted_at')->get();
        $archivedCycles = ESBTPCycle::onlyTrashed()->get();
        
        return view('esbtp.cycles.index', compact('activeCycles', 'inactiveCycles', 'archivedCycles'));
    }

    /**
     * Affiche le formulaire de création d'un cycle
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('esbtp.cycles.create');
    }

    /**
     * Enregistre un nouveau cycle dans la base de données
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
                'code' => 'required|string|max:50|unique:esbtp_cycles,code',
                'duration' => 'required|integer|min:1|max:10',
                'diploma_awarded' => 'required|string|max:255',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
            ]);
            
            // Créer le cycle
            $cycle = ESBTPCycle::create($validatedData);
            
            return redirect()->route('esbtp.cycles.show', $cycle)
                ->with('success', 'Le cycle de formation a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du cycle: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du cycle.');
        }
    }

    /**
     * Affiche les détails d'un cycle spécifique
     * 
     * @param  \App\Models\ESBTPCycle  $cycle
     * @return \Illuminate\View\View
     */
    public function show(ESBTPCycle $cycle)
    {
        // Charger les spécialités et années d'études associées
        $cycle->load(['specialties', 'studyYears']);
        
        return view('esbtp.cycles.show', compact('cycle'));
    }

    /**
     * Affiche le formulaire de modification d'un cycle
     * 
     * @param  \App\Models\ESBTPCycle  $cycle
     * @return \Illuminate\View\View
     */
    public function edit(ESBTPCycle $cycle)
    {
        return view('esbtp.cycles.edit', compact('cycle'));
    }

    /**
     * Met à jour un cycle existant dans la base de données
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPCycle  $cycle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ESBTPCycle $cycle)
    {
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:esbtp_cycles,code,' . $cycle->id,
                'duration' => 'required|integer|min:1|max:10',
                'diploma_awarded' => 'required|string|max:255',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
            ]);
            
            // Mettre à jour le cycle
            $cycle->update($validatedData);
            
            return redirect()->route('esbtp.cycles.show', $cycle)
                ->with('success', 'Le cycle de formation a été mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du cycle: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du cycle.');
        }
    }

    /**
     * Supprime temporairement (soft delete) un cycle
     * 
     * @param  \App\Models\ESBTPCycle  $cycle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ESBTPCycle $cycle)
    {
        try {
            $cycle->delete();
            return redirect()->route('esbtp.cycles.index')
                ->with('success', 'Le cycle de formation a été archivé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du cycle: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'archivage du cycle.');
        }
    }

    /**
     * Restaure un cycle supprimé temporairement
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        try {
            $cycle = ESBTPCycle::withTrashed()->findOrFail($id);
            $cycle->restore();
            
            return redirect()->route('esbtp.cycles.index')
                ->with('success', 'Le cycle de formation a été restauré avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration du cycle: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la restauration du cycle.');
        }
    }

    /**
     * Supprime définitivement un cycle
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        try {
            $cycle = ESBTPCycle::withTrashed()->findOrFail($id);
            
            // Vérifier si le cycle a des spécialités ou des années d'études
            if ($cycle->specialties()->count() > 0 || $cycle->studyYears()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce cycle car il possède des spécialités ou des années d\'études associées.');
            }
            
            $cycle->forceDelete();
            
            return redirect()->route('esbtp.cycles.index')
                ->with('success', 'Le cycle de formation a été supprimé définitivement.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression définitive du cycle: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression définitive du cycle.');
        }
    }
}

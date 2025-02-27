<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPContinuingEducation;
use App\Models\Department;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ESBTPContinuingEducationController extends Controller
{
    /**
     * Affiche la liste des formations continues
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer les formations continues actives, inactives et archivées
        $activePrograms = ESBTPContinuingEducation::where('is_active', true)->whereNull('deleted_at')->get();
        $inactivePrograms = ESBTPContinuingEducation::where('is_active', false)->whereNull('deleted_at')->get();
        $archivedPrograms = ESBTPContinuingEducation::onlyTrashed()->get();
        
        return view('esbtp.continuing-education.index', compact('activePrograms', 'inactivePrograms', 'archivedPrograms'));
    }

    /**
     * Affiche le formulaire de création d'une formation continue
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = Department::pluck('name', 'id');
        $programTypes = [
            'certificate' => 'Certificat',
            'diploma' => 'Diplôme',
            'short_course' => 'Formation courte',
            'workshop' => 'Atelier',
            'seminar' => 'Séminaire',
            'other' => 'Autre'
        ];
        
        return view('esbtp.continuing-education.create', compact('departments', 'programTypes'));
    }

    /**
     * Enregistre une nouvelle formation continue dans la base de données
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:esbtp_continuing_education,code',
                'type' => 'required|string|max:50',
                'department_id' => 'required|exists:departments,id',
                'coordinator_name' => 'nullable|string|max:255',
                'duration' => 'required|integer|min:1',
                'duration_unit' => 'required|string|in:hours,days,weeks,months',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'price' => 'nullable|numeric|min:0',
                'max_participants' => 'nullable|integer|min:1',
                'image' => 'nullable|image|max:2048',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
                'objectives' => 'nullable|string',
                'target_audience' => 'nullable|string',
                'prerequisites' => 'nullable|string',
                'certification' => 'nullable|string',
            ]);
            
            // Traiter l'image s'il est fourni
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = 'continuing_ed_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('continuing-education/images', $filename, 'public');
                $validatedData['image'] = $path;
            }
            
            // Créer la formation continue
            $program = ESBTPContinuingEducation::create($validatedData);
            
            return redirect()->route('esbtp.continuing-education.show', $program)
                ->with('success', 'La formation continue a été créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la formation continue: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la formation continue.');
        }
    }

    /**
     * Affiche les détails d'une formation continue spécifique
     * 
     * @param  \App\Models\ESBTPContinuingEducation  $continuingEducation
     * @return \Illuminate\View\View
     */
    public function show(ESBTPContinuingEducation $continuingEducation)
    {
        // Charger le département associé
        $continuingEducation->load('department');
        
        return view('esbtp.continuing-education.show', compact('continuingEducation'));
    }

    /**
     * Affiche le formulaire de modification d'une formation continue
     * 
     * @param  \App\Models\ESBTPContinuingEducation  $continuingEducation
     * @return \Illuminate\View\View
     */
    public function edit(ESBTPContinuingEducation $continuingEducation)
    {
        $departments = Department::pluck('name', 'id');
        $programTypes = [
            'certificate' => 'Certificat',
            'diploma' => 'Diplôme',
            'short_course' => 'Formation courte',
            'workshop' => 'Atelier',
            'seminar' => 'Séminaire',
            'other' => 'Autre'
        ];
        
        $durationUnits = [
            'hours' => 'Heures',
            'days' => 'Jours',
            'weeks' => 'Semaines',
            'months' => 'Mois'
        ];
        
        return view('esbtp.continuing-education.edit', compact('continuingEducation', 'departments', 'programTypes', 'durationUnits'));
    }

    /**
     * Met à jour une formation continue existante dans la base de données
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPContinuingEducation  $continuingEducation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ESBTPContinuingEducation $continuingEducation)
    {
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:esbtp_continuing_education,code,' . $continuingEducation->id,
                'type' => 'required|string|max:50',
                'department_id' => 'required|exists:departments,id',
                'coordinator_name' => 'nullable|string|max:255',
                'duration' => 'required|integer|min:1',
                'duration_unit' => 'required|string|in:hours,days,weeks,months',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'price' => 'nullable|numeric|min:0',
                'max_participants' => 'nullable|integer|min:1',
                'image' => 'nullable|image|max:2048',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
                'objectives' => 'nullable|string',
                'target_audience' => 'nullable|string',
                'prerequisites' => 'nullable|string',
                'certification' => 'nullable|string',
            ]);
            
            // Traiter l'image s'il est fourni
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image s'il existe
                if ($continuingEducation->image) {
                    Storage::disk('public')->delete($continuingEducation->image);
                }
                
                $image = $request->file('image');
                $filename = 'continuing_ed_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('continuing-education/images', $filename, 'public');
                $validatedData['image'] = $path;
            }
            
            // Mettre à jour la formation continue
            $continuingEducation->update($validatedData);
            
            return redirect()->route('esbtp.continuing-education.show', $continuingEducation)
                ->with('success', 'La formation continue a été mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la formation continue: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la formation continue.');
        }
    }

    /**
     * Supprime temporairement (soft delete) une formation continue
     * 
     * @param  \App\Models\ESBTPContinuingEducation  $continuingEducation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ESBTPContinuingEducation $continuingEducation)
    {
        try {
            $continuingEducation->delete();
            return redirect()->route('esbtp.continuing-education.index')
                ->with('success', 'La formation continue a été archivée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la formation continue: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'archivage de la formation continue.');
        }
    }

    /**
     * Restaure une formation continue supprimée temporairement
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        try {
            $continuingEducation = ESBTPContinuingEducation::withTrashed()->findOrFail($id);
            $continuingEducation->restore();
            
            return redirect()->route('esbtp.continuing-education.index')
                ->with('success', 'La formation continue a été restaurée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration de la formation continue: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la restauration de la formation continue.');
        }
    }

    /**
     * Supprime définitivement une formation continue
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        try {
            $continuingEducation = ESBTPContinuingEducation::withTrashed()->findOrFail($id);
            
            // Supprimer l'image s'il existe
            if ($continuingEducation->image) {
                Storage::disk('public')->delete($continuingEducation->image);
            }
            
            $continuingEducation->forceDelete();
            
            return redirect()->route('esbtp.continuing-education.index')
                ->with('success', 'La formation continue a été supprimée définitivement.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression définitive de la formation continue: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression définitive de la formation continue.');
        }
    }
}

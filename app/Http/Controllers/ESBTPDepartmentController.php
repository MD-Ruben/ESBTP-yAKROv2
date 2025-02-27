<?php

namespace App\Http\Controllers;

use App\Models\ESBTPDepartment;
use App\Models\ESBTPPartnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ESBTPDepartmentController extends Controller
{
    /**
     * Affiche la liste des départements.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer tous les départements, y compris les supprimés
        $departments = ESBTPDepartment::withTrashed()->latest()->get();
        
        return view('esbtp.departments.index', compact('departments'));
    }

    /**
     * Affiche le formulaire de création d'un département.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('esbtp.departments.create');
    }

    /**
     * Enregistre un nouveau département dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_departments',
            'head_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Gérer le téléchargement du logo si présent
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('departments', 'public');
            $validated['logo'] = $logoPath;
        }
        
        // Créer le département
        $department = ESBTPDepartment::create($validated);
        
        return redirect()->route('esbtp.departments.index')
            ->with('success', 'Département créé avec succès.');
    }

    /**
     * Affiche les détails d'un département spécifique.
     *
     * @param  \App\Models\ESBTPDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPDepartment $department)
    {
        // Charger les relations nécessaires
        $department->load(['specialties', 'continuingEducation']);
        
        return view('esbtp.departments.show', compact('department'));
    }

    /**
     * Affiche le formulaire d'édition d'un département.
     *
     * @param  \App\Models\ESBTPDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPDepartment $department)
    {
        return view('esbtp.departments.edit', compact('department'));
    }

    /**
     * Met à jour le département spécifié dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPDepartment $department)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_departments,code,' . $department->id,
            'head_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Gérer le téléchargement du logo si présent
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo s'il existe
            if ($department->logo) {
                Storage::disk('public')->delete($department->logo);
            }
            
            $logoPath = $request->file('logo')->store('departments', 'public');
            $validated['logo'] = $logoPath;
        }
        
        // Mettre à jour le département
        $department->update($validated);
        
        return redirect()->route('esbtp.departments.index')
            ->with('success', 'Département mis à jour avec succès.');
    }

    /**
     * Supprime le département spécifié (soft delete).
     *
     * @param  \App\Models\ESBTPDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPDepartment $department)
    {
        $department->delete();
        
        return redirect()->route('esbtp.departments.index')
            ->with('success', 'Département supprimé avec succès.');
    }
    
    /**
     * Restaure un département précédemment supprimé.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $department = ESBTPDepartment::withTrashed()->findOrFail($id);
        $department->restore();
        
        return redirect()->route('esbtp.departments.index')
            ->with('success', 'Département restauré avec succès.');
    }
    
    /**
     * Supprime définitivement un département de la base de données.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $department = ESBTPDepartment::withTrashed()->findOrFail($id);
        
        // Supprimer le logo s'il existe
        if ($department->logo) {
            Storage::disk('public')->delete($department->logo);
        }
        
        $department->forceDelete();
        
        return redirect()->route('esbtp.departments.index')
            ->with('success', 'Département supprimé définitivement.');
    }
    
    /**
     * Attach a partnership to the department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attachPartnership(Request $request, $id)
    {
        $department = ESBTPDepartment::findOrFail($id);
        
        $validated = $request->validate([
            'partnership_id' => 'required|exists:esbtp_partnerships,id',
            'specific_details' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        // Vérifier si la relation existe déjà
        if (!$department->partnerships()->where('partnership_id', $validated['partnership_id'])->exists()) {
            $department->partnerships()->attach($validated['partnership_id'], [
                'specific_details' => $validated['specific_details'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ]);
            
            return redirect()->route('esbtp.departments.show', $department->id)
                ->with('success', 'Partenariat ajouté au département avec succès.');
        }
        
        return redirect()->route('esbtp.departments.show', $department->id)
            ->with('error', 'Ce partenariat est déjà associé à ce département.');
    }
    
    /**
     * Detach a partnership from the department.
     *
     * @param  int  $departmentId
     * @param  int  $partnershipId
     * @return \Illuminate\Http\Response
     */
    public function detachPartnership($departmentId, $partnershipId)
    {
        $department = ESBTPDepartment::findOrFail($departmentId);
        $department->partnerships()->detach($partnershipId);
        
        return redirect()->route('esbtp.departments.show', $department->id)
            ->with('success', 'Partenariat retiré du département avec succès.');
    }
}

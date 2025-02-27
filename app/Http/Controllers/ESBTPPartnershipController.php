<?php

namespace App\Http\Controllers;

use App\Models\ESBTPDepartment;
use App\Models\ESBTPPartnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class ESBTPPartnershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer les partenariats actifs, inactifs et archivés
        $activePartnerships = ESBTPPartnership::where('is_active', true)->whereNull('deleted_at')->get();
        $inactivePartnerships = ESBTPPartnership::where('is_active', false)->whereNull('deleted_at')->get();
        $archivedPartnerships = ESBTPPartnership::onlyTrashed()->get();
        
        return view('esbtp.partnerships.index', compact('activePartnerships', 'inactivePartnerships', 'archivedPartnerships'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::pluck('name', 'id');
        $partnershipTypes = [
            'academic' => 'Académique',
            'industrial' => 'Industriel',
            'research' => 'Recherche',
            'internship' => 'Stage',
            'financial' => 'Financier',
            'other' => 'Autre'
        ];
        
        return view('esbtp.partnerships.create', compact('departments', 'partnershipTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|array',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'website' => 'nullable|url|max:255',
                'logo' => 'nullable|image|max:2048',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
            ]);
            
            // Convertir le tableau de types en chaîne JSON
            $validatedData['type'] = json_encode($request->type);
            
            // Traiter le logo s'il est fourni
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $filename = 'partnership_' . time() . '_' . Str::random(10) . '.' . $logo->getClientOriginalExtension();
                $path = $logo->storeAs('partnerships/logos', $filename, 'public');
                $validatedData['logo'] = $path;
            }
            
            // Créer le partenariat
            $partnership = ESBTPPartnership::create($validatedData);
            
            return redirect()->route('esbtp.partnerships.show', $partnership)
                ->with('success', 'Le partenariat a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du partenariat: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du partenariat.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $partnership = ESBTPPartnership::withTrashed()->findOrFail($id);
        $departments = ESBTPDepartment::where('is_active', true)->get();
        $partnershipDepartments = $partnership->departments;
        
        // Décoder les types de partenariat
        $partnershipTypes = [
            'academic' => 'Académique',
            'industrial' => 'Industriel',
            'research' => 'Recherche',
            'internship' => 'Stage',
            'financial' => 'Financier',
            'other' => 'Autre'
        ];
        
        $types = json_decode($partnership->type, true) ?? [];
        
        return view('esbtp.partnerships.show', compact('partnership', 'departments', 'partnershipDepartments', 'partnershipTypes', 'types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partnership = ESBTPPartnership::withTrashed()->findOrFail($id);
        $departments = ESBTPDepartment::where('is_active', true)->get();
        
        // Décoder les types de partenariat
        $selectedTypes = json_decode($partnership->type, true) ?? [];
        
        return view('esbtp.partnerships.edit', compact('partnership', 'departments', 'selectedTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $partnership = ESBTPPartnership::withTrashed()->findOrFail($id);
        
        try {
            // Valider les données du formulaire
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|array',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'website' => 'nullable|url|max:255',
                'logo' => 'nullable|image|max:2048',
                'is_active' => 'boolean',
                'description' => 'nullable|string',
            ]);
            
            // Convertir le tableau de types en chaîne JSON
            $validatedData['type'] = json_encode($request->type);
            
            // Traiter le logo s'il est fourni
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo s'il existe
                if ($partnership->logo) {
                    Storage::disk('public')->delete($partnership->logo);
                }
                
                $logo = $request->file('logo');
                $filename = 'partnership_' . time() . '_' . Str::random(10) . '.' . $logo->getClientOriginalExtension();
                $path = $logo->storeAs('partnerships/logos', $filename, 'public');
                $validatedData['logo'] = $path;
            }
            
            // Mettre à jour le partenariat
            $partnership->update($validatedData);
            
            return redirect()->route('esbtp.partnerships.show', $partnership)
                ->with('success', 'Le partenariat a été mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du partenariat: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du partenariat.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $partnership = ESBTPPartnership::withTrashed()->findOrFail($id);
        
        try {
            $partnership->delete();
            return redirect()->route('esbtp.partnerships.index')
                ->with('success', 'Le partenariat a été archivé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du partenariat: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'archivage du partenariat.');
        }
    }
    
    /**
     * Restore the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $partnership = ESBTPPartnership::withTrashed()->findOrFail($id);
            $partnership->restore();
            
            return redirect()->route('esbtp.partnerships.index')
                ->with('success', 'Le partenariat a été restauré avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration du partenariat: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la restauration du partenariat.');
        }
    }
    
    /**
     * Attach a department to the partnership.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attachDepartment(Request $request, $id)
    {
        $partnership = ESBTPPartnership::findOrFail($id);
        
        $validated = $request->validate([
            'department_id' => 'required|exists:esbtp_departments,id',
            'specific_details' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        // Vérifier si la relation existe déjà
        if (!$partnership->departments()->where('department_id', $validated['department_id'])->exists()) {
            $partnership->departments()->attach($validated['department_id'], [
                'specific_details' => $validated['specific_details'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ]);
            
            return redirect()->route('esbtp.partnerships.show', $partnership->id)
                ->with('success', 'Département ajouté au partenariat avec succès.');
        }
        
        return redirect()->route('esbtp.partnerships.show', $partnership->id)
            ->with('error', 'Ce département est déjà associé à ce partenariat.');
    }
    
    /**
     * Detach a department from the partnership.
     *
     * @param  int  $partnershipId
     * @param  int  $departmentId
     * @return \Illuminate\Http\Response
     */
    public function detachDepartment($partnershipId, $departmentId)
    {
        $partnership = ESBTPPartnership::findOrFail($partnershipId);
        $partnership->departments()->detach($departmentId);
        
        return redirect()->route('esbtp.partnerships.show', $partnership->id)
            ->with('success', 'Département retiré du partenariat avec succès.');
    }

    /**
     * Supprime définitivement un partenariat
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        try {
            $partnership = ESBTPPartnership::withTrashed()->findOrFail($id);
            
            // Supprimer le logo s'il existe
            if ($partnership->logo) {
                Storage::disk('public')->delete($partnership->logo);
            }
            
            // Détacher les départements associés
            $partnership->departments()->detach();
            
            $partnership->forceDelete();
            
            return redirect()->route('esbtp.partnerships.index')
                ->with('success', 'Le partenariat a été supprimé définitivement.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression définitive du partenariat: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression définitive du partenariat.');
        }
    }
}

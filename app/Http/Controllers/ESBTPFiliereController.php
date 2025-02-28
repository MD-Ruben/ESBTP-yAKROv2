<?php

namespace App\Http\Controllers;

use App\Models\ESBTPFiliere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ESBTPFiliereController extends Controller
{
    /**
     * Affiche la liste des filières.
     * 
     * Cette méthode récupère toutes les filières principales (sans parent)
     * et leurs options (filières enfants) pour les afficher dans une liste.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer toutes les filières principales (sans parent)
        $filieres = ESBTPFiliere::whereNull('parent_id')->with('options')->get();
        
        return view('esbtp.filieres.index', compact('filieres'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle filière.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Récupérer toutes les filières principales pour le dropdown (pour créer une option)
        $parentFilieres = ESBTPFiliere::whereNull('parent_id')->get();
        
        return view('esbtp.filieres.create', compact('parentFilieres'));
    }

    /**
     * Enregistre une nouvelle filière dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:esbtp_filieres,name',
            'code' => 'required|string|max:50|unique:esbtp_filieres,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:esbtp_filieres,id',
            'is_active' => 'boolean',
        ]);
        
        // Créer la nouvelle filière
        $filiere = ESBTPFiliere::create($validatedData);
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.filieres.index')
            ->with('success', 'La filière a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une filière spécifique.
     *
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPFiliere $filiere)
    {
        // Charger les relations
        $filiere->load('parent', 'options');
        
        return view('esbtp.filieres.show', compact('filiere'));
    }

    /**
     * Affiche le formulaire de modification d'une filière.
     *
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPFiliere $filiere)
    {
        // Récupérer toutes les filières principales pour le dropdown (pour modifier une option)
        // Exclure la filière actuelle pour éviter une auto-référence
        $parentFilieres = ESBTPFiliere::whereNull('parent_id')
            ->where('id', '!=', $filiere->id)
            ->get();
        
        return view('esbtp.filieres.edit', compact('filiere', 'parentFilieres'));
    }

    /**
     * Met à jour la filière spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPFiliere $filiere)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:esbtp_filieres,name,' . $filiere->id,
            'code' => 'required|string|max:50|unique:esbtp_filieres,code,' . $filiere->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:esbtp_filieres,id',
            'is_active' => 'boolean',
        ]);
        
        // Vérifier que la filière n'est pas son propre parent
        if ($request->parent_id == $filiere->id) {
            return redirect()->back()
                ->withErrors(['parent_id' => 'Une filière ne peut pas être son propre parent.'])
                ->withInput();
        }
        
        // Mettre à jour la filière
        $filiere->update($validatedData);
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.filieres.index')
            ->with('success', 'La filière a été mise à jour avec succès.');
    }

    /**
     * Supprime la filière spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPFiliere $filiere)
    {
        // Vérifier si la filière a des options (filières enfants)
        if ($filiere->options()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette filière car elle a des options associées.');
        }
        
        // Vérifier si la filière a des étudiants inscrits
        if ($filiere->inscriptions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette filière car des étudiants y sont inscrits.');
        }
        
        // Supprimer la filière
        $filiere->delete();
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.filieres.index')
            ->with('success', 'La filière a été supprimée avec succès.');
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ESBTPAnneeUniversitaireController extends Controller
{
    /**
     * Affiche la liste des années universitaires.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $annees = ESBTPAnneeUniversitaire::orderBy('start_date', 'desc')->get();
        
        return view('esbtp.annees.index', compact('annees'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle année universitaire.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('esbtp.annees.create');
    }

    /**
     * Enregistre une nouvelle année universitaire dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:esbtp_annee_universitaires,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);
        
        // Créer la nouvelle année universitaire
        $annee = ESBTPAnneeUniversitaire::create($validatedData);
        
        // Si cette année est définie comme l'année en cours, mettre à jour les autres années
        if ($request->has('is_current') && $request->is_current) {
            $annee->setAsCurrent();
        }
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.annees.index')
            ->with('success', 'L\'année universitaire a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une année universitaire spécifique.
     *
     * @param  \App\Models\ESBTPAnneeUniversitaire  $annee
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPAnneeUniversitaire $annee)
    {
        // Charger les étudiants inscrits pour cette année
        $annee->load('inscriptions.student', 'inscriptions.filiere', 'inscriptions.niveauEtude');
        
        return view('esbtp.annees.show', compact('annee'));
    }

    /**
     * Affiche le formulaire de modification d'une année universitaire.
     *
     * @param  \App\Models\ESBTPAnneeUniversitaire  $annee
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPAnneeUniversitaire $annee)
    {
        return view('esbtp.annees.edit', compact('annee'));
    }

    /**
     * Met à jour l'année universitaire spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPAnneeUniversitaire  $annee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPAnneeUniversitaire $annee)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:esbtp_annee_universitaires,name,' . $annee->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);
        
        // Mettre à jour l'année universitaire
        $annee->update($validatedData);
        
        // Si cette année est définie comme l'année en cours, mettre à jour les autres années
        if ($request->has('is_current') && $request->is_current) {
            $annee->setAsCurrent();
        }
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.annees.index')
            ->with('success', 'L\'année universitaire a été mise à jour avec succès.');
    }

    /**
     * Supprime l'année universitaire spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPAnneeUniversitaire  $annee
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPAnneeUniversitaire $annee)
    {
        // Vérifier si l'année universitaire a des étudiants inscrits
        if ($annee->inscriptions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette année universitaire car des étudiants y sont inscrits.');
        }
        
        // Supprimer l'année universitaire
        $annee->delete();
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.annees.index')
            ->with('success', 'L\'année universitaire a été supprimée avec succès.');
    }
    
    /**
     * Définit l'année universitaire spécifiée comme l'année en cours.
     *
     * @param  \App\Models\ESBTPAnneeUniversitaire  $annee
     * @return \Illuminate\Http\Response
     */
    public function setCurrent(ESBTPAnneeUniversitaire $annee)
    {
        // Définir cette année comme l'année en cours
        $annee->setAsCurrent();
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.annees.index')
            ->with('success', 'L\'année universitaire a été définie comme l\'année en cours.');
    }
} 
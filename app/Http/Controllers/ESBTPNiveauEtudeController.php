<?php

namespace App\Http\Controllers;

use App\Models\ESBTPNiveauEtude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ESBTPNiveauEtudeController extends Controller
{
    /**
     * Affiche la liste des niveaux d'études.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $niveaux = ESBTPNiveauEtude::orderBy('type')->orderBy('year')->get();
        
        return view('esbtp.niveaux.index', compact('niveaux'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau niveau d'études.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Types de niveaux d'études possibles (BTS, Licence, Master, etc.)
        $types = ['BTS', 'Licence', 'Master', 'Ingénieur', 'Autre'];
        
        return view('esbtp.niveaux.create', compact('types'));
    }

    /**
     * Enregistre un nouveau niveau d'études dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_niveau_etudes,code',
            'type' => 'required|string|max:50',
            'year' => 'required|integer|between:1,7',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Créer le nouveau niveau d'études
        ESBTPNiveauEtude::create($validatedData);
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.niveaux.index')
            ->with('success', 'Le niveau d\'études a été créé avec succès.');
    }

    /**
     * Affiche les détails d'un niveau d'études spécifique.
     *
     * @param  \App\Models\ESBTPNiveauEtude  $niveau
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPNiveauEtude $niveau)
    {
        return view('esbtp.niveaux.show', compact('niveau'));
    }

    /**
     * Affiche le formulaire de modification d'un niveau d'études.
     *
     * @param  \App\Models\ESBTPNiveauEtude  $niveau
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPNiveauEtude $niveau)
    {
        // Types de niveaux d'études possibles (BTS, Licence, Master, etc.)
        $types = ['BTS', 'Licence', 'Master', 'Ingénieur', 'Autre'];
        
        return view('esbtp.niveaux.edit', compact('niveau', 'types'));
    }

    /**
     * Met à jour le niveau d'études spécifié dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPNiveauEtude  $niveau
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPNiveauEtude $niveau)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_niveau_etudes,code,' . $niveau->id,
            'type' => 'required|string|max:50',
            'year' => 'required|integer|between:1,7',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Mettre à jour le niveau d'études
        $niveau->update($validatedData);
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.niveaux.index')
            ->with('success', 'Le niveau d\'études a été mis à jour avec succès.');
    }

    /**
     * Supprime le niveau d'études spécifié de la base de données.
     *
     * @param  \App\Models\ESBTPNiveauEtude  $niveau
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPNiveauEtude $niveau)
    {
        // Vérifier si le niveau d'études a des étudiants inscrits
        if ($niveau->inscriptions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer ce niveau d\'études car des étudiants y sont inscrits.');
        }
        
        // Supprimer le niveau d'études
        $niveau->delete();
        
        // Rediriger avec un message de succès
        return redirect()->route('esbtp.niveaux.index')
            ->with('success', 'Le niveau d\'études a été supprimé avec succès.');
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\ESBTPClasse;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\ESBTPFormation;
use App\Models\ESBTPMatiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ESBTPClasseController extends Controller
{
    /**
     * Affiche la liste des classes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classes = ESBTPClasse::with(['filiere', 'niveau', 'annee'])->get();
        
        return view('esbtp.classes.index', compact('classes'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle classe.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::where('is_active', true)->get();
        $formations = ESBTPFormation::where('is_active', true)->get();
        
        return view('esbtp.classes.create', compact('filieres', 'niveaux', 'annees', 'formations'));
    }

    /**
     * Enregistre une nouvelle classe dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_classes,code',
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_etude_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'formation_ids' => 'required|array',
            'formation_ids.*' => 'exists:esbtp_formations,id',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Ajouter les champs de traçabilité
        $validatedData['created_by'] = Auth::id();
        $validatedData['updated_by'] = Auth::id();
        
        // Créer la nouvelle classe
        $classe = ESBTPClasse::create($validatedData);
        
        // Récupérer les matières associées aux formations et niveaux sélectionnés
        $matieres = ESBTPMatiere::whereHas('formations', function ($query) use ($request) {
            $query->whereIn('esbtp_formations.id', $request->formation_ids);
        })->whereHas('niveaux', function ($query) use ($request) {
            $query->where('esbtp_niveau_etudes.id', $request->niveau_etude_id);
        })->get();
        
        // Associer les matières à la classe avec leurs coefficients et heures par défaut
        foreach ($matieres as $matiere) {
            $classe->matieres()->attach($matiere->id, [
                'coefficient' => $matiere->coefficient_default,
                'total_heures' => $matiere->total_heures_default,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return redirect()->route('esbtp.classes.index')
            ->with('success', 'La classe a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une classe spécifique.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPClasse $classe)
    {
        $classe->load(['filiere', 'niveau', 'annee', 'matieres', 'etudiants', 'inscriptions', 'emploisDuTemps']);
        
        return view('esbtp.classes.show', compact('classe'));
    }

    /**
     * Affiche le formulaire de modification d'une classe existante.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPClasse $classe)
    {
        $filieres = ESBTPFiliere::where('is_active', true)->get();
        $niveaux = ESBTPNiveauEtude::where('is_active', true)->get();
        $annees = ESBTPAnneeUniversitaire::where('is_active', true)->get();
        $formations = ESBTPFormation::where('is_active', true)->get();
        
        // Récupérer les formations associées à la classe via ses matières
        $formationIds = ESBTPFormation::whereHas('matieres', function($query) use ($classe) {
            $query->whereIn('esbtp_matieres.id', $classe->matieres->pluck('id'));
        })->pluck('id')->toArray();
        
        return view('esbtp.classes.edit', compact('classe', 'filieres', 'niveaux', 'annees', 'formations', 'formationIds'));
    }

    /**
     * Met à jour la classe spécifiée dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPClasse $classe)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:esbtp_classes,code,' . $classe->id,
            'filiere_id' => 'required|exists:esbtp_filieres,id',
            'niveau_etude_id' => 'required|exists:esbtp_niveau_etudes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'formation_ids' => 'required|array',
            'formation_ids.*' => 'exists:esbtp_formations,id',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Mettre à jour les champs de traçabilité
        $validatedData['updated_by'] = Auth::id();
        
        // Mettre à jour la classe
        $classe->update($validatedData);
        
        // Si les formations ou le niveau ont changé, mettre à jour les matières
        if ($request->has('formation_ids') || $classe->isDirty('niveau_etude_id')) {
            // Récupérer les matières associées aux formations et niveau sélectionnés
            $matieres = ESBTPMatiere::whereHas('formations', function ($query) use ($request) {
                $query->whereIn('esbtp_formations.id', $request->formation_ids);
            })->whereHas('niveaux', function ($query) use ($request) {
                $query->where('esbtp_niveau_etudes.id', $request->niveau_etude_id);
            })->get();
            
            // Réinitialiser les matières associées à la classe
            $classe->matieres()->detach();
            
            // Associer les nouvelles matières à la classe
            foreach ($matieres as $matiere) {
                $classe->matieres()->attach($matiere->id, [
                    'coefficient' => $matiere->coefficient_default,
                    'total_heures' => $matiere->total_heures_default,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        return redirect()->route('esbtp.classes.index')
            ->with('success', 'La classe a été mise à jour avec succès.');
    }

    /**
     * Supprime la classe spécifiée de la base de données.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPClasse $classe)
    {
        // Vérifier si des étudiants sont inscrits dans cette classe
        if ($classe->inscriptions()->count() > 0) {
            return redirect()->route('esbtp.classes.index')
                ->with('error', 'Impossible de supprimer cette classe car elle contient des étudiants inscrits.');
        }
        
        // Détacher toutes les matières
        $classe->matieres()->detach();
        
        // Supprimer la classe
        $classe->delete();
        
        return redirect()->route('esbtp.classes.index')
            ->with('success', 'La classe a été supprimée avec succès.');
    }

    /**
     * Affiche la page de gestion des matières associées à une classe.
     *
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function matieres(ESBTPClasse $classe)
    {
        $classe->load('matieres');
        $allMatieres = ESBTPMatiere::where('is_active', true)->get();
        
        return view('esbtp.classes.matieres', compact('classe', 'allMatieres'));
    }

    /**
     * Met à jour les matières et leurs coefficients pour une classe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPClasse  $classe
     * @return \Illuminate\Http\Response
     */
    public function updateMatieres(Request $request, ESBTPClasse $classe)
    {
        $validatedData = $request->validate([
            'matieres' => 'required|array',
            'matieres.*.id' => 'required|exists:esbtp_matieres,id',
            'matieres.*.coefficient' => 'required|numeric|min:0',
            'matieres.*.total_heures' => 'required|integer|min:0',
            'matieres.*.is_active' => 'boolean',
        ]);
        
        // Réinitialiser les matières existantes
        $classe->matieres()->detach();
        
        // Ajouter les matières avec leurs coefficients et heures spécifiques
        foreach ($validatedData['matieres'] as $matiere) {
            $classe->matieres()->attach($matiere['id'], [
                'coefficient' => $matiere['coefficient'],
                'total_heures' => $matiere['total_heures'],
                'is_active' => $matiere['is_active'] ?? false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return redirect()->route('esbtp.classes.show', $classe)
            ->with('success', 'Les matières de la classe ont été mises à jour avec succès.');
    }
} 
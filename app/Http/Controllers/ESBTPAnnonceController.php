<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPAnnonce;
use App\Models\ESBTPClasse;
use App\Models\ESBTPEtudiant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ESBTPAnnonceController extends Controller
{
    /**
     * Affiche la liste des annonces.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $annonces = ESBTPAnnonce::with(['classes', 'etudiants', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('esbtp.annonces.index', compact('annonces'));
    }

    /**
     * Affiche le formulaire de création d'une annonce.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $etudiants = ESBTPEtudiant::orderBy('nom')->get();
        
        return view('esbtp.annonces.create', compact('classes', 'etudiants'));
    }

    /**
     * Enregistre une nouvelle annonce.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'type' => 'required|in:general,classe,etudiant',
            'priorite' => 'required|in:normale,moyenne,elevee',
            'classes' => 'required_if:type,classe|array',
            'etudiants' => 'required_if:type,etudiant|array',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'contenu.required' => 'Le contenu est obligatoire',
            'date_debut.required' => 'La date de début est obligatoire',
            'date_fin.required' => 'La date de fin est obligatoire',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début',
            'priorite.required' => 'La priorité est obligatoire',
            'classes.required_if' => 'Veuillez sélectionner au moins une classe',
            'etudiants.required_if' => 'Veuillez sélectionner au moins un étudiant',
        ]);

        DB::beginTransaction();
        try {
            $annonce = new ESBTPAnnonce();
            $annonce->titre = $request->titre;
            $annonce->contenu = $request->contenu;
            $annonce->date_debut = $request->date_debut;
            $annonce->date_fin = $request->date_fin;
            $annonce->type = $request->type;
            $annonce->priorite = $request->priorite;
            $annonce->is_active = $request->has('is_active');
            $annonce->user_id = Auth::id();
            $annonce->save();

            // Attacher les classes ou les étudiants selon le type
            if ($request->type == 'classe' && $request->has('classes')) {
                $annonce->classes()->attach($request->classes);
            } elseif ($request->type == 'etudiant' && $request->has('etudiants')) {
                $annonce->etudiants()->attach($request->etudiants);
            }

            DB::commit();
            return redirect()->route('annonces.index')
                ->with('success', 'L\'annonce a été créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'annonce: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche une annonce spécifique.
     *
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPAnnonce $annonce)
    {
        $annonce->load(['classes', 'etudiants', 'user']);
        return view('esbtp.annonces.show', compact('annonce'));
    }

    /**
     * Affiche le formulaire de modification d'une annonce.
     *
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPAnnonce $annonce)
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $etudiants = ESBTPEtudiant::orderBy('nom')->get();
        
        $classeIds = $annonce->classes->pluck('id')->toArray();
        $etudiantIds = $annonce->etudiants->pluck('id')->toArray();
        
        return view('esbtp.annonces.edit', compact('annonce', 'classes', 'etudiants', 'classeIds', 'etudiantIds'));
    }

    /**
     * Met à jour une annonce spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPAnnonce $annonce)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'type' => 'required|in:general,classe,etudiant',
            'priorite' => 'required|in:normale,moyenne,elevee',
            'classes' => 'required_if:type,classe|array',
            'etudiants' => 'required_if:type,etudiant|array',
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'contenu.required' => 'Le contenu est obligatoire',
            'date_debut.required' => 'La date de début est obligatoire',
            'date_fin.required' => 'La date de fin est obligatoire',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début',
            'priorite.required' => 'La priorité est obligatoire',
            'classes.required_if' => 'Veuillez sélectionner au moins une classe',
            'etudiants.required_if' => 'Veuillez sélectionner au moins un étudiant',
        ]);

        DB::beginTransaction();
        try {
            $annonce->titre = $request->titre;
            $annonce->contenu = $request->contenu;
            $annonce->date_debut = $request->date_debut;
            $annonce->date_fin = $request->date_fin;
            $annonce->type = $request->type;
            $annonce->priorite = $request->priorite;
            $annonce->is_active = $request->has('is_active');
            $annonce->save();

            // Mettre à jour les associations
            $annonce->classes()->detach();
            $annonce->etudiants()->detach();

            if ($request->type == 'classe' && $request->has('classes')) {
                $annonce->classes()->attach($request->classes);
            } elseif ($request->type == 'etudiant' && $request->has('etudiants')) {
                $annonce->etudiants()->attach($request->etudiants);
            }

            DB::commit();
            return redirect()->route('annonces.index')
                ->with('success', 'L\'annonce a été mise à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'annonce: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime une annonce spécifique.
     *
     * @param  \App\Models\ESBTPAnnonce  $annonce
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPAnnonce $annonce)
    {
        try {
            // Détacher d'abord toutes les relations
            $annonce->classes()->detach();
            $annonce->etudiants()->detach();
            
            // Puis supprimer l'annonce
            $annonce->delete();
            
            return redirect()->route('annonces.index')
                ->with('success', 'L\'annonce a été supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('annonces.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'annonce: ' . $e->getMessage());
        }
    }
} 
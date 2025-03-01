<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPEmploiTemps;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ESBTPEmploiTempsController extends Controller
{
    /**
     * Affiche la liste des emplois du temps.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emploisTemps = ESBTPEmploiTemps::with(['classe', 'seances', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('esbtp.emplois-temps.index', compact('emploisTemps'));
    }

    /**
     * Affiche le formulaire de création d'un emploi du temps.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        return view('esbtp.emplois-temps.create', compact('classes'));
    }

    /**
     * Enregistre un nouvel emploi du temps.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'semestre' => 'nullable|string|max:50',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'is_active' => 'boolean'
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'classe_id.required' => 'La classe est obligatoire',
            'date_debut.required' => 'La date de début est obligatoire',
            'date_fin.required' => 'La date de fin est obligatoire',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début'
        ]);
        
        // Vérifier s'il existe déjà un emploi du temps actif pour cette classe avec des dates qui se chevauchent
        if ($request->input('is_active', true)) {
            $emploiTempsExistant = ESBTPEmploiTemps::where('classe_id', $request->classe_id)
                ->where('is_active', true)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('date_debut', [$request->date_debut, $request->date_fin])
                        ->orWhereBetween('date_fin', [$request->date_debut, $request->date_fin])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('date_debut', '<=', $request->date_debut)
                                ->where('date_fin', '>=', $request->date_fin);
                        });
                })->exists();
            
            if ($emploiTempsExistant) {
                return redirect()->back()
                    ->with('error', 'Il existe déjà un emploi du temps actif pour cette classe pendant cette période')
                    ->withInput();
            }
        }
        
        DB::beginTransaction();
        try {
            $emploiTemps = new ESBTPEmploiTemps();
            $emploiTemps->titre = $request->titre;
            $emploiTemps->classe_id = $request->classe_id;
            $emploiTemps->semestre = $request->semestre;
            $emploiTemps->date_debut = $request->date_debut;
            $emploiTemps->date_fin = $request->date_fin;
            $emploiTemps->is_active = $request->input('is_active', true);
            $emploiTemps->created_by = Auth::id();
            $emploiTemps->save();
            
            DB::commit();
            return redirect()->route('esbtp.emplois-temps.show', $emploiTemps)
                ->with('success', 'L\'emploi du temps a été créé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'emploi du temps: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche un emploi du temps spécifique.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemps
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPEmploiTemps $emploiTemp)
    {
        $emploiTemps = $emploiTemp; // Pour faciliter la lecture
        $emploiTemps->load(['classe', 'seances.matiere', 'seances.enseignant']);
        
        // Organiser les séances par jour de la semaine
        $seancesParJour = $emploiTemps->getSeancesParJour();
        
        // Définir les heures de début et de fin pour l'affichage de l'emploi du temps
        $heuresDebut = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
        $heuresFin = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
        
        $joursNoms = [
            0 => 'Lundi',
            1 => 'Mardi',
            2 => 'Mercredi',
            3 => 'Jeudi',
            4 => 'Vendredi',
            5 => 'Samedi',
            6 => 'Dimanche'
        ];
        
        return view('esbtp.emplois-temps.show', compact('emploiTemps', 'seancesParJour', 'heuresDebut', 'heuresFin', 'joursNoms'));
    }

    /**
     * Affiche le formulaire d'édition d'un emploi du temps.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemps
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPEmploiTemps $emploiTemp)
    {
        $emploiTemps = $emploiTemp; // Pour faciliter la lecture
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        return view('esbtp.emplois-temps.edit', compact('emploiTemps', 'classes'));
    }

    /**
     * Met à jour un emploi du temps spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemps
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPEmploiTemps $emploiTemp)
    {
        $emploiTemps = $emploiTemp; // Pour faciliter la lecture
        
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'semestre' => 'nullable|string|max:50',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'is_active' => 'boolean'
        ], [
            'titre.required' => 'Le titre est obligatoire',
            'classe_id.required' => 'La classe est obligatoire',
            'date_debut.required' => 'La date de début est obligatoire',
            'date_fin.required' => 'La date de fin est obligatoire',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début'
        ]);
        
        // Vérifier s'il existe déjà un emploi du temps actif pour cette classe avec des dates qui se chevauchent
        if ($request->input('is_active', true)) {
            $emploiTempsExistant = ESBTPEmploiTemps::where('classe_id', $request->classe_id)
                ->where('is_active', true)
                ->where('id', '!=', $emploiTemps->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('date_debut', [$request->date_debut, $request->date_fin])
                        ->orWhereBetween('date_fin', [$request->date_debut, $request->date_fin])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('date_debut', '<=', $request->date_debut)
                                ->where('date_fin', '>=', $request->date_fin);
                        });
                })->exists();
            
            if ($emploiTempsExistant) {
                return redirect()->back()
                    ->with('error', 'Il existe déjà un emploi du temps actif pour cette classe pendant cette période')
                    ->withInput();
            }
        }
        
        DB::beginTransaction();
        try {
            $emploiTemps->titre = $request->titre;
            $emploiTemps->classe_id = $request->classe_id;
            $emploiTemps->semestre = $request->semestre;
            $emploiTemps->date_debut = $request->date_debut;
            $emploiTemps->date_fin = $request->date_fin;
            $emploiTemps->is_active = $request->input('is_active', true);
            $emploiTemps->updated_by = Auth::id();
            $emploiTemps->save();
            
            DB::commit();
            return redirect()->route('esbtp.emplois-temps.show', $emploiTemps)
                ->with('success', 'L\'emploi du temps a été mis à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'emploi du temps: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime un emploi du temps spécifique.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemps
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPEmploiTemps $emploiTemp)
    {
        $emploiTemps = $emploiTemp; // Pour faciliter la lecture
        
        DB::beginTransaction();
        try {
            // Supprimer d'abord toutes les séances associées
            $emploiTemps->seances()->delete();
            
            // Puis supprimer l'emploi du temps
            $emploiTemps->delete();
            
            DB::commit();
            return redirect()->route('esbtp.emplois-temps.index')
                ->with('success', 'L\'emploi du temps a été supprimé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'emploi du temps: ' . $e->getMessage());
        }
    }
} 
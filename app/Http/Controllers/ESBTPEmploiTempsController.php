<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPEmploiTemps;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\User;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPAnneeUniversitaire;
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
        $emploisTemps = ESBTPEmploiTemps::orderBy('date_debut', 'desc')->get();
        
        // Ajout des filières pour le filtre
        $filieres = ESBTPFiliere::where('is_active', true)->orderBy('name')->get();
        
        // Ajout des niveaux pour le filtre
        $niveaux = ESBTPNiveauEtude::orderBy('name')->get();
        
        // Ajout des années universitaires pour le filtre
        $annees = ESBTPAnneeUniversitaire::orderBy('name', 'desc')->get();
        
        // Récupérer l'année universitaire en cours
        $anneeEnCours = ESBTPAnneeUniversitaire::where('is_active', true)->first();
        
        // Statistiques
        $totalEmploisTemps = $emploisTemps->count();
        $emploisTempsActifs = $emploisTemps->where('is_active', true)->count();
        $totalSeances = ESBTPSeanceCours::count();
        
        // Emplois du temps de l'année en cours
        $emploisTempsAnneeEnCours = 0;
        if ($anneeEnCours) {
            // Trouver tous les emplois du temps associés à des classes de l'année en cours
            $classesAnneeEnCours = ESBTPClasse::where('annee_universitaire_id', $anneeEnCours->id)->pluck('id')->toArray();
            $emploisTempsAnneeEnCours = $emploisTemps->whereIn('classe_id', $classesAnneeEnCours)->count();
        }
        
        return view('esbtp.emplois-temps.index', compact(
            'emploisTemps', 'filieres', 'niveaux', 'annees',
            'totalEmploisTemps', 'emploisTempsActifs', 'totalSeances', 'emploisTempsAnneeEnCours'
        ));
    }

    /**
     * Affiche le formulaire de création d'un emploi du temps.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('name', 'desc')->get();
        return view('esbtp.emplois-temps.create', compact('classes', 'annees'));
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
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
        ]);
        
        $emploiTemps = new ESBTPEmploiTemps();
        $emploiTemps->titre = $validated['titre'];
        $emploiTemps->classe_id = $validated['classe_id'];
        $emploiTemps->date_debut = $validated['date_debut'];
        $emploiTemps->date_fin = $validated['date_fin'];
        $emploiTemps->description = $validated['description'];
        $emploiTemps->is_active = true;
        $emploiTemps->created_by = Auth::id();
        $emploiTemps->save();
        
        return redirect()->route('esbtp.emplois-temps.show', $emploiTemps)
            ->with('success', 'L\'emploi du temps a été créé avec succès. Vous pouvez maintenant ajouter des séances.');
    }

    /**
     * Affiche un emploi du temps spécifique.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemp
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPEmploiTemps $emploiTemp)
    {
        // Charger les séances pour cet emploi du temps
        $emploiTemp->load('seances');

        // Grouper les séances par jour
        $seancesParJour = $emploiTemp->getSeancesParJour();

        // Récupérer les heures de début et de fin pour l'affichage
        $heuresDebut = ['08:00', '10:00', '13:00', '15:00', '17:00'];
        $heuresFin = ['10:00', '12:00', '15:00', '17:00', '19:00'];

        // Noms des jours pour l'affichage
        $joursNoms = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
        ];

        return view('esbtp.emplois-temps.show', compact('emploiTemp', 'seancesParJour', 'heuresDebut', 'heuresFin', 'joursNoms'));
    }

    /**
     * Affiche le formulaire de modification d'un emploi du temps.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemp
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPEmploiTemps $emploiTemp)
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        return view('esbtp.emplois-temps.edit', compact('emploiTemp', 'classes'));
    }

    /**
     * Met à jour un emploi du temps.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPEmploiTemps $emploiTemp)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $emploiTemp->titre = $validated['titre'];
        $emploiTemp->classe_id = $validated['classe_id'];
        $emploiTemp->date_debut = $validated['date_debut'];
        $emploiTemp->date_fin = $validated['date_fin'];
        $emploiTemp->description = $validated['description'] ?? $emploiTemp->description;
        $emploiTemp->is_active = $request->has('is_active');
        $emploiTemp->updated_by = Auth::id();
        $emploiTemp->save();
        
        return redirect()->route('esbtp.emplois-temps.show', $emploiTemp)
            ->with('success', 'L\'emploi du temps a été modifié avec succès.');
    }

    /**
     * Supprime un emploi du temps.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emploiTemp
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPEmploiTemps $emploiTemp)
    {
        $emploiTemp->delete();
        
        return redirect()->route('esbtp.emplois-temps.index')
            ->with('success', 'L\'emploi du temps a été supprimé avec succès.');
    }
    
    /**
     * Affiche l'emploi du temps de l'étudiant connecté.
     *
     * @return \Illuminate\Http\Response
     */
    public function studentTimetable()
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();
        
        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }
        
        $emploiTemps = ESBTPEmploiTemps::where('classe_id', $etudiant->classe_id)
            ->where('is_current', true)
            ->first();
            
        if (!$emploiTemps) {
            return view('etudiants.emploi-temps', [
                'etudiant' => $etudiant,
                'emploiTemps' => null,
                'seances' => collect()
            ])->with('warning', 'Aucun emploi du temps n\'est actuellement disponible pour votre classe.');
        }
        
        $seances = $emploiTemps->seances()
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour_semaine');
            
        return view('etudiants.emploi-temps', compact('etudiant', 'emploiTemps', 'seances'));
    }
} 
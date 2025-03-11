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
        $emploisTemps = ESBTPEmploiTemps::with(['classe', 'seances'])->get();

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

        return view('esbtp.emploi-temps.index', compact(
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
        $classes = ESBTPClasse::all();
        $annees = ESBTPAnneeUniversitaire::where('is_active', true)->get();
        return view('esbtp.emploi-temps.create', compact('classes', 'annees'));
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
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'semestre' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;
        $validated['is_current'] = $request->has('is_current');

        $emploiTemps = ESBTPEmploiTemps::create($validated);

        if ($emploiTemps->is_current) {
            ESBTPEmploiTemps::setAsCurrent($emploiTemps->id);
        }

        return redirect()->route('esbtp.emploi-temps.show', $emploiTemps)
            ->with('success', 'Emploi du temps créé avec succès.');
    }

    /**
     * Affiche un emploi du temps spécifique.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emploi_temp
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPEmploiTemps $emploi_temp)
    {
        // Charger les séances pour cet emploi du temps
        $emploi_temp->load([
            'seances.matiere',
            'seances.enseignant',
            'classe',
            'classe.filiere',
            'classe.niveau',
            'annee'
        ]);

        // Variable $seances pour la vue
        $seances = $emploi_temp->seances;

        // Grouper les séances par jour
        $seancesParJour = $emploi_temp->getSeancesParJour();

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

        // Calcul des statistiques par matière
        $matiereStats = [];
        foreach ($emploi_temp->seances as $seance) {
            $matiereName = $seance->matiere ? $seance->matiere->name : 'Non définie';
            if (!isset($matiereStats[$matiereName])) {
                $matiereStats[$matiereName] = 0;
            }
            $matiereStats[$matiereName]++;
        }

        // Renommer la variable pour la vue
        $emploiTemps = $emploi_temp;

        return view('esbtp.emploi-temps.show', compact('emploiTemps', 'seances', 'seancesParJour', 'heuresDebut', 'heuresFin', 'joursNoms', 'matiereStats'));
    }

    /**
     * Affiche le formulaire de modification d'un emploi du temps.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emplois_temp
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPEmploiTemps $emplois_temp)
    {
        $emploiTemps = $emplois_temp;
        $classes = ESBTPClasse::all();
        return view('esbtp.emploi-temps.edit', compact('emploiTemps', 'classes'));
    }

    /**
     * Met à jour un emploi du temps.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPEmploiTemps  $emplois_temp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPEmploiTemps $emplois_temp)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'semestre' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_current'] = $request->has('is_current');

        $emplois_temp->update($validated);

        if ($emplois_temp->is_current) {
            ESBTPEmploiTemps::setAsCurrent($emplois_temp->id);
        }

        return redirect()->route('esbtp.emploi-temps.show', $emplois_temp)
            ->with('success', 'Emploi du temps mis à jour avec succès.');
    }

    /**
     * Supprime un emploi du temps.
     *
     * @param  \App\Models\ESBTPEmploiTemps  $emplois_temp
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPEmploiTemps $emplois_temp)
    {
        $emplois_temp->delete();

        return redirect()->route('esbtp.emploi-temps.index')
            ->with('success', 'Emploi du temps supprimé avec succès.');
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

    public function setAsCurrent($id)
    {
        ESBTPEmploiTemps::setAsCurrent($id);
        return redirect()->back()->with('success', 'Emploi du temps défini comme actuel.');
    }

    public function getCurrentForClass($classeId)
    {
        $emploiTemps = ESBTPEmploiTemps::where('classe_id', $classeId)
            ->where('is_current', true)
            ->first();

        if (!$emploiTemps) {
            return response()->json(['message' => 'Aucun emploi du temps actuel trouvé pour cette classe.'], 404);
        }

        return response()->json($emploiTemps->load('seances'));
    }
}

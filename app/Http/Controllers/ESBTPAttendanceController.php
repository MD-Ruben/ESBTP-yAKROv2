<?php

namespace App\Http\Controllers;

use App\Models\ESBTPAttendance;
use App\Models\ESBTPClasse;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ESBTPAttendanceController extends Controller
{
    /**
     * Affiche la liste des présences.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Initialiser la requête
        $query = ESBTPAttendance::with(['etudiant', 'seanceCours', 'createdBy']);

        // Filtrer par classe
        if ($request->filled('classe_id')) {
            $query->parClasse($request->classe_id);
        }

        // Filtrer par étudiant
        if ($request->filled('etudiant_id')) {
            $query->parEtudiant($request->etudiant_id);
        }

        // Filtrer par date
        if ($request->filled('date')) {
            $query->parDate($request->date);
        }

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->parStatut($request->statut);
        }

        // Récupérer les données
        $attendances = $query->orderBy('date', 'desc')->paginate(15);

        // Récupérer les données pour les filtres
        $classes = ESBTPClasse::all();
        $etudiants = ESBTPEtudiant::all();

        return view('esbtp.attendances.index', compact('attendances', 'classes', 'etudiants'));
    }

    /**
     * Affiche le formulaire pour marquer les présences d'une séance.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Récupérer les classes pour le filtre
        $classes = ESBTPClasse::all();

        // Initialiser les variables
        $seances = collect();
        $etudiants = collect();
        $dateSeance = null;
        $messageErreur = null;
        $classeSelectionnee = false;
        $debug = []; // Tableau pour stocker les informations de débogage

        // Si une classe est sélectionnée (et que la valeur n'est pas vide)
        if ($request->filled('classe_id') && !empty($request->classe_id)) {
            $classeSelectionnee = true;
            $debug['classe_id'] = $request->classe_id;

            try {
                // Vérifier que la classe existe
                $classe = ESBTPClasse::findOrFail($request->classe_id);
                $debug['classe_trouvee'] = true;
                $debug['classe_nom'] = $classe->name;

                // Récupérer les séances de cours pour cette classe
                $seances = ESBTPSeanceCours::whereHas('emploiTemps', function($query) use ($request) {
                    $query->where('classe_id', $request->classe_id)
                          ->where('is_active', true);
                })->with(['emploiTemps', 'matiere'])->get();

                $debug['nombre_seances'] = $seances->count();

                // Ajouter des informations supplémentaires pour l'affichage
                $seances->each(function($seance) {
                    $seance->jour_nom = $seance->getNomJour();
                    $seance->date_calculee = $seance->getDateSeance() ? $seance->getDateSeance()->format('Y-m-d') : null;
                });

                // Si aucune séance n'est trouvée, afficher un message
                if ($seances->isEmpty()) {
                    $messageErreur = 'Aucune séance active n\'a été trouvée pour cette classe. Veuillez vérifier que l\'emploi du temps est actif.';
                    $debug['erreur'] = 'aucune_seance_active';
                }

                // Si une séance est sélectionnée, vérifier qu'elle appartient à la classe sélectionnée
                if ($request->filled('seance_id')) {
                    $debug['seance_id'] = $request->seance_id;
                    $seanceAppartientAClasse = $seances->contains('id', $request->seance_id);
                    $debug['seance_appartient_a_classe'] = $seanceAppartientAClasse;

                    if (!$seanceAppartientAClasse) {
                        // La séance sélectionnée n'appartient pas à la classe sélectionnée
                        // Rediriger vers la même page sans le paramètre seance_id
                        return redirect()->route('esbtp.attendances.create', ['classe_id' => $request->classe_id])
                            ->with('warning', 'La séance sélectionnée n\'appartient pas à la classe sélectionnée.');
                    }

                    // Récupérer la séance avec ses relations
                    $seance = ESBTPSeanceCours::with(['emploiTemps.classe', 'matiere'])->findOrFail($request->seance_id);
                    $debug['seance_trouvee'] = true;
                    $debug['seance_emploi_temps_existe'] = isset($seance->emploiTemps);

                    // Vérifier si l'emploi du temps existe
                    if (!$seance->emploiTemps) {
                        $messageErreur = 'L\'emploi du temps associé à cette séance n\'existe pas ou a été supprimé.';
                        $debug['erreur'] = 'emploi_temps_manquant';
                    }
                    // Vérifier si la classe existe
                    elseif (!$seance->emploiTemps->classe) {
                        $messageErreur = 'La classe associée à cet emploi du temps n\'existe pas ou a été supprimée.';
                        $debug['erreur'] = 'classe_manquante';
                    }
                    else {
                        // Récupérer les étudiants directement de la classe sélectionnée
                        // plutôt que de la classe associée à la séance
                        $etudiants = $classe->etudiants;
                        $debug['nombre_etudiants'] = $etudiants->count();
                        $debug['etudiants_ids'] = $etudiants->pluck('id')->toArray();

                        // Vérifier si la classe a des étudiants
                        if ($etudiants->isEmpty()) {
                            $messageErreur = 'Aucun étudiant n\'est inscrit dans cette classe. Veuillez d\'abord inscrire des étudiants.';
                            $debug['erreur'] = 'aucun_etudiant';
                        }

                        // Calculer la date de la séance
                        $dateCalculee = $seance->getDateSeance();
                        if ($dateCalculee) {
                            $dateSeance = $dateCalculee->format('Y-m-d');
                            $debug['date_seance'] = $dateSeance;
                        } else {
                            $messageErreur = 'Impossible de calculer la date de cette séance. Veuillez vérifier les dates de l\'emploi du temps.';
                            $debug['erreur'] = 'date_calcul_impossible';
                            $dateSeance = now()->format('Y-m-d'); // Date par défaut
                        }
                    }
                } else {
                    // Si la classe est sélectionnée mais pas de séance, récupérer quand même les étudiants
                    // pour vérifier s'il y en a dans cette classe
                    $etudiants = $classe->etudiants;
                    $debug['nombre_etudiants_classe'] = $etudiants->count();

                    if ($etudiants->isEmpty()) {
                        $messageErreur = 'Aucun étudiant n\'est inscrit dans cette classe. Veuillez d\'abord inscrire des étudiants.';
                        $debug['erreur'] = 'aucun_etudiant_dans_classe';
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la récupération des données pour le marquage des présences: ' . $e->getMessage());
                $messageErreur = 'Une erreur est survenue lors de la récupération des données: ' . $e->getMessage();
                $debug['exception'] = $e->getMessage();
                $debug['exception_trace'] = $e->getTraceAsString();
            }
        } else {
            // Si aucune classe n'est sélectionnée mais qu'une séance est spécifiée,
            // rediriger vers la page sans paramètres pour éviter les incohérences
            if ($request->filled('seance_id')) {
                return redirect()->route('esbtp.attendances.create')
                    ->with('warning', 'Veuillez d\'abord sélectionner une classe.');
            }
        }

        // Enregistrer les informations de débogage dans le journal
        \Log::info('Débogage marquage présences', $debug);

        return view('esbtp.attendances.create', compact('classes', 'seances', 'etudiants', 'dateSeance', 'messageErreur', 'classeSelectionnee', 'debug'));
    }

    /**
     * Enregistre les présences des étudiants.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valider les données
        $validatedData = $request->validate([
            'seance_cours_id' => 'required|exists:esbtp_seance_cours,id',
            'date' => 'required|date',
            'statuts' => 'required|array',
            'statuts.*' => 'required|in:present,absent,retard,excuse',
            'commentaires' => 'nullable|array',
            'commentaires.*' => 'nullable|string'
        ]);

        // Vérifier que la date correspond au jour de la séance
        $seance = ESBTPSeanceCours::findOrFail($validatedData['seance_cours_id']);
        $dateCalculee = $seance->getDateSeance() ? $seance->getDateSeance()->format('Y-m-d') : null;

        if ($dateCalculee && $dateCalculee != $validatedData['date']) {
            return back()->withInput()->withErrors([
                'date' => 'La date sélectionnée ne correspond pas au jour de la séance dans l\'emploi du temps.'
            ]);
        }

        try {
            DB::beginTransaction();

            foreach ($validatedData['statuts'] as $etudiantId => $statut) {
                // Vérifier si l'enregistrement existe déjà
                $attendance = ESBTPAttendance::where([
                    'seance_cours_id' => $validatedData['seance_cours_id'],
                    'etudiant_id' => $etudiantId,
                    'date' => $validatedData['date']
                ])->first();

                $commentaire = $validatedData['commentaires'][$etudiantId] ?? null;

                if ($attendance) {
                    // Mettre à jour l'enregistrement existant
                    $attendance->update([
                        'statut' => $statut,
                        'commentaire' => $commentaire,
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    // Récupérer les heures de début et de fin de la séance
                    // S'assurer que les valeurs sont au format correct pour la base de données
                    $heureDebut = $seance->heure_debut ? $seance->heure_debut->format('H:i:s') : '08:00:00';
                    $heureFin = $seance->heure_fin ? $seance->heure_fin->format('H:i:s') : '10:00:00';

                    // Créer un nouvel enregistrement
                    ESBTPAttendance::create([
                        'seance_cours_id' => $validatedData['seance_cours_id'],
                        'etudiant_id' => $etudiantId,
                        'date' => $validatedData['date'],
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin,
                        'statut' => $statut,
                        'commentaire' => $commentaire,
                        'created_by' => Auth::id()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('esbtp.attendances.index')
                ->with('success', 'Les présences ont été enregistrées avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des présences: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'une présence.
     *
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPAttendance $attendance)
    {
        return view('esbtp.attendances.show', compact('attendance'));
    }

    /**
     * Affiche le formulaire pour modifier une présence.
     *
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPAttendance $attendance)
    {
        return view('esbtp.attendances.edit', compact('attendance'));
    }

    /**
     * Met à jour une présence.
     *
     * @param Request $request
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPAttendance $attendance)
    {
        // Valider les données
        $validatedData = $request->validate([
            'statut' => 'required|in:present,absent,retard,excuse',
            'commentaire' => 'nullable|string'
        ]);

        // Ajouter l'identifiant de l'utilisateur qui modifie
        $validatedData['updated_by'] = Auth::id();

        // Mettre à jour l'enregistrement
        $attendance->update($validatedData);

        return redirect()->route('esbtp.attendances.index')
            ->with('success', 'La présence a été mise à jour avec succès.');
    }

    /**
     * Supprime une présence.
     *
     * @param ESBTPAttendance $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPAttendance $attendance)
    {
        try {
            $attendance->delete();
            return redirect()->route('esbtp.attendances.index')->with('success', 'Présence supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Génère un rapport de présence.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function rapport(Request $request)
    {
        // Valider les données
        $validatedData = $request->validate([
            'classe_id' => 'required|exists:esbtp_classes,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut'
        ]);

        // Récupérer la classe
        $classe = ESBTPClasse::findOrFail($validatedData['classe_id']);

        // Récupérer les étudiants de la classe
        $etudiants = $classe->etudiants;

        // Récupérer les séances de cours de la classe
        $seances = ESBTPSeanceCours::whereHas('emploiTemps', function($query) use ($classe) {
            $query->where('classe_id', $classe->id);
        })->get();

        // Récupérer les présences pour chaque étudiant
        $statistiques = [];

        foreach ($etudiants as $etudiant) {
            $attendances = ESBTPAttendance::where('etudiant_id', $etudiant->id)
                ->whereHas('seanceCours.emploiTemps', function($query) use ($classe) {
                    $query->where('classe_id', $classe->id);
                })
                ->whereBetween('date', [$validatedData['date_debut'], $validatedData['date_fin']])
                ->get();

            // Calculer les statistiques
            $totalSeances = $seances->count();
            $present = $attendances->where('statut', 'present')->count();
            $absent = $attendances->where('statut', 'absent')->count();
            $retard = $attendances->where('statut', 'retard')->count();
            $excuse = $attendances->where('statut', 'excuse')->count();

            $tauxPresence = $totalSeances > 0 ? round(($present / $totalSeances) * 100, 2) : 0;

            $statistiques[$etudiant->id] = [
                'etudiant' => $etudiant,
                'present' => $present,
                'absent' => $absent,
                'retard' => $retard,
                'excuse' => $excuse,
                'taux_presence' => $tauxPresence
            ];
        }

        return view('esbtp.attendances.rapport', compact('classe', 'etudiants', 'statistiques', 'validatedData'));
    }

    /**
     * Affiche le formulaire pour générer un rapport.
     *
     * @return \Illuminate\Http\Response
     */
    public function rapportForm()
    {
        $classes = ESBTPClasse::all();

        return view('esbtp.attendances.rapport-form', compact('classes'));
    }

    /**
     * Affiche les présences de l'étudiant connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function studentAttendance(Request $request)
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();

        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }

        $absences = ESBTPAttendance::where('etudiant_id', $etudiant->id)
            ->where('statut', 'absent')
            ->orderBy('date', 'desc')
            ->get();

        $presences = ESBTPAttendance::where('etudiant_id', $etudiant->id)
            ->where('statut', 'present')
            ->orderBy('date', 'desc')
            ->get();

        return view('etudiants.attendances', compact('absences', 'presences', 'etudiant'));
    }
}

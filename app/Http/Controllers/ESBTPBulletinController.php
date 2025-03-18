<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPBulletin;
use App\Models\ESBTPResultatMatiere;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPNote;
use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Models\ESBTPAbsence;
use App\Models\ESBTPEvaluation;

class ESBTPBulletinController extends Controller
{
    /**
     * Affiche la liste des bulletins avec filtre par année et classe
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();

        // Périodes disponibles (définir les périodes pour la vue)
        $periodes = [
            (object)['id' => 'semestre1', 'nom' => 'Premier Semestre', 'annee_scolaire' => date('Y') . '-' . (date('Y') + 1)],
            (object)['id' => 'semestre2', 'nom' => 'Deuxième Semestre', 'annee_scolaire' => date('Y') . '-' . (date('Y') + 1)],
            (object)['id' => 'annuel', 'nom' => 'Annuel', 'annee_scolaire' => date('Y') . '-' . (date('Y') + 1)]
        ];

        // Statistiques pour les widgets
        $stats = [
            'total' => ESBTPBulletin::count(),
            'published' => ESBTPBulletin::where('is_published', true)->count(),
            'pending' => ESBTPBulletin::where('is_published', false)->count(),
            'periodes' => count($periodes)
        ];

        // Valeurs par défaut filtre
        $classe_id = $request->input('classe_id');
        $annee_id = $request->input('annee_universitaire_id',
            ESBTPAnneeUniversitaire::where('is_current', true)->first()->id ?? null);
        $periode_id = $request->input('periode_id');

        $query = ESBTPBulletin::with(['etudiant', 'classe', 'anneeUniversitaire']);

        // Application des filtres
        if ($classe_id) {
            $query->where('classe_id', $classe_id);
        }

        if ($annee_id) {
            $query->where('annee_universitaire_id', $annee_id);
        }

        if ($periode_id) {
            $query->where('periode', $periode_id);
        }

        // Utiliser paginate() au lieu de get() pour permettre l'utilisation de appends()
        $bulletins = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('esbtp.bulletins.index', compact(
            'bulletins',
            'classes',
            'anneesUniversitaires',
            'classe_id',
            'annee_id',
            'periodes',
            'periode_id',
            'stats'
        ));
    }

    /**
     * Affiche le formulaire de sélection d'étudiant pour créer un bulletin
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        $anneeActuelle = ESBTPAnneeUniversitaire::where('is_current', true)->first();

        return view('esbtp.bulletins.create', compact('classes', 'anneesUniversitaires', 'anneeActuelle'));
    }

    /**
     * Enregistre un nouveau bulletin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:esbtp_etudiants,id',
            'classe_id' => 'required|exists:esbtp_classes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'periode' => 'required|in:semestre1,semestre2,annuel',
            'appreciation_generale' => 'nullable|string',
            'decision_conseil' => 'nullable|string',
        ], [
            'etudiant_id.required' => 'L\'étudiant est obligatoire',
            'classe_id.required' => 'La classe est obligatoire',
            'annee_universitaire_id.required' => 'L\'année universitaire est obligatoire',
            'periode.required' => 'La période est obligatoire',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier si l'étudiant est bien inscrit dans cette classe pour cette année
            $etudiantInscrit = ESBTPEtudiant::findOrFail($request->etudiant_id)
                ->inscriptions()
                ->where('classe_id', $request->classe_id)
                ->where('annee_universitaire_id', $request->annee_universitaire_id)
                ->exists();

            if (!$etudiantInscrit) {
                return redirect()->back()
                    ->with('error', 'L\'étudiant n\'est pas inscrit dans cette classe pour cette année universitaire')
                    ->withInput();
            }

            // Vérifier s'il existe déjà un bulletin pour cet étudiant, cette classe, cette année et cette période
            $bulletinExistant = ESBTPBulletin::where('etudiant_id', $request->etudiant_id)
                ->where('classe_id', $request->classe_id)
                ->where('annee_universitaire_id', $request->annee_universitaire_id)
                ->where('periode', $request->periode)
                ->exists();

            if ($bulletinExistant) {
                return redirect()->back()
                    ->with('error', 'Un bulletin existe déjà pour cet étudiant pour cette période')
                    ->withInput();
            }

            // Créer le bulletin
            $bulletin = new ESBTPBulletin();
            $bulletin->etudiant_id = $request->etudiant_id;
            $bulletin->classe_id = $request->classe_id;
            $bulletin->annee_universitaire_id = $request->annee_universitaire_id;
            $bulletin->periode = $request->periode;
            $bulletin->appreciation_generale = $request->appreciation_generale;
            $bulletin->decision_conseil = $request->decision_conseil;
            $bulletin->user_id = Auth::id();
            $bulletin->save();

            // Récupérer toutes les matières de la classe
            $classe = ESBTPClasse::findOrFail($request->classe_id);
            $matieres = $classe->matieres;

            // Pour chaque matière, calculer la moyenne et créer un résultat
            foreach ($matieres as $matiere) {
                // Récupérer toutes les évaluations de cette matière pour cette classe
                $evaluations = $matiere ? $matiere->evaluations()
                    ->where('classe_id', $classe->id)
                    ->where('periode', $request->periode)
                    ->get() : collect();

                \Log::info('Récupération des évaluations', [
                    'matiere_id' => $matiere->id,
                    'nombre_evaluations' => $evaluations->count(),
                    'classe_id' => $classe->id,
                    'periode' => $request->periode
                ]);

                if (!$evaluations || $evaluations->isEmpty()) {
                    continue; // Passer à la matière suivante s'il n'y a pas d'évaluations
                }

                // Récupérer les notes de l'étudiant pour ces évaluations
                $notes = ESBTPNote::whereIn('evaluation_id', $evaluations->pluck('id'))
                    ->where('etudiant_id', $request->etudiant_id)
                    ->get();

                if (!$notes || $notes->isEmpty()) {
                    continue; // Passer à la matière suivante s'il n'y a pas de notes
                }

                // Calculer la moyenne
                $sommeNotes = 0;
                $sommeCoefficients = 0;

                foreach ($notes as $note) {
                    $evaluation = $evaluations->where('id', $note->evaluation_id)->first();
                    $sommeNotes += ($note->valeur / $evaluation->bareme) * 20 * $evaluation->coefficient;
                    $sommeCoefficients += $evaluation->coefficient;
                }

                $moyenne = $sommeCoefficients > 0 ? $sommeNotes / $sommeCoefficients : null;

                // Récupérer le coefficient de la matière pour cette classe
                $pivotData = $classe->matieres()->where('matiere_id', $matiere->id)->first()->pivot;
                $coefficient = $pivotData->coefficient ?? 1;

                // Créer le résultat pour cette matière
                $resultat = new ESBTPResultatMatiere();
                $resultat->bulletin_id = $bulletin->id;
                $resultat->matiere_id = $matiere->id;
                $resultat->moyenne = $moyenne;
                $resultat->coefficient = $coefficient;
                $resultat->commentaire = null;
                $resultat->save();
            }

            // Calculer et mettre à jour la moyenne générale du bulletin
            $this->calculerMoyenneGenerale($bulletin);

            DB::commit();
            return redirect()->route('bulletins.show', $bulletin)
                ->with('success', 'Le bulletin a été créé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création du bulletin: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Calcule et met à jour la moyenne générale d'un bulletin
     */
    private function calculerMoyenneGenerale(ESBTPBulletin $bulletin)
    {
        \Log::info('Calcul de la moyenne générale pour le bulletin ' . $bulletin->id);

        try {
            $resultats = $bulletin->resultats;
            \Log::info('Nombre de résultats trouvés: ' . $resultats->count());

            if ($resultats->isEmpty()) {
                \Log::info('Aucun résultat trouvé pour le bulletin ' . $bulletin->id);
                $bulletin->moyenne_generale = null;
                $bulletin->save();
                return;
            }

            $sommePoints = 0;
            $sommeCoefficients = 0;

            foreach ($resultats as $resultat) {
                if ($resultat->moyenne !== null) {
                    \Log::info('Résultat pour matière ' . $resultat->matiere_id . ': moyenne=' . $resultat->moyenne . ', coefficient=' . $resultat->coefficient);
                    $sommePoints += $resultat->moyenne * $resultat->coefficient;
                    $sommeCoefficients += $resultat->coefficient;
                } else {
                    \Log::info('Résultat ignoré pour matière ' . $resultat->matiere_id . ' (moyenne null)');
                }
            }

            \Log::info('Somme des points: ' . $sommePoints . ', Somme des coefficients: ' . $sommeCoefficients);
            $moyenneGenerale = $sommeCoefficients > 0 ? $sommePoints / $sommeCoefficients : null;
            \Log::info('Moyenne générale calculée: ' . $moyenneGenerale);

            $bulletin->moyenne_generale = $moyenneGenerale;
            $bulletin->save();
            \Log::info('Moyenne générale enregistrée pour le bulletin ' . $bulletin->id);

            // Calculer le rang si la moyenne a changé
            $this->calculerRang($bulletin);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul de la moyenne générale: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Calcule et met à jour le rang de l'étudiant dans sa classe
     */
    private function calculerRang($bulletin)
    {
        // Récupérer tous les bulletins de la même classe pour la même période
        $bulletins = ESBTPBulletin::where('classe_id', $bulletin->classe_id)
            ->where('annee_universitaire_id', $bulletin->annee_universitaire_id)
            ->where('periode', $bulletin->periode)
            ->whereNotNull('moyenne_generale')
            ->orderByDesc('moyenne_generale')
            ->get();

        // Mettre à jour l'effectif de la classe
        $bulletin->effectif_classe = $bulletins->count();

        // Trouver le rang de l'étudiant
        foreach ($bulletins as $index => $b) {
            if ($b->id === $bulletin->id) {
                $bulletin->rang = $index + 1;
                break;
            }
        }

        $bulletin->save();
    }

    /**
     * Affiche un bulletin spécifique.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function show(ESBTPBulletin $bulletin)
    {
        $bulletin->load(['etudiant', 'classe', 'anneeUniversitaire', 'resultats.matiere', 'user']);
        return view('esbtp.bulletins.show', compact('bulletin'));
    }

    /**
     * Affiche le formulaire de modification d'un bulletin.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function edit(ESBTPBulletin $bulletin)
    {
        $bulletin->load(['etudiant', 'classe', 'anneeUniversitaire', 'resultats.matiere']);
        return view('esbtp.bulletins.edit', compact('bulletin'));
    }

    /**
     * Met à jour un bulletin spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ESBTPBulletin $bulletin)
    {
        $request->validate([
            'resultats' => 'required|array',
            'resultats.*.matiere_id' => 'required|exists:esbtp_matieres,id',
            'resultats.*.moyenne' => 'nullable|numeric|min:0|max:20',
            'resultats.*.coefficient' => 'required|numeric|min:0',
            'resultats.*.commentaire' => 'nullable|string',
            'appreciation_generale' => 'nullable|string',
            'decision_conseil' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour les informations du bulletin
            $bulletin->appreciation_generale = $request->appreciation_generale;
            $bulletin->decision_conseil = $request->decision_conseil;
            $bulletin->save();

            // Mettre à jour les résultats par matière
            foreach ($request->resultats as $resultatData) {
                $matiereId = $resultatData['matiere_id'];
                $moyenne = $resultatData['moyenne'] !== null && $resultatData['moyenne'] !== ''
                    ? $resultatData['moyenne'] : null;

                $resultat = ESBTPResultatMatiere::where('bulletin_id', $bulletin->id)
                    ->where('matiere_id', $matiereId)
                    ->first();

                if ($resultat) {
                    $resultat->moyenne = $moyenne;
                    $resultat->coefficient = $resultatData['coefficient'];
                    $resultat->commentaire = $resultatData['commentaire'] ?? null;
                    $resultat->save();
                } else {
                    $resultat = new ESBTPResultatMatiere();
                    $resultat->bulletin_id = $bulletin->id;
                    $resultat->matiere_id = $matiereId;
                    $resultat->moyenne = $moyenne;
                    $resultat->coefficient = $resultatData['coefficient'];
                    $resultat->commentaire = $resultatData['commentaire'] ?? null;
                    $resultat->save();
                }
            }

            // Recalculer la moyenne générale
            $this->calculerMoyenneGenerale($bulletin);

            DB::commit();
            return redirect()->route('bulletins.show', $bulletin)
                ->with('success', 'Le bulletin a été mis à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du bulletin: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime un bulletin spécifique.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function destroy(ESBTPBulletin $bulletin)
    {
        try {
            $bulletin->delete();
            return redirect()->route('esbtp.bulletins.index')->with('success', 'Bulletin supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Génère un PDF du bulletin.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function genererPDF(ESBTPBulletin $bulletin)
    {
        try {
            \Log::info('Début de la génération du PDF pour le bulletin #' . $bulletin->id);

            // Charger toutes les relations nécessaires avec eager loading, y compris les relations imbriquées
            $bulletin->load([
                'etudiant',
                'classe.niveauEtude',
                'classe.filiere',
                'anneeUniversitaire',
                'resultats.matiere',
                'user'
            ]);

            // Vérifier que les relations essentielles sont chargées
            if (!$bulletin->etudiant) {
                \Log::error('Relation etudiant manquante pour le bulletin #' . $bulletin->id);
                throw new \Exception("L'étudiant associé à ce bulletin n'a pas été trouvé. Veuillez vérifier que l'étudiant existe et est correctement associé au bulletin.");
            }

            if (!$bulletin->classe) {
                \Log::error('Relation classe manquante pour le bulletin #' . $bulletin->id);
                throw new \Exception("La classe associée à ce bulletin n'a pas été trouvée. Veuillez vérifier que la classe existe et est correctement associée au bulletin.");
            }

            if (!$bulletin->anneeUniversitaire) {
                \Log::error('Relation anneeUniversitaire manquante pour le bulletin #' . $bulletin->id);
                throw new \Exception("L'année universitaire associée à ce bulletin n'a pas été trouvée. Veuillez vérifier que l'année universitaire existe et est correctement associée au bulletin.");
            }

            // Calculer la moyenne générale si pas déjà fait
            if (!$bulletin->moyenne_generale) {
                try {
                    $bulletin->calculerMoyenneGenerale();
                } catch (\Exception $e) {
                    \Log::error('Erreur lors du calcul de la moyenne générale: ' . $e->getMessage());
                    \Log::error('Trace: ' . $e->getTraceAsString());
                    $bulletin->moyenne_generale = 0;
                }
            }

            // Calculer la mention si pas déjà fait
            if (!$bulletin->mention) {
                try {
                    $bulletin->calculerMention();
                } catch (\Exception $e) {
                    \Log::error('Erreur lors du calcul de la mention: ' . $e->getMessage());
                    \Log::error('Trace: ' . $e->getTraceAsString());
                    $bulletin->mention = 'Non calculée';
                }
            }

            // Calculer le rang si pas déjà fait
            if (!$bulletin->rang) {
                try {
                    $bulletin->calculerRang();
                } catch (\Exception $e) {
                    \Log::error('Erreur lors du calcul du rang: ' . $e->getMessage());
                    \Log::error('Trace: ' . $e->getTraceAsString());
                    $bulletin->rang = 0;
                }
            }

            // Calculer les absences justifiées et non justifiées
            try {
                $absences = $this->calculerAbsencesDetailees($bulletin);
                $bulletin->absences_justifiees = $absences['justifiees'];
                $bulletin->absences_non_justifiees = $absences['non_justifiees'];
                $bulletin->total_absences = $absences['total'];
            } catch (\Exception $e) {
                \Log::error('Erreur lors du calcul des absences: ' . $e->getMessage());
                \Log::error('Trace: ' . $e->getTraceAsString());
                $bulletin->absences_justifiees = 0;
                $bulletin->absences_non_justifiees = 0;
                $bulletin->total_absences = 0;
            }

            // Grouper les résultats par type d'enseignement (général ou technique)
            try {
                // S'assurer que les résultats sont chargés
                if ($bulletin->resultats->isEmpty()) {
                    \Log::warning('Aucun résultat trouvé pour le bulletin #' . $bulletin->id);
                }

                // Vérifier que chaque résultat a une matière associée
                foreach ($bulletin->resultats as $resultat) {
                    if (!$resultat->matiere) {
                        \Log::warning('Résultat #' . $resultat->id . ' sans matière associée pour le bulletin #' . $bulletin->id);
                    }
                }

                $resultatsGeneraux = $bulletin->resultats->filter(function($resultat) {
                    return $resultat->matiere && $resultat->matiere->type_formation == 'generale';
                });

                $resultatsTechniques = $bulletin->resultats->filter(function($resultat) {
                    return $resultat->matiere && $resultat->matiere->type_formation == 'technologique_professionnelle';
                });

                // Vérifier si des résultats ont été trouvés après filtrage
                if ($resultatsGeneraux->isEmpty() && $resultatsTechniques->isEmpty()) {
                    \Log::warning('Aucun résultat trouvé après filtrage par type de formation pour le bulletin #' . $bulletin->id);
                }
            } catch (\Exception $e) {
                \Log::error('Erreur lors du filtrage des résultats: ' . $e->getMessage());
                \Log::error('Trace: ' . $e->getTraceAsString());
                $resultatsGeneraux = collect();
                $resultatsTechniques = collect();
            }

            // Calculer les moyennes par type d'enseignement
            try {
                $moyenneGenerale = $bulletin->calculerMoyenneParType('generale');
                $moyenneTechnique = $bulletin->calculerMoyenneParType('technologique_professionnelle');
            } catch (\Exception $e) {
                \Log::error('Erreur lors du calcul des moyennes par type: ' . $e->getMessage());
                \Log::error('Trace: ' . $e->getTraceAsString());
                $moyenneGenerale = 0;
                $moyenneTechnique = 0;
            }

            // Générer le PDF avec les configurations de l'école
            $data = [
                'bulletin' => $bulletin,
                'resultatsGeneraux' => $resultatsGeneraux,
                'resultatsTechniques' => $resultatsTechniques,
                'moyenneGenerale' => $moyenneGenerale,
                'moyenneTechnique' => $moyenneTechnique,
                'config' => [
                    'school_name' => config('school.name', 'École Spéciale du Bâtiment et des Travaux Publics'),
                    'school_type' => config('school.type', 'Enseignement Supérieur Technique'),
                    'school_authorization' => config('school.authorization', ''),
                    'school_address' => config('school.address', 'BP 2541 Yamoussoukro'),
                    'school_phone' => config('school.phone', 'Tél/Fax: 30 64 39 93 - Cel: 05 93 34 26 : 07 72 88 56'),
                    'school_email' => config('school.email', 'esbtp@aviso.ci'),
                    'school_logo' => config('school.logo', 'images/esbtp_logo.png'),
                ]
            ];

            try {
                \Log::info('Chargement de la vue PDF pour le bulletin #' . $bulletin->id);
                $pdf = PDF::loadView('esbtp.bulletins.pdf', $data);
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

                // Nom du fichier PDF
                $filename = 'bulletin_' .
                            ($bulletin->etudiant ? $bulletin->etudiant->matricule : 'unknown') . '_' .
                            ($bulletin->classe ? $bulletin->classe->code : 'unknown') . '_' .
                            $bulletin->periode . '_' .
                            ($bulletin->anneeUniversitaire ? $bulletin->anneeUniversitaire->libelle : 'unknown') . '.pdf';

                \Log::info('PDF généré avec succès pour le bulletin #' . $bulletin->id);
                // Télécharger le PDF
                return $pdf->download($filename);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la génération du PDF: ' . $e->getMessage());
                \Log::error('Trace: ' . $e->getTraceAsString());

                // Enregistrer des informations supplémentaires pour le débogage
                \Log::error('Données du bulletin: ' . json_encode([
                    'id' => $bulletin->id,
                    'etudiant_id' => $bulletin->etudiant_id,
                    'classe_id' => $bulletin->classe_id,
                    'annee_universitaire_id' => $bulletin->annee_universitaire_id,
                    'periode' => $bulletin->periode,
                ]));

                return back()->with('error', 'Une erreur est survenue lors de la génération du PDF: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la préparation des données pour le PDF: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            // Enregistrer des informations supplémentaires pour le débogage
            if (isset($bulletin)) {
                \Log::error('Données du bulletin: ' . json_encode([
                    'id' => $bulletin->id,
                    'etudiant_id' => $bulletin->etudiant_id,
                    'classe_id' => $bulletin->classe_id,
                    'annee_universitaire_id' => $bulletin->annee_universitaire_id,
                    'periode' => $bulletin->periode,
                ]));
            }

            return back()->with('error', 'Une erreur est survenue lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    /**
     * Calcule les absences justifiées et non justifiées pour un bulletin.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return array
     */
    private function calculerAbsencesDetailees($bulletin)
    {
        try {
            \Log::info('Début du calcul des absences détaillées pour le bulletin #' . $bulletin->id);

            // Vérifier que les relations nécessaires sont chargées
            if (!$bulletin->etudiant || !$bulletin->classe || !$bulletin->anneeUniversitaire) {
                \Log::error('Relations essentielles manquantes pour le calcul des absences du bulletin #' . $bulletin->id);
                throw new \Exception("Données incomplètes pour calculer les absences. Veuillez vérifier que l'étudiant, la classe et l'année universitaire sont correctement définis.");
            }

            // Vérifier que les dates de l'année universitaire sont définies
            if (!$bulletin->anneeUniversitaire->date_debut || !$bulletin->anneeUniversitaire->date_fin) {
                \Log::error('Dates de l\'année universitaire non définies pour le bulletin #' . $bulletin->id);
                throw new \Exception("Les dates de début et de fin de l'année universitaire ne sont pas définies.");
            }

            // Absences justifiées
            try {
                $absencesJustifiees = ESBTPAbsence::where('etudiant_id', $bulletin->etudiant_id)
                    ->whereHas('cours', function ($query) use ($bulletin) {
                        $query->whereHas('matiere', function ($q) use ($bulletin) {
                            $q->whereHas('classes', function ($c) use ($bulletin) {
                                $c->where('classe_id', $bulletin->classe_id);
                            });
                        });
                    })
                    ->where('date', '>=', $bulletin->anneeUniversitaire->date_debut)
                    ->where('date', '<=', $bulletin->anneeUniversitaire->date_fin)
                    ->where('justified', true)
                    ->sum('hours');

                \Log::info('Absences justifiées calculées: ' . $absencesJustifiees . ' heures');
            } catch (\Exception $e) {
                \Log::error('Erreur lors du calcul des absences justifiées: ' . $e->getMessage());
                \Log::error('Trace: ' . $e->getTraceAsString());
                $absencesJustifiees = 0;
            }

            // Absences non justifiées
            try {
                $absencesNonJustifiees = ESBTPAbsence::where('etudiant_id', $bulletin->etudiant_id)
                    ->whereHas('cours', function ($query) use ($bulletin) {
                        $query->whereHas('matiere', function ($q) use ($bulletin) {
                            $q->whereHas('classes', function ($c) use ($bulletin) {
                                $c->where('classe_id', $bulletin->classe_id);
                            });
                        });
                    })
                    ->where('date', '>=', $bulletin->anneeUniversitaire->date_debut)
                    ->where('date', '<=', $bulletin->anneeUniversitaire->date_fin)
                    ->where('justified', false)
                    ->sum('hours');

                \Log::info('Absences non justifiées calculées: ' . $absencesNonJustifiees . ' heures');
            } catch (\Exception $e) {
                \Log::error('Erreur lors du calcul des absences non justifiées: ' . $e->getMessage());
                \Log::error('Trace: ' . $e->getTraceAsString());
                $absencesNonJustifiees = 0;
            }

            $total = $absencesJustifiees + $absencesNonJustifiees;
            \Log::info('Total des absences calculées: ' . $total . ' heures');

            return [
                'justifiees' => $absencesJustifiees,
                'non_justifiees' => $absencesNonJustifiees,
                'total' => $total
            ];
        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul des absences: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            // Enregistrer des informations supplémentaires pour le débogage
            if (isset($bulletin)) {
                \Log::error('Données du bulletin: ' . json_encode([
                    'id' => $bulletin->id,
                    'etudiant_id' => $bulletin->etudiant_id,
                    'classe_id' => $bulletin->classe_id,
                    'annee_universitaire_id' => $bulletin->annee_universitaire_id,
                    'periode' => $bulletin->periode,
                ]));
            }

            // Retourner des valeurs par défaut en cas d'erreur
            return [
                'justifiees' => 0,
                'non_justifiees' => 0,
                'total' => 0
            ];
        }
    }

    /**
     * Calcule le total des heures d'absence pour un bulletin.
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return int
     */
    private function calculerTotalAbsences($bulletin)
    {
        return ESBTPAbsence::where('etudiant_id', $bulletin->etudiant_id)
            ->whereHas('cours', function ($query) use ($bulletin) {
                $query->whereHas('matiere', function ($q) use ($bulletin) {
                    $q->whereHas('classes', function ($c) use ($bulletin) {
                        $c->where('classe_id', $bulletin->classe_id);
                    });
                });
            })
            ->where('date', '>=', $bulletin->anneeUniversitaire->date_debut)
            ->where('date', '<=', $bulletin->anneeUniversitaire->date_fin)
            ->sum('hours');
    }

    /**
     * Génère les bulletins pour une classe entière.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function genererClasseBulletins(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:esbtp_classes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id',
            'periode' => 'required|in:semestre1,semestre2,annuel',
        ]);

        try {
            \Log::info('Début de la génération des bulletins', $request->all());
            $classe = ESBTPClasse::findOrFail($request->classe_id);
            $anneeUniversitaire = ESBTPAnneeUniversitaire::findOrFail($request->annee_universitaire_id);

            // Récupérer tous les étudiants inscrits dans cette classe pour cette année
            try {
                \Log::info('Récupération des étudiants inscrits');

                // Utiliser une requête directe à la place de la relation 'inscriptions'
                $etudiantIds = DB::table('esbtp_inscriptions')
                    ->where('classe_id', $request->classe_id)
                    ->where('annee_universitaire_id', $request->annee_universitaire_id)
                    ->where('status', 'active')
                    ->pluck('etudiant_id');

                $etudiants = ESBTPEtudiant::whereIn('id', $etudiantIds)->get();

                // Si aucun étudiant n'est trouvé par cette méthode, essayer de récupérer tous les étudiants de la classe
                if ($etudiants->isEmpty()) {
                    \Log::info('Aucun étudiant trouvé via les inscriptions, recherche alternative');
                    $etudiants = ESBTPEtudiant::where('classe_id', $request->classe_id)->get();
                }

                \Log::info('Nombre d\'étudiants trouvés: ' . $etudiants->count());

                if ($etudiants->isEmpty()) {
                    \Log::warning('Aucun étudiant trouvé pour la classe ' . $classe->name);
                    return redirect()->route('esbtp.bulletins.index')
                        ->with('warning', 'Aucun étudiant trouvé pour la classe sélectionnée.');
                }
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la récupération des étudiants: ' . $e->getMessage());
                \Log::error('SQL: ' . $e->getTraceAsString());
                throw $e;
            }

            $bulletinsGeneres = 0;

            foreach ($etudiants as $etudiant) {
                \Log::info('Traitement de l\'étudiant: ' . $etudiant->id . ' - ' . $etudiant->nom . ' ' . $etudiant->prenoms);
                // Vérifier si un bulletin existe déjà pour cet étudiant
                try {
                    $bulletinExistant = ESBTPBulletin::where('etudiant_id', $etudiant->id)
                        ->where('classe_id', $request->classe_id)
                        ->where('annee_universitaire_id', $request->annee_universitaire_id)
                        ->where('periode', $request->periode)
                        ->exists();

                    if ($bulletinExistant) {
                        \Log::info('Bulletin existant pour l\'étudiant: ' . $etudiant->id);
                        continue; // Passer à l'étudiant suivant
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de la vérification du bulletin existant: ' . $e->getMessage());
                    \Log::error('SQL: ' . $e->getTraceAsString());
                    throw $e;
                }

                // Créer une requête simulée pour réutiliser la méthode store
                $bulletinRequest = new Request([
                    'etudiant_id' => $etudiant->id,
                    'classe_id' => $request->classe_id,
                    'annee_universitaire_id' => $request->annee_universitaire_id,
                    'periode' => $request->periode,
                    'appreciation_generale' => null,
                    'decision_conseil' => null,
                ]);

                // Appeler la méthode store mais sans rediriger
                try {
                    DB::beginTransaction();

                    // Créer le bulletin
                    $bulletin = new ESBTPBulletin();
                    $bulletin->etudiant_id = $etudiant->id;
                    $bulletin->classe_id = $request->classe_id;
                    $bulletin->annee_universitaire_id = $request->annee_universitaire_id;
                    $bulletin->periode = $request->periode;
                    $bulletin->appreciation_generale = null;
                    $bulletin->decision_conseil = null;
                    $bulletin->user_id = Auth::id();
                    $bulletin->save();
                    \Log::info('Bulletin créé: ' . $bulletin->id);

                    // Récupérer toutes les matières de la classe
                    $matieres = $classe->matieres;
                    \Log::info('Nombre de matières trouvées: ' . $matieres->count());

                    // Pour chaque matière, calculer la moyenne et créer un résultat
                    foreach ($matieres as $matiere) {
                        \Log::info('Traitement de la matière: ' . $matiere->id . ' - ' . ($matiere->nom ?? $matiere->name ?? 'Nom inconnu'));

                        // Vérifier si la matière est valide
                        if (!$matiere || !$matiere->id) {
                            \Log::warning('Matière invalide trouvée');
                            continue;
                        }

                        // Récupérer toutes les évaluations de cette matière pour cette classe
                        try {
                            $evaluations = $matiere->evaluations()
                                ->where('classe_id', $classe->id)
                                ->where('periode', $request->periode)
                                ->get();

                            \Log::info('Nombre d\'évaluations trouvées: ' . $evaluations->count(), [
                                'matiere_id' => $matiere->id,
                                'classe_id' => $classe->id,
                                'periode' => $request->periode
                            ]);

                            if (!$evaluations || $evaluations->isEmpty()) {
                                \Log::info('Pas d\'évaluations pour la matière et la période: ' . $matiere->id, [
                                    'periode' => $request->periode
                                ]);

                                // Créer un résultat vide pour cette matière
                                try {
                                    // Récupérer le coefficient de la matière pour cette classe
                                    $coefficient = 1; // Valeur par défaut
                                    try {
                                        $pivot = DB::table('esbtp_classe_matiere')
                                            ->where('classe_id', $classe->id)
                                            ->where('matiere_id', $matiere->id)
                                            ->first();

                                        if ($pivot && isset($pivot->coefficient)) {
                                            $coefficient = $pivot->coefficient;
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error('Erreur lors de la récupération du coefficient: ' . $e->getMessage());
                                    }

                                    $resultat = new ESBTPResultatMatiere();
                                    $resultat->bulletin_id = $bulletin->id;
                                    $resultat->matiere_id = $matiere->id;
                                    $resultat->moyenne = null; // Pas de moyenne car pas d'évaluations
                                    $resultat->coefficient = $coefficient;
                                    $resultat->commentaire = null;
                                    $resultat->save();
                                    \Log::info('Résultat vide créé pour la matière: ' . $matiere->id);
                                } catch (\Exception $e) {
                                    \Log::error('Erreur lors de la création du résultat vide: ' . $e->getMessage());
                                }

                                continue; // Passer à la matière suivante s'il n'y a pas d'évaluations
                            }
                        } catch (\Exception $e) {
                            \Log::error('Erreur lors de la récupération des évaluations: ' . $e->getMessage());
                            \Log::error('SQL: ' . $e->getTraceAsString());
                            continue; // Passer à la matière suivante en cas d'erreur
                        }

                        // Récupérer les notes de l'étudiant pour ces évaluations
                        try {
                            $notes = ESBTPNote::where('etudiant_id', $etudiant->id)
                                ->where('classe_id', $request->classe_id)
                                ->whereHas('evaluation', function($query) use ($request) {
                                    $query->where('annee_universitaire_id', $request->annee_universitaire_id);
                                    if ($request->periode != 'annuel') {
                                        $query->where('periode', $request->periode);
                                    }
                                })
                                ->get();

                            \Log::info('Nombre de notes trouvées: ' . $notes->count());

                            if (!$notes || $notes->isEmpty()) {
                                \Log::info('Pas de notes pour l\'étudiant: ' . $etudiant->id . ' dans la matière: ' . $matiere->id);
                                continue; // Passer à la matière suivante s'il n'y a pas de notes
                            }
                        } catch (\Exception $e) {
                            \Log::error('Erreur lors de la récupération des notes: ' . $e->getMessage());
                            \Log::error('SQL: ' . $e->getTraceAsString());
                            throw $e;
                        }

                        // Calculer la moyenne
                        $sommeNotes = 0;
                        $sommeCoefficients = 0;

                        foreach ($notes as $note) {
                            $evaluation = $notes->where('evaluation_id', $note->evaluation_id)->first();
                            $sommeNotes += ($note->valeur / $evaluation->bareme) * 20 * $evaluation->coefficient;
                            $sommeCoefficients += $evaluation->coefficient;
                        }

                        $moyenne = $sommeCoefficients > 0 ? $sommeNotes / $sommeCoefficients : null;

                        // Récupérer le coefficient de la matière pour cette classe
                        try {
                            $pivotData = $classe->matieres()->where('matiere_id', $matiere->id)->first()->pivot;
                            $coefficient = $pivotData->coefficient ?? 1;
                        } catch (\Exception $e) {
                            \Log::error('Erreur lors de la récupération du coefficient: ' . $e->getMessage());
                            \Log::error('SQL: ' . $e->getTraceAsString());
                            $coefficient = 1; // Valeur par défaut en cas d'erreur
                        }

                        // Créer le résultat pour cette matière
                        try {
                            $resultat = new ESBTPResultatMatiere();
                            $resultat->bulletin_id = $bulletin->id;
                            $resultat->matiere_id = $matiere->id;
                            $resultat->moyenne = $moyenne;
                            $resultat->coefficient = $coefficient;
                            $resultat->commentaire = null;
                            $resultat->save();
                            \Log::info('Résultat créé pour la matière: ' . $matiere->id . ' avec moyenne: ' . $moyenne);
                        } catch (\Exception $e) {
                            \Log::error('Erreur lors de la création du résultat: ' . $e->getMessage());
                            \Log::error('SQL: ' . $e->getTraceAsString());
                            throw $e;
                        }
                    }

                    // Calculer et mettre à jour la moyenne générale du bulletin
                    try {
                        \Log::info('Calcul de la moyenne générale pour le bulletin: ' . $bulletin->id);
                        $this->calculerMoyenneGenerale($bulletin);
                    } catch (\Exception $e) {
                        \Log::error('Erreur lors du calcul de la moyenne générale: ' . $e->getMessage());
                        \Log::error('SQL: ' . $e->getTraceAsString());
                        throw $e;
                    }

                    DB::commit();
                    $bulletinsGeneres++;
                    \Log::info('Bulletin généré avec succès pour l\'étudiant: ' . $etudiant->id);
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Erreur lors de la génération du bulletin pour l\'étudiant: ' . $etudiant->id . ' - ' . $e->getMessage());
                    \Log::error('SQL: ' . $e->getTraceAsString());
                    // Continuer avec l'étudiant suivant
                }
            }

            if ($bulletinsGeneres > 0) {
                \Log::info('Bulletins générés avec succès: ' . $bulletinsGeneres);
                return redirect()->route('esbtp.bulletins.index')
                    ->with('success', $bulletinsGeneres . ' bulletins ont été générés avec succès');
            } else {
                \Log::info('Aucun bulletin généré');
                return redirect()->route('esbtp.bulletins.index')
                    ->with('info', 'Aucun nouveau bulletin n\'a été généré. Tous les bulletins existent déjà ou il n\'y a pas de données suffisantes.');
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération des bulletins: ' . $e->getMessage());
            \Log::error('SQL: ' . $e->getTraceAsString());

            return redirect()->route('esbtp.bulletins.index')
                ->with('error', 'Une erreur est survenue lors de la génération des bulletins: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la page de sélection pour les bulletins
     *
     * @return \Illuminate\Http\Response
     */
    public function select()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
        $anneeActuelle = ESBTPAnneeUniversitaire::where('is_current', true)->first();

        return view('esbtp.bulletins.select', compact('classes', 'anneesUniversitaires', 'anneeActuelle'));
    }

    /**
     * Affiche les résultats des étudiants
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resultats(Request $request)
    {
        // Récupération des filtres
        $classe_id = $request->input('classe_id');

        // Fix: Safely handling potential null value from first() before accessing ->id
        $currentAnnee = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        $annee_id = $request->input('annee_universitaire_id', $currentAnnee ? $currentAnnee->id : null);

        $periode = $request->input('periode', 'semestre1');

        // Vérifier les années universitaires manquantes avant de récupérer les notes
        $this->verifierAnneesUniversitaires();

        // Récupération des classes et années pour le filtre
        $classes = ESBTPClasse::with(['filiere', 'niveau'])->orderBy('name')->get();
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderByDesc('annee_debut')->get();

        // Proceed if we have an academic year (always should have a default)
        if ($annee_id) {
            $anneeUniversitaire = ESBTPAnneeUniversitaire::findOrFail($annee_id);

            // Get the selected class if specified
            $classe = null;
            if ($classe_id) {
                $classe = ESBTPClasse::with(['filiere', 'niveau'])->findOrFail($classe_id);
            }

            // Query to get students based on filters
            $studentsQuery = ESBTPEtudiant::query();

            // If a class is selected, get only students from that class
            if ($classe_id) {
                $studentsQuery = $studentsQuery->whereHas('inscriptions', function ($query) use ($classe_id, $annee_id) {
                    $query->where('classe_id', $classe_id)
                          ->where('annee_universitaire_id', $annee_id)
                          ->where('statut', 'active');
                });

                // Fallback: If no students found in inscriptions, try direct class relation
                if ($studentsQuery->count() === 0) {
                    $studentsQuery = ESBTPEtudiant::where('classe_id', $classe_id);
                }
            } else {
                // If no class is selected, get all students enrolled in the academic year
                $studentsQuery = $studentsQuery->whereHas('inscriptions', function ($query) use ($annee_id) {
                    $query->where('annee_universitaire_id', $annee_id)
                          ->where('statut', 'active');
                });
            }

            $etudiants = $studentsQuery->orderBy('nom')->orderBy('prenoms')->get();

            \Log::info('Requête résultats: Trouvé ' . $etudiants->count() . ' étudiants', [
                'classe_id' => $classe_id,
                'annee_id' => $annee_id,
                'periode' => $periode
            ]);

            // Récupération des matières enseignées (all matières if no class selected)
            $matieres = ESBTPMatiere::query();
            if ($classe_id) {
                $matieres->whereHas('classes', function($query) use ($classe_id) {
                    $query->where('classe_id', $classe_id);
                });
            }
            $matieres = $matieres->get();

            // Initialize arrays to store results
            $moyennes = [];
            $rangs = [];
            $bulletins = [];
            $resultats = []; // Initialize $resultats array

            foreach ($etudiants as $etudiant) {
                // Initialize $moyenne to null or a default value at the start of each iteration
                $moyenne = null;

                // Récupération des notes de l'étudiant pour cette période
                $notesQuery = ESBTPNote::where('etudiant_id', $etudiant->id)
                    ->whereHas('evaluation', function($query) use ($annee_id) {
                        $query->where('annee_universitaire_id', $annee_id);
                    });

                // Filter by class if specified
                if ($classe_id) {
                    $notesQuery->where(function($query) use ($classe_id) {
                        $query->where('classe_id', $classe_id)
                              ->orWhereHas('evaluation', function($q) use ($classe_id) {
                                  $q->where('classe_id', $classe_id);
                              });
                    });
                }

                // Filter by period if not 'annuel'
                if ($periode !== 'annuel') {
                    $notesQuery->where(function($query) use ($periode) {
                        $query->where('semestre', $periode)
                            ->orWhereHas('evaluation', function($q) use ($periode) {
                                $q->where('periode', $periode);
                            });
                    });
                }

                $notes = $notesQuery->get();

                \Log::info('Notes pour étudiant ' . $etudiant->id . ': ' . $notes->count());

                if ($notes->isNotEmpty()) {
                    // Vérifier que toutes les notes ont une évaluation valide
                    $validNotes = $notes->filter(function($note) {
                        return $note->evaluation && $note->evaluation->coefficient > 0;
                    });

                    if ($validNotes->isEmpty()) {
                        continue;
                    }

                    // Nouveau calcul plus robuste
                    $totalPondere = $validNotes->sum(function($note) {
                        return $note->note_vingt * $note->evaluation->coefficient;
                    });

                    $totalCoefficients = $validNotes->sum('evaluation.coefficient');

                    $moyenne = $totalCoefficients > 0 ? $totalPondere / $totalCoefficients : 0;

                    // Store moyenne for display
                    $moyennes[$etudiant->id] = $moyenne;
                }

                // Ajouter les résultats de l'étudiant
                $resultats[] = [
                    'etudiant' => $etudiant,
                    'moyenne' => $moyenne,
                    'notes_count' => $notes->count()
                ];
            }

            // Trier les résultats par moyenne décroissante
            usort($resultats, function($a, $b) {
                return ($b['moyenne'] ?? 0) <=> ($a['moyenne'] ?? 0); // Handle null values
            });

            // Calculate ranks
            $rank = 1;
            foreach ($resultats as $index => $result) {
                if ($result['moyenne'] !== null) {
                    if ($index > 0 && $resultats[$index-1]['moyenne'] == $result['moyenne']) {
                        // Same rank for same moyenne
                        $rangs[$result['etudiant']->id] = $rangs[$resultats[$index-1]['etudiant']->id];
                    } else {
                        $rangs[$result['etudiant']->id] = $rank;
                    }
                    $rank++;
                }
            }

            // Périodes disponibles
            $periodes = [
                (object)['id' => 'semestre1', 'nom' => 'Premier Semestre'],
                (object)['id' => 'semestre2', 'nom' => 'Deuxième Semestre'],
                (object)['id' => 'annuel', 'nom' => 'Annuel']
            ];

            // Récupérer tous les bulletins des étudiants
            $bulletinsCollection = ESBTPBulletin::whereIn('etudiant_id', $etudiants->pluck('id'))
                ->where('annee_universitaire_id', $annee_id)
                ->where('periode', $periode)
                ->get();

            foreach ($bulletinsCollection as $bulletin) {
                $bulletins[$bulletin->etudiant_id] = $bulletin->id;
            }

            // Récupérer toutes les notes pour vérifier si elles sont vides
            $notesQuery = ESBTPNote::whereIn('etudiant_id', $etudiants->pluck('id'))
                ->where(function($query) use ($periode) {
                    $query->where('semestre', $periode)
                        ->orWhereHas('evaluation', function($q) use ($periode) {
                            $q->where('periode', $periode);
                        });
                })
                ->whereHas('evaluation', function($query) use ($annee_id) {
                    $query->where('annee_universitaire_id', $annee_id);
                });

            if ($classe_id) {
                $notesQuery->where('classe_id', $classe_id);
            }

            $notes = $notesQuery->get();

            return view('esbtp.resultats.index', compact(
                'classes',
                'anneesUniversitaires',
                'classe',
                'classe_id',
                'annee_id',
                'anneeUniversitaire',
                'periode',
                'etudiants',
                'moyennes',
                'rangs',
                'bulletins',
                'resultats',
                'notes'
            ));
        }

        // If no academic year is found or selected (shouldn't happen with defaults)
        return view('esbtp.resultats.index', compact('classes', 'anneesUniversitaires'));
    }

    /**
     * Affiche le bulletin de l'étudiant connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function monBulletin(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Récupérer l'étudiant associé à l'utilisateur
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre compte utilisateur n\'est pas associé à un étudiant.');
        }

        // Récupérer les paramètres de filtre
        $anneeId = $request->input('annee_universitaire_id',
            ESBTPAnneeUniversitaire::where('is_current', true)->first()->id ?? null);
        $periode = $request->input('periode');

        // Récupérer l'inscription active de l'étudiant
        $inscription = $etudiant->inscriptions()
            ->where('annee_universitaire_id', $anneeId)
            ->where('statut', 'active')
            ->first();

        if (!$inscription) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas inscrit pour l\'année universitaire sélectionnée.');
        }

        // Récupérer la classe de l'étudiant
        $classe = $inscription->classe;

        if (!$classe) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre inscription n\'est associée à aucune classe.');
        }

        // Récupérer le bulletin de l'étudiant
        $bulletin = ESBTPBulletin::where('etudiant_id', $etudiant->id)
            ->where('annee_universitaire_id', $anneeId);

        if ($periode) {
            $bulletin = $bulletin->where('periode', $periode);
        }

        $bulletin = $bulletin->first();

        // Si le bulletin n'existe pas encore, on affiche un message
        if (!$bulletin) {
            // Récupérer toutes les années universitaires pour le filtre
            $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();

            return view('esbtp.bulletin.mon-bulletin', compact(
                'etudiant',
                'classe',
                'anneeId',
                'periode',
                'anneesUniversitaires'
            ))->with('warning', 'Le bulletin n\'est pas encore disponible pour la période sélectionnée.');
        }

        // Récupérer les détails du bulletin
        $detailsBulletin = ESBTPBulletinDetail::where('bulletin_id', $bulletin->id)
            ->with(['matiere'])
            ->get();

        // Regrouper les détails par UE si nécessaire
        $detailsParUE = [];

        foreach ($detailsBulletin as $detail) {
            $ueId = $detail->matiere->ue_id ?? 'sans_ue';
            if (!isset($detailsParUE[$ueId])) {
                $detailsParUE[$ueId] = [
                    'ue' => $detail->matiere->ue ?? null,
                    'details' => []
                ];
            }
            $detailsParUE[$ueId]['details'][] = $detail;
        }

        // Calculer les statistiques globales
        $moyenneGenerale = $bulletin->moyenne_generale;
        $rangGeneral = $bulletin->rang;
        $effectifClasse = $bulletin->effectif_classe;
        $creditsTotaux = $detailsBulletin->sum('credits_valides');
        $decisionConseil = $bulletin->decision_conseil;

        // Récupérer toutes les années universitaires pour le filtre
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();

        return view('esbtp.bulletin.mon-bulletin', compact(
            'etudiant',
            'classe',
            'bulletin',
            'detailsBulletin',
            'detailsParUE',
            'moyenneGenerale',
            'rangGeneral',
            'effectifClasse',
            'creditsTotaux',
            'decisionConseil',
            'anneeId',
            'periode',
            'anneesUniversitaires'
        ));
    }

    /**
     * Affiche les bulletins de l'étudiant connecté.
     *
     * @return \Illuminate\Http\Response
     */
    public function studentBulletins()
    {
        $user = Auth::user();
        $etudiant = ESBTPEtudiant::where('user_id', $user->id)->first();

        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }

        $bulletins = ESBTPBulletin::where('etudiant_id', $etudiant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('etudiants.bulletins', compact('bulletins', 'etudiant'));
    }

    /**
     * Signe un bulletin par un responsable
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @param  string  $role
     * @return \Illuminate\Http\Response
     */
    public function signer(ESBTPBulletin $bulletin, $role)
    {
        if (!in_array($role, ['directeur', 'responsable', 'parent'])) {
            return back()->with('error', 'Rôle de signature invalide.');
        }

        try {
            $bulletin->signer($role);
            return back()->with('success', 'Bulletin signé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la signature: ' . $e->getMessage());
        }
    }

    /**
     * Bascule l'état de publication d'un bulletin
     *
     * @param  \App\Models\ESBTPBulletin  $bulletin
     * @return \Illuminate\Http\Response
     */
    public function togglePublication(ESBTPBulletin $bulletin)
    {
        try {
            $bulletin->is_published = !$bulletin->is_published;
            $bulletin->save();

            $message = $bulletin->is_published
                ? 'Le bulletin a été publié avec succès.'
                : 'Le bulletin a été dépublié avec succès.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les bulletins en attente (non publiés ou non signés)
     *
     * @return \Illuminate\Http\Response
     */
    public function pending()
    {
        // Récupérer les bulletins qui ne sont pas publiés ou qui n'ont pas toutes les signatures
        $bulletins = ESBTPBulletin::where('is_published', false)
            ->orWhere(function($query) {
                $query->where('signature_responsable', false)
                      ->orWhere('signature_directeur', false);
            })
            ->with(['etudiant', 'classe', 'anneeUniversitaire'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistiques
        $totalPending = ESBTPBulletin::where('is_published', false)->count();
        $totalNonSigned = ESBTPBulletin::where('is_published', true)
            ->where(function($query) {
                $query->where('signature_responsable', false)
                      ->orWhere('signature_directeur', false);
            })->count();

        return view('esbtp.bulletins.pending', compact('bulletins', 'totalPending', 'totalNonSigned'));
    }

    /**
     * Affiche les résultats des étudiants d'une classe spécifique
     *
     * @param ESBTPClasse $classe
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function resultatClasse(ESBTPClasse $classe, Request $request)
    {
        // Récupérer l'année universitaire
        // Fix: Safely handling potential null value from first() before accessing ->id
        $currentAnnee = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        $annee_id = $request->input('annee_universitaire_id', $currentAnnee ? $currentAnnee->id : null);

        // Récupérer la période
        $periode = $request->input('periode', 'semestre1');

        // Avant de récupérer les notes, vérifiez les années universitaires manquantes
        $this->verifierAnneesUniversitaires();

        // Log debug info
        \Log::info('Résultats classe', [
            'classe_id' => $classe->id,
            'classe_name' => $classe->name,
            'annee_id' => $annee_id,
            'periode' => $periode
        ]);

        // Récupérer les étudiants inscrits à cette classe pour cette année
        $studentsQuery = ESBTPEtudiant::query();

        // Try to find students via inscriptions first
        $studentsQuery->whereHas('inscriptions', function($query) use ($classe, $annee_id) {
            $query->where('classe_id', $classe->id)
                ->where('annee_universitaire_id', $annee_id)
                ->where('statut', 'active');
        });

        // If no students found via inscriptions, try direct class relationship
        if ($studentsQuery->count() === 0) {
            \Log::info('Aucun étudiant trouvé via inscriptions pour la classe '.$classe->id.', essai via relation directe');
            $studentsQuery = ESBTPEtudiant::where('classe_id', $classe->id);
        }

        $etudiants = $studentsQuery->get();

        \Log::info('Nombre d\'étudiants trouvés: ' . $etudiants->count());

        // Récupérer les matières enseignées dans cette classe
        $matieres = ESBTPMatiere::whereHas('classes', function($query) use ($classe) {
            $query->where('classe_id', $classe->id);
        })->get();

        // Récupérer les résultats pour chaque étudiant
        $resultats = [];
        foreach ($etudiants as $etudiant) {
            // Récupérer les notes de l'étudiant pour cette période
            $notesQuery = ESBTPNote::where('etudiant_id', $etudiant->id)
                ->where(function($query) use ($classe) {
                    $query->where('classe_id', $classe->id)
                          ->orWhereHas('evaluation', function($q) use ($classe) {
                              $q->where('classe_id', $classe->id);
                          });
                })
                ->whereHas('evaluation', function($query) use ($annee_id) {
                    $query->where('annee_universitaire_id', $annee_id);
                });

            // FIX: Properly filter by period to get all notes
            if ($periode !== 'annuel') {
                $notesQuery->where(function($query) use ($periode) {
                    $query->where('semestre', $periode)
                        ->orWhereHas('evaluation', function($q) use ($periode) {
                            $q->where('periode', $periode);
                        });
                });
            }

            $notes = $notesQuery->with(['evaluation', 'evaluation.matiere'])->get();

            \Log::info('Notes pour étudiant ' . $etudiant->id . ': ' . $notes->count());

            if ($notes->isNotEmpty()) {
                // Vérifier que toutes les notes ont une évaluation valide
                $validNotes = $notes->filter(function($note) {
                    return $note->evaluation && $note->evaluation->matiere && $note->evaluation->coefficient > 0;
                });

                if ($validNotes->isEmpty()) {
                    \Log::info('Aucune note valide pour étudiant ' . $etudiant->id);
                    continue;
                }

                // Nouveau calcul plus robuste
                $totalPondere = $validNotes->sum(function($note) {
                    return $note->note_vingt * $note->evaluation->coefficient;
                });

                $totalCoefficients = $validNotes->sum('evaluation.coefficient');

                $moyenne = $totalCoefficients > 0 ? $totalPondere / $totalCoefficients : 0;
            } else {
                // Si aucune note, définir moyenne à null pour distinguer des 0 réels
                $moyenne = null;
            }

            // Ajouter les résultats de l'étudiant
            $resultats[] = [
                'etudiant' => $etudiant,
                'moyenne' => $moyenne,
                'notes_count' => $notes->count()
            ];
        }

        // Trier les résultats par moyenne décroissante
        usort($resultats, function($a, $b) {
            return ($b['moyenne'] ?? 0) <=> ($a['moyenne'] ?? 0); // Handle null values
        });

        // Périodes disponibles
        $periodes = [
            (object)['id' => 'semestre1', 'nom' => 'Premier Semestre'],
            (object)['id' => 'semestre2', 'nom' => 'Deuxième Semestre'],
            (object)['id' => 'annuel', 'nom' => 'Annuel']
        ];

        // Années universitaires
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();

        return view('esbtp.resultats.classe', compact(
            'classe',
            'resultats',
            'periodes',
            'periode',
            'anneesUniversitaires',
            'annee_id',
            'matieres'
        ));
    }

    /**
     * Affiche les résultats détaillés d'un étudiant spécifique
     *
     * @param ESBTPEtudiant $etudiant
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function resultatEtudiant(ESBTPEtudiant $etudiant, Request $request)
    {
        // Récupérer les paramètres
        $classe_id = $request->input('classe_id');

        // Fix: Safely handling potential null value from first() before accessing ->id
        $currentAnnee = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        $annee_id = $request->input('annee_universitaire_id', $currentAnnee ? $currentAnnee->id : null);

        $periode = $request->input('periode', 'semestre1');

        // Vérifier les années universitaires manquantes avant de récupérer les notes
        $this->verifierAnneesUniversitaires();

        \Log::info('Résultat étudiant', [
            'etudiant_id' => $etudiant->id,
            'etudiant_nom' => $etudiant->nom_complet,
            'classe_id' => $classe_id,
            'annee_id' => $annee_id,
            'periode' => $periode
        ]);

        // Vérifier si une classe est spécifiée, sinon prendre la classe actuelle de l'étudiant
        if (!$classe_id) {
            $inscription = $etudiant->inscriptions()
                ->where('annee_universitaire_id', $annee_id)
                ->where('statut', 'active')
                ->first();

            if ($inscription) {
                $classe_id = $inscription->classe_id;
                \Log::info('Classe trouvée via inscription: ' . $classe_id);
            } else {
                // If no inscription found, try to get the student's direct class
                if ($etudiant->classe_id) {
                    $classe_id = $etudiant->classe_id;
                    \Log::info('Classe trouvée via relation directe: ' . $classe_id);
                } else {
                    // If no class found at all, return with error
                    return redirect()->route('esbtp.resultats.index')
                        ->with('error', 'Aucune classe trouvée pour cet étudiant pour l\'année universitaire sélectionnée.');
                }
            }
        }

        // Récupérer la classe
        try {
            $classe = ESBTPClasse::findOrFail($classe_id);
        } catch (\Exception $e) {
            return redirect()->route('esbtp.resultats.index')
                ->with('error', 'La classe spécifiée n\'existe pas.');
        }

        // Récupérer les notes de l'étudiant pour cette classe, année et période
        $notesQuery = ESBTPNote::where('etudiant_id', $etudiant->id)
            ->where(function($query) use ($classe_id) {
                $query->where('classe_id', $classe_id)
                    ->orWhereHas('evaluation', function($q) use ($classe_id) {
                        $q->where('classe_id', $classe_id);
                    });
            })
            ->whereHas('evaluation', function($query) use ($annee_id) {
                $query->where('annee_universitaire_id', $annee_id);
            });

        // Filter by period if not 'annuel'
        if ($periode !== 'annuel') {
            $notesQuery->where(function($query) use ($periode) {
                $query->where('semestre', $periode)
                    ->orWhereHas('evaluation', function($q) use ($periode) {
                        $q->where('periode', $periode);
                    });
            });
        }

        $notes = $notesQuery->with(['evaluation.matiere'])->get();

        \Log::info('Nombre de notes trouvées: ' . $notes->count());

        // Regrouper les notes par matière
        $notesByMatiere = [];
        foreach ($notes as $note) {
            if (!$note->evaluation || !$note->evaluation->matiere) {
                \Log::warning('Note sans évaluation ou matière valide', ['note_id' => $note->id]);
                continue; // Skip notes without evaluations or matières
            }

            $matiere_id = $note->evaluation->matiere_id;
            if (!isset($notesByMatiere[$matiere_id])) {
                $notesByMatiere[$matiere_id] = [
                    'matiere' => $note->evaluation->matiere,
                    'notes' => [],
                    'total_coefficients' => 0,
                    'total_pondere' => 0
                ];
            }

            $notesByMatiere[$matiere_id]['notes'][] = $note;
            $notesByMatiere[$matiere_id]['total_coefficients'] += $note->evaluation->coefficient;
            $notesByMatiere[$matiere_id]['total_pondere'] += $note->note_vingt * $note->evaluation->coefficient;
        }

        // Calculer la moyenne par matière
        foreach ($notesByMatiere as &$matiereData) {
            if ($matiereData['total_coefficients'] > 0) {
                $matiereData['moyenne'] = $matiereData['total_pondere'] / $matiereData['total_coefficients'];
            } else {
                $matiereData['moyenne'] = 0;
            }
        }

        // Calculer la moyenne générale
        $totalCoefficients = array_sum(array_column($notesByMatiere, 'total_coefficients'));
        $totalPondere = array_sum(array_column($notesByMatiere, 'total_pondere'));
        $moyenneGenerale = $totalCoefficients > 0 ? $totalPondere / $totalCoefficients : 0;

        \Log::info('Moyenne générale calculée: ' . $moyenneGenerale);

        // Périodes disponibles
        $periodes = [
            (object)['id' => 'semestre1', 'nom' => 'Premier Semestre'],
            (object)['id' => 'semestre2', 'nom' => 'Deuxième Semestre'],
            (object)['id' => 'annuel', 'nom' => 'Annuel']
        ];

        // Années universitaires
        $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();

        // Classes où l'étudiant est inscrit
        $classes = ESBTPClasse::whereHas('inscriptions', function($query) use ($etudiant, $annee_id) {
            $query->where('etudiant_id', $etudiant->id)
                ->where('annee_universitaire_id', $annee_id);
        })->get();

        // If no classes found via inscriptions, try direct class relation
        if ($classes->isEmpty() && $etudiant->classe_id) {
            $classes = ESBTPClasse::where('id', $etudiant->classe_id)->get();
        }

        return view('esbtp.resultats.etudiant', compact(
            'etudiant',
            'classe',
            'notes',
            'notesByMatiere',
            'moyenneGenerale',
            'periodes',
            'periode',
            'anneesUniversitaires',
            'annee_id',
            'classes',
            'classe_id'
        ));
    }

    // Ajouter cette méthode pour vérifier et corriger les années universitaires manquantes dans les évaluations
    public function verifierAnneesUniversitaires()
    {
        try {
            $evaluationsSansAnnee = ESBTPEvaluation::whereNull('annee_universitaire_id')->get();
            $count = $evaluationsSansAnnee->count();

            if ($count === 0) {
                \Log::info('Toutes les évaluations ont une année universitaire assignée.');
                return [
                    'success' => true,
                    'message' => 'Toutes les évaluations ont une année universitaire assignée.'
                ];
            }

            \Log::info("Trouvé {$count} évaluations sans année universitaire. Correction en cours...");

            // Récupérer l'année universitaire actuelle
            $anneeActuelle = ESBTPAnneeUniversitaire::where('is_current', true)->first();

            if (!$anneeActuelle) {
                \Log::error("Impossible de corriger les évaluations sans année universitaire. Aucune année actuelle n'est définie.");
                return [
                    'success' => false,
                    'message' => 'Impossible de corriger les évaluations sans année universitaire. Aucune année actuelle n\'est définie.'
                ];
            }

            $updatedFromClass = 0;
            $updatedFromDefault = 0;

            // Mettre à jour les évaluations sans année universitaire
            foreach ($evaluationsSansAnnee as $eval) {
                // D'abord essayer d'obtenir l'année universitaire à partir de la classe
                if ($eval->classe_id) {
                    $classe = ESBTPClasse::find($eval->classe_id);
                    if ($classe && $classe->annee_universitaire_id) {
                        $eval->annee_universitaire_id = $classe->annee_universitaire_id;
                        $updatedFromClass++;
                        \Log::info("Évaluation ID {$eval->id} mise à jour avec l'année universitaire de sa classe: {$classe->annee_universitaire_id}");
                    } else {
                        $eval->annee_universitaire_id = $anneeActuelle->id;
                        $updatedFromDefault++;
                        \Log::info("Évaluation ID {$eval->id} mise à jour avec l'année universitaire par défaut: {$anneeActuelle->id}");
                    }
                } else {
                    $eval->annee_universitaire_id = $anneeActuelle->id;
                    $updatedFromDefault++;
                    \Log::info("Évaluation ID {$eval->id} sans classe mise à jour avec l'année universitaire par défaut: {$anneeActuelle->id}");
                }
                $eval->save();
            }

            \Log::info("Mise à jour effectuée: {$updatedFromClass} évaluations mises à jour via classe, {$updatedFromDefault} via année par défaut");
            return [
                'success' => true,
                'message' => "Mise à jour effectuée: {$count} évaluations ont été assignées à l'année universitaire correspondante."
            ];
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la vérification des années universitaires: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ];
        }
    }
}

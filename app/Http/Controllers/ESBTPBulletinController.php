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
                    ->get() : collect();

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
        $resultats = $bulletin->resultats;

        if ($resultats->isEmpty()) {
            $bulletin->moyenne_generale = null;
            $bulletin->save();
            return;
        }

        $sommePoints = 0;
        $sommeCoefficients = 0;

        foreach ($resultats as $resultat) {
            if ($resultat->moyenne !== null) {
                $sommePoints += $resultat->moyenne * $resultat->coefficient;
                $sommeCoefficients += $resultat->coefficient;
            }
        }

        $moyenneGenerale = $sommeCoefficients > 0 ? $sommePoints / $sommeCoefficients : null;

        $bulletin->moyenne_generale = $moyenneGenerale;
        $bulletin->save();

        // Calculer le rang si la moyenne a changé
        $this->calculerRang($bulletin);
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
                $etudiants = ESBTPEtudiant::whereHas('inscriptions', function($query) use ($request) {
                    $query->where('classe_id', $request->classe_id)
                        ->where('annee_universitaire_id', $request->annee_universitaire_id);
                })->get();
                \Log::info('Nombre d\'étudiants trouvés: ' . $etudiants->count());
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la récupération des étudiants: ' . $e->getMessage());
                \Log::error('SQL: ' . $e->getTraceAsString());
                throw $e;
            }

            $bulletinsGeneres = 0;

            foreach ($etudiants as $etudiant) {
                \Log::info('Traitement de l\'étudiant: ' . $etudiant->id . ' - ' . $etudiant->nom . ' ' . $etudiant->prenom);
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
                        \Log::info('Traitement de la matière: ' . $matiere->id . ' - ' . $matiere->nom);
                        // Récupérer toutes les évaluations de cette matière pour cette classe
                        try {
                            $evaluations = $matiere ? $matiere->evaluations()
                                ->where('classe_id', $classe->id)
                                ->get() : collect();

                            \Log::info('Nombre d\'évaluations trouvées: ' . $evaluations->count());

                            if (!$evaluations || $evaluations->isEmpty()) {
                                \Log::info('Pas d\'évaluations pour la matière: ' . $matiere->id);
                                continue; // Passer à la matière suivante s'il n'y a pas d'évaluations
                            }
                        } catch (\Exception $e) {
                            \Log::error('Erreur lors de la récupération des évaluations: ' . $e->getMessage());
                            \Log::error('SQL: ' . $e->getTraceAsString());
                            throw $e;
                        }

                        // Récupérer les notes de l'étudiant pour ces évaluations
                        try {
                            $notes = ESBTPNote::whereIn('evaluation_id', $evaluations->pluck('id'))
                                ->where('etudiant_id', $etudiant->id)
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
                            $evaluation = $evaluations->where('id', $note->evaluation_id)->first();
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
        // Récupérer les paramètres de filtre
        $classe_id = $request->input('classe_id');
        $annee_id = $request->input('annee_universitaire_id',
            ESBTPAnneeUniversitaire::where('is_current', true)->first()->id ?? null);
        $periode = $request->input('periode', 'semestre1');

        // Vérifier si les paramètres sont valides
        if (!$classe_id || !$annee_id) {
            $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
            $anneesUniversitaires = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();
            return view('esbtp.resultats.index', compact('classes', 'anneesUniversitaires', 'classe_id', 'annee_id', 'periode'));
        }

        // Récupérer la classe sélectionnée avec ses étudiants inscrits
        $classe = ESBTPClasse::with([
            'inscriptions' => function($query) use ($annee_id) {
                $query->where('annee_universitaire_id', $annee_id)
                      ->where('status', 'active');
            },
            'inscriptions.etudiant',
            'matieres'
        ])->findOrFail($classe_id);

        // Récupérer l'année universitaire
        $anneeUniversitaire = ESBTPAnneeUniversitaire::findOrFail($annee_id);

        // Récupérer les bulletins des étudiants pour cette classe, cette année et cette période
        $bulletins = ESBTPBulletin::with(['etudiant', 'resultats.matiere'])
            ->where('classe_id', $classe_id)
            ->where('annee_universitaire_id', $annee_id)
            ->where('periode', $periode)
            ->get();

        // Préparer les données pour l'affichage
        $resultatsEtudiants = [];
        foreach ($bulletins as $bulletin) {
            $resultatsEtudiants[$bulletin->etudiant_id] = [
                'etudiant' => $bulletin->etudiant,
                'bulletin' => $bulletin,
                'resultats' => $bulletin->resultats->keyBy('matiere_id'),
                'moyenne' => $bulletin->moyenne_generale,
                'rang' => $bulletin->rang
            ];
        }

        // Récupérer les étudiants qui n'ont pas encore de bulletin
        $etudiantsSansBulletin = [];
        foreach ($classe->inscriptions as $inscription) {
            if (!isset($resultatsEtudiants[$inscription->etudiant_id])) {
                $etudiantsSansBulletin[] = $inscription->etudiant;
            }
        }

        return view('esbtp.resultats.index', compact(
            'classe',
            'anneeUniversitaire',
            'periode',
            'resultatsEtudiants',
            'etudiantsSansBulletin',
            'bulletins'
        ));
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
}

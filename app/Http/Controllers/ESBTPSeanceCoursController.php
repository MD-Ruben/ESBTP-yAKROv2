<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPEmploiTemps;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ESBTPSeanceCoursController extends Controller
{
    /**
     * Affiche la liste des séances de cours.
     */
    public function index(Request $request)
    {
        try {
            // Récupérer les filtres de la requête
            $emploiTempsId = $request->input('emploi_temps_id');
            $jourSemaine = $request->input('jour_semaine');
            $typeSeance = $request->input('type_seance');
            $enseignantNom = $request->input('enseignant');

            // Construire la requête de base
            $query = ESBTPSeanceCours::with(['emploiTemps.classe', 'matiere']);

            // Appliquer les filtres si présents
            if ($emploiTempsId) {
                $query->where('emploi_temps_id', $emploiTempsId);
            }

            if ($jourSemaine) {
                $query->where('jour', $jourSemaine);
            }

            if ($typeSeance) {
                $query->where('type_seance', $typeSeance);
            }

            if ($enseignantNom) {
                $query->where('enseignant', $enseignantNom);
            }

            // Récupérer les séances de cours paginées
            $seancesCours = $query->orderBy('jour')->orderBy('heure_debut')->paginate(25);

            // Récupérer tous les emplois du temps pour le filtre
            $emploisTemps = ESBTPEmploiTemps::with('classe')->orderBy('created_at', 'desc')->get();

            // Récupérer tous les enseignants pour le filtre
            $enseignants = User::role('enseignant')->where('is_active', true)->orderBy('name')->get();

            // Calculer les statistiques par type de séance
            $statsCours = [
                'cours' => ESBTPSeanceCours::where('type_seance', 'cours')->count(),
                'td' => ESBTPSeanceCours::where('type_seance', 'td')->count(),
                'tp' => ESBTPSeanceCours::where('type_seance', 'tp')->count(),
                'examen' => ESBTPSeanceCours::where('type_seance', 'examen')->count(),
                'autre' => ESBTPSeanceCours::whereNotIn('type_seance', ['cours', 'td', 'tp', 'examen'])->count(),
            ];

            // Calculer les statistiques par jour
            $statsJours = ESBTPSeanceCours::select('jour', DB::raw('count(*) as total'))
                ->groupBy('jour')
                ->pluck('total', 'jour')
                ->toArray();

            // Détecter les conflits potentiels
            $conflits = $this->detecterConflitsHoraire();

            return view('esbtp.seances-cours.index', compact(
                'seancesCours',
                'emploisTemps',
                'enseignants',
                'statsCours',
                'statsJours',
                'conflits'
            ));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage des séances de cours: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Une erreur est survenue lors du chargement des séances de cours: ' . $e->getMessage());
        }
    }

    /**
     * Détecte les conflits d'horaire entre les séances de cours.
     *
     * @return array Liste des conflits détectés
     */
    private function detecterConflitsHoraire()
    {
        $conflits = [];

        // Récupérer toutes les séances actives
        $seances = ESBTPSeanceCours::with(['emploiTemps.classe', 'matiere'])
            ->where('is_active', true)
            ->get();

        // Vérifier les conflits pour chaque séance
        foreach ($seances as $seance) {
            // Vérifier les conflits avec les autres séances
            foreach ($seances as $autreSeance) {
                // Ne pas comparer une séance avec elle-même
                if ($seance->id == $autreSeance->id) {
                    continue;
                }

                // Vérifier si les séances sont le même jour et se chevauchent
                if ($seance->jour == $autreSeance->jour &&
                    $seance->heure_debut < $autreSeance->heure_fin &&
                    $seance->heure_fin > $autreSeance->heure_debut) {

                    // Vérifier les conflits d'enseignant
                    if ($seance->enseignant == $autreSeance->enseignant) {
                        $conflits[] = [
                            'type' => 'Enseignant',
                            'nom' => $seance->enseignant,
                            'jour' => $seance->jour,
                            'heure_debut' => $seance->heure_debut,
                            'heure_fin' => $seance->heure_fin,
                            'seance_id' => $seance->id
                        ];
                    }

                    // Vérifier les conflits de salle
                    if ($seance->salle == $autreSeance->salle) {
                        $conflits[] = [
                            'type' => 'Salle',
                            'nom' => $seance->salle,
                            'jour' => $seance->jour,
                            'heure_debut' => $seance->heure_debut,
                            'heure_fin' => $seance->heure_fin,
                            'seance_id' => $seance->id
                        ];
                    }

                    // Vérifier les conflits de classe
                    if ($seance->emploiTemps && $autreSeance->emploiTemps &&
                        $seance->emploiTemps->classe_id == $autreSeance->emploiTemps->classe_id) {
                        $conflits[] = [
                            'type' => 'Classe',
                            'nom' => $seance->emploiTemps->classe->name,
                            'jour' => $seance->jour,
                            'heure_debut' => $seance->heure_debut,
                            'heure_fin' => $seance->heure_fin,
                            'seance_id' => $seance->id
                        ];
                    }
                }
            }
        }

        // Éliminer les doublons
        $conflitsUniques = [];
        foreach ($conflits as $conflit) {
            $key = $conflit['type'] . '-' . $conflit['nom'] . '-' . $conflit['jour'] . '-' . $conflit['heure_debut'] . '-' . $conflit['heure_fin'];
            $conflitsUniques[$key] = $conflit;
        }

        return array_values($conflitsUniques);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle séance de cours.
     */
    public function create(Request $request)
    {
        // Récupérer tous les emplois du temps pour le dropdown
        $emploisTemps = ESBTPEmploiTemps::with('classe.filiere', 'classe.niveau')->orderBy('created_at', 'desc')->get();

        // Initialiser les variables
        $emploiTemps = null;
        $classe = null;

        // Vérifier si un emploi du temps est spécifié
        if ($request->has('emploi_temps_id')) {
            try {
                $emploiTemps = ESBTPEmploiTemps::findOrFail($request->emploi_temps_id);
                $classe = ESBTPClasse::with('filiere', 'niveau')->findOrFail($emploiTemps->classe_id);
            } catch (\Exception $e) {
                // Si l'emploi du temps n'existe pas, on continue sans erreur
                // mais on garde la trace de l'erreur pour le débogage
                \Log::warning("Emploi du temps non trouvé: " . $request->emploi_temps_id);
                // On ne redirige pas, on laisse l'utilisateur choisir un emploi du temps valide
            }
        }

        // Récupérer les matières
        $matieres = ESBTPMatiere::where('is_active', true)->orderBy('name')->get();

        // Récupérer les enseignants disponibles
        $enseignants = User::role('enseignant')->where('is_active', true)->orderBy('name')->get();

        $joursSemaine = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche'
        ];

        // Récupérer les paramètres d'URL
        $jour = $request->get('jour');
        $heure_debut = $request->get('heure_debut');

        // Passer le request à la vue pour pouvoir accéder aux paramètres
        $request_params = $request;

        return view('esbtp.seances-cours.create', compact(
            'emploiTemps',
            'emploisTemps',
            'classe',
            'matieres',
            'enseignants',
            'joursSemaine',
            'jour',
            'heure_debut',
            'request_params'
        ));
    }

    /**
     * Enregistre une nouvelle séance de cours.
     */
    public function store(Request $request)
    {
        try {
            // Ajout de logs pour déboguer
            Log::info('Début de la méthode store pour séance de cours');
            Log::info('Données reçues:', $request->all());

            // Récupérer l'emploi du temps pour obtenir classe_id et annee_universitaire_id
            $emploiTempsId = $request->input('emploi_temps_id');

            if (!$emploiTempsId) {
                Log::error('Erreur: emploi_temps_id manquant');
                return back()->withInput()->with('error', 'L\'identifiant de l\'emploi du temps est requis.');
            }

            $emploiTemps = ESBTPEmploiTemps::find($emploiTempsId);

            if (!$emploiTemps) {
                Log::error('Erreur: emploi du temps non trouvé avec ID: ' . $emploiTempsId);
                return back()->withInput()->with('error', 'L\'emploi du temps spécifié n\'existe pas.');
            }

            Log::info('Emploi du temps trouvé:', [
                'id' => $emploiTemps->id,
                'classe_id' => $emploiTemps->classe_id,
                'annee_universitaire_id' => $emploiTemps->annee_universitaire_id
            ]);

            // Validation des données avec les champs corrects
            $validated = $request->validate([
                'matiere_id' => 'required|exists:esbtp_matieres,id',
                'enseignant' => 'nullable|string', // Correction: enseignant au lieu de enseignant_id
                'jour' => 'required|integer|min:1|max:7',
                'heure_debut' => 'required|string',
                'heure_fin' => 'required|string|after:heure_debut',
                'salle' => 'required|string',
                'type_seance' => 'required|string|in:cours,td,tp,examen,pause,dejeuner,autre',
                'emploi_temps_id' => 'required|exists:esbtp_emploi_temps,id',
                'details' => 'nullable|string', // Ajout du champ details qui est dans le formulaire
            ]);

            Log::info('Validation réussie');

            DB::beginTransaction();

            $seanceCours = new ESBTPSeanceCours();
            $seanceCours->classe_id = $emploiTemps->classe_id; // Récupéré de l'emploi du temps
            $seanceCours->matiere_id = $validated['matiere_id'];
            $seanceCours->enseignant = $validated['enseignant']; // Correction: enseignant au lieu de enseignant_id
            $seanceCours->jour = $validated['jour'];
            $seanceCours->heure_debut = $validated['heure_debut'];
            $seanceCours->heure_fin = $validated['heure_fin'];
            $seanceCours->salle = $validated['salle'];
            $seanceCours->description = $validated['details'] ?? null; // Utilisation du champ details
            $seanceCours->annee_universitaire_id = $emploiTemps->annee_universitaire_id; // Récupéré de l'emploi du temps
            $seanceCours->type_seance = $validated['type_seance'];
            $seanceCours->emploi_temps_id = $validated['emploi_temps_id'];
            $seanceCours->is_active = $request->has('is_active'); // Gestion du checkbox is_active

            Log::info('Objet séance de cours créé:', $seanceCours->toArray());

            $seanceCours->save();

            Log::info('Séance de cours enregistrée avec succès, ID: ' . $seanceCours->id);

            DB::commit();

            return redirect()->route('esbtp.emploi-temps.show', $validated['emploi_temps_id'])
                ->with('success', 'Séance de cours ajoutée avec succès à l\'emploi du temps.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'ajout d\'une séance de cours: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la séance de cours: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de modification d'une séance de cours.
     */
    public function edit(ESBTPSeanceCours $seancesCour)
    {
        // Ajouter des logs détaillés pour le débogage
        \Log::info('Tentative de modification d\'une séance de cours', [
            'seance_id' => $seancesCour->id,
            'emploi_temps_id' => $seancesCour->emploi_temps_id,
            'classe_id' => $seancesCour->classe_id,
            'annee_universitaire_id' => $seancesCour->annee_universitaire_id
        ]);

        // Récupérer l'emploi du temps directement depuis la base de données
        // en utilisant DB::table pour éviter tout problème avec le modèle
        $emploiTempsDB = \DB::table('esbtp_emploi_temps')
            ->where('id', $seancesCour->emploi_temps_id)
            ->first();

        \Log::info('Résultat de la requête DB directe pour l\'emploi du temps', [
            'emploi_temps_trouve' => $emploiTempsDB ? 'Oui' : 'Non',
            'emploi_temps_data' => $emploiTempsDB
        ]);

        // Récupérer l'emploi du temps avec le modèle Eloquent
        $emploiTemps = \App\Models\ESBTPEmploiTemps::find($seancesCour->emploi_temps_id);

        \Log::info('Résultat de la requête Eloquent pour l\'emploi du temps', [
            'emploi_temps_trouve' => $emploiTemps ? 'Oui' : 'Non',
            'emploi_temps_id' => $emploiTemps ? $emploiTemps->id : null,
            'emploi_temps_deleted_at' => $emploiTemps ? $emploiTemps->deleted_at : null
        ]);

        // Si l'emploi du temps existe dans la base de données mais pas via Eloquent,
        // cela pourrait être dû à un soft delete
        if ($emploiTempsDB && !$emploiTemps) {
            \Log::warning('L\'emploi du temps existe dans la base de données mais pas via Eloquent, possible soft delete', [
                'emploi_temps_id' => $seancesCour->emploi_temps_id,
                'deleted_at' => $emploiTempsDB->deleted_at ?? 'Non disponible'
            ]);

            // Essayer de récupérer même les emplois du temps supprimés
            $emploiTemps = \App\Models\ESBTPEmploiTemps::withTrashed()->find($seancesCour->emploi_temps_id);

            \Log::info('Résultat de la requête Eloquent avec withTrashed()', [
                'emploi_temps_trouve' => $emploiTemps ? 'Oui' : 'Non',
                'emploi_temps_id' => $emploiTemps ? $emploiTemps->id : null,
                'emploi_temps_deleted_at' => $emploiTemps ? $emploiTemps->deleted_at : null
            ]);
        }

        // Vérifier si l'emploi du temps existe
        if (!$emploiTemps) {
            // Log l'erreur pour le débogage
            \Log::error('Emploi du temps non trouvé pour la séance de cours ID: ' . $seancesCour->id . ', emploi_temps_id: ' . $seancesCour->emploi_temps_id);

            // Essayer de trouver un emploi du temps correspondant à la classe
            if ($seancesCour->classe_id && $seancesCour->annee_universitaire_id) {
                \Log::info('Tentative de recherche d\'un emploi du temps pour classe_id: ' . $seancesCour->classe_id . ', annee_universitaire_id: ' . $seancesCour->annee_universitaire_id);

                $emploiTemps = \App\Models\ESBTPEmploiTemps::where('classe_id', $seancesCour->classe_id)
                    ->where('annee_universitaire_id', $seancesCour->annee_universitaire_id)
                    ->first();

                if ($emploiTemps) {
                    // Mettre à jour la séance avec l'emploi du temps trouvé
                    $seancesCour->emploi_temps_id = $emploiTemps->id;
                    $seancesCour->save();

                    \Log::info('Emploi du temps trouvé et associé à la séance de cours ID: ' . $seancesCour->id . ', nouvel emploi_temps_id: ' . $emploiTemps->id);
                } else {
                    \Log::error('Aucun emploi du temps trouvé pour classe_id: ' . $seancesCour->classe_id . ', annee_universitaire_id: ' . $seancesCour->annee_universitaire_id);

                    // Essayer de trouver n'importe quel emploi du temps pour cette classe
                    $emploiTemps = \App\Models\ESBTPEmploiTemps::where('classe_id', $seancesCour->classe_id)->first();

                    if ($emploiTemps) {
                        // Mettre à jour la séance avec l'emploi du temps trouvé
                        $seancesCour->emploi_temps_id = $emploiTemps->id;
                        $seancesCour->save();

                        \Log::info('Emploi du temps alternatif trouvé et associé à la séance de cours ID: ' . $seancesCour->id . ', nouvel emploi_temps_id: ' . $emploiTemps->id);
                    } else {
                        // Rediriger avec un message d'erreur
                        return redirect()->route('esbtp.seances-cours.index')
                            ->with('error', 'L\'emploi du temps associé à cette séance de cours n\'existe pas ou a été supprimé.');
                    }
                }
            } else {
                \Log::error('Impossible de rechercher un emploi du temps alternatif car classe_id ou annee_universitaire_id est manquant. classe_id: ' . $seancesCour->classe_id . ', annee_universitaire_id: ' . $seancesCour->annee_universitaire_id);

                // Rediriger avec un message d'erreur
                return redirect()->route('esbtp.seances-cours.index')
                    ->with('error', 'L\'emploi du temps associé à cette séance de cours n\'existe pas ou a été supprimé.');
            }
        }

        // Si nous avons trouvé un emploi du temps, continuer avec le chargement des données pour la vue
        if ($emploiTemps) {
            $classe = \App\Models\ESBTPClasse::with('filiere', 'niveau')->findOrFail($emploiTemps->classe_id);

            // Récupérer les matières associées à cette classe ou formation
            $matieres = \App\Models\ESBTPMatiere::where('is_active', true)->orderBy('name')->get();

            // Récupérer les enseignants disponibles
            $enseignants = \App\Models\User::role('enseignant')->where('is_active', true)->orderBy('name')->get();

            $joursSemaine = [
                1 => 'Lundi',
                2 => 'Mardi',
                3 => 'Mercredi',
                4 => 'Jeudi',
                5 => 'Vendredi',
                6 => 'Samedi',
                7 => 'Dimanche'
            ];

            return view('esbtp.seances-cours.edit', compact(
                'seancesCour',
                'emploiTemps',
                'classe',
                'matieres',
                'enseignants',
                'joursSemaine'
            ));
        } else {
            // Si nous n'avons toujours pas d'emploi du temps, rediriger avec un message d'erreur
            return redirect()->route('esbtp.seances-cours.index')
                ->with('error', 'L\'emploi du temps associé à cette séance de cours n\'existe pas ou a été supprimé.');
        }
    }

    /**
     * Mettre à jour une séance de cours existante.
     */
    public function update(Request $request, ESBTPSeanceCours $seancesCour)
    {
        // Validation
        $validated = $request->validate([
            'matiere_id' => 'required|exists:esbtp_matieres,id',
            'enseignant' => 'required|string|max:255',
            'jour' => 'required|integer|min:1|max:7',
            'heure_debut' => 'required|string',
            'heure_fin' => 'required|string|after:heure_debut',
            'salle' => 'required|string|max:50',
            'is_active' => 'boolean',
            'type_seance' => 'required|string|in:cours,td,tp,examen,pause,dejeuner,autre',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Mise à jour des champs
            $seancesCour->matiere_id = $validated['matiere_id'];
            $seancesCour->enseignant = $validated['enseignant'];
            $seancesCour->jour = $validated['jour'];
            $seancesCour->heure_debut = $validated['heure_debut'];
            $seancesCour->heure_fin = $validated['heure_fin'];
            $seancesCour->salle = $validated['salle'];
            $seancesCour->is_active = $request->has('is_active');
            $seancesCour->type_seance = $validated['type_seance'];
            $seancesCour->description = $validated['description'] ?? null;

            $seancesCour->save();

            DB::commit();

            // Vérifier si l'emploi du temps existe avant de rediriger
            $emploiTempsExists = \App\Models\ESBTPEmploiTemps::where('id', $seancesCour->emploi_temps_id)->exists();

            if ($emploiTempsExists) {
                return redirect()->route('esbtp.emploi-temps.show', $seancesCour->emploi_temps_id)
                    ->with('success', 'Séance de cours mise à jour avec succès.');
            } else {
                // Si l'emploi du temps n'existe pas, rediriger vers la liste des séances
                \Log::warning('Redirection vers la liste des séances car l\'emploi du temps n\'existe pas', [
                    'seance_id' => $seancesCour->id,
                    'emploi_temps_id' => $seancesCour->emploi_temps_id
                ]);

                return redirect()->route('esbtp.seances-cours.index')
                    ->with('success', 'Séance de cours mise à jour avec succès.')
                    ->with('warning', 'L\'emploi du temps associé n\'existe plus ou a été supprimé.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour d\'une séance de cours: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la séance de cours: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une séance de cours.
     */
    public function destroy(ESBTPSeanceCours $seancesCour)
    {
        try {
            $emploi_temps_id = $seancesCour->emploi_temps_id;
            $seancesCour->delete();

            return redirect()->route('esbtp.emploi-temps.show', $emploi_temps_id)
                ->with('success', 'La séance a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la séance: ' . $e->getMessage());
        }
    }

    /**
     * Vérifie s'il y a des conflits d'horaire pour une séance donnée.
     *
     * @param ESBTPSeanceCours $seanceCours
     * @return array Liste des conflits détectés
     */
    private function verifierConflitsHoraire(ESBTPSeanceCours $seanceCours)
    {
        $conflits = [];
        $emploiTemps = ESBTPEmploiTemps::findOrFail($seanceCours->emploi_temps_id);
        $classe = ESBTPClasse::findOrFail($emploiTemps->classe_id);

        // Requête pour trouver les séances qui se chevauchent le même jour
        $query = ESBTPSeanceCours::where('jour', $seanceCours->jour)
            ->where(function($q) use ($seanceCours) {
                // Chevauchement d'horaires
                $q->where(function($q1) use ($seanceCours) {
                    $q1->where('heure_debut', '<', $seanceCours->heure_fin)
                       ->where('heure_fin', '>', $seanceCours->heure_debut);
                });
            });

        // Exclure la séance actuelle pour les mises à jour
        if ($seanceCours->exists) {
            $query->where('id', '!=', $seanceCours->id);
        }

        // Vérifier les conflits avec la même classe
        $conflitsClasse = (clone $query)
            ->whereHas('emploiTemps', function($q) use ($classe) {
                $q->where('classe_id', $classe->id);
            })
            ->get();

        if ($conflitsClasse->count() > 0) {
            $conflits[] = "La classe {$classe->name} a déjà cours à cet horaire";
        }

        // Vérifier les conflits avec le même enseignant
        $conflitsEnseignant = (clone $query)
            ->where('enseignant', $seanceCours->enseignant)
            ->get();

        if ($conflitsEnseignant->count() > 0) {
            $conflits[] = "L'enseignant {$seanceCours->enseignant} a déjà cours à cet horaire";
        }

        // Vérifier les conflits avec la même salle
        $conflitsSalle = (clone $query)
            ->where('salle', $seanceCours->salle)
            ->get();

        if ($conflitsSalle->count() > 0) {
            $conflits[] = "La salle {$seanceCours->salle} est déjà occupée à cet horaire";
        }

        return $conflits;
    }
}

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

class ESBTPSeanceCoursController extends Controller
{
    /**
     * Affiche le formulaire de création d'une nouvelle séance de cours.
     */
    public function create(Request $request)
    {
        // Vérifier que nous avons un emploi du temps
        if (!$request->has('emploi_temps_id')) {
            return redirect()->route('esbtp.emploi-temps.index')
                ->with('error', 'Veuillez sélectionner un emploi du temps pour ajouter une séance.');
        }

        $emploiTemps = ESBTPEmploiTemps::findOrFail($request->emploi_temps_id);
        $classe = ESBTPClasse::with('filiere', 'niveau')->findOrFail($emploiTemps->classe_id);

        // Récupérer les matières associées à cette classe ou formation
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
        $heure = $request->get('heure');

        return view('esbtp.seances-cours.create', compact(
            'emploiTemps',
            'classe',
            'matieres',
            'enseignants',
            'joursSemaine',
            'jour',
            'heure'
        ));
    }

    /**
     * Enregistre une nouvelle séance de cours.
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'emploi_temps_id' => 'required|exists:esbtp_emploi_temps,id',
            'matiere_id' => 'required|exists:esbtp_matieres,id',
            'enseignant_id' => 'nullable|exists:users,id',
            'jour_semaine' => 'required|integer|min:1|max:7',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'salle' => 'required|string|max:50',
            'is_active' => 'boolean',
            'type_seance' => 'required|in:cours,td,tp,examen,autre',
            'commentaire' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Créer la séance
            $seanceCours = new ESBTPSeanceCours();
            $seanceCours->emploi_temps_id = $validated['emploi_temps_id'];
            $seanceCours->matiere_id = $validated['matiere_id'];
            $seanceCours->enseignant_id = $validated['enseignant_id'];
            $seanceCours->jour_semaine = $validated['jour_semaine'];
            $seanceCours->heure_debut = $validated['heure_debut'];
            $seanceCours->heure_fin = $validated['heure_fin'];
            $seanceCours->salle = $validated['salle'];
            $seanceCours->type_seance = $validated['type_seance'];
            $seanceCours->is_active = $request->has('is_active');
            $seanceCours->commentaire = $validated['commentaire'] ?? null;
            $seanceCours->created_by = auth()->id();

            // Vérifier les conflits d'horaire
            $conflits = $this->verifierConflitsHoraire($seanceCours);

            if (count($conflits) > 0) {
                DB::rollBack();
                return redirect()->back()->withInput()
                    ->with('error', 'Conflit d\'horaire détecté: ' . implode(', ', $conflits));
            }

            $seanceCours->save();

            DB::commit();

            return redirect()->route('esbtp.emploi-temps.show', $validated['emploi_temps_id'])
                ->with('success', 'La séance a été ajoutée avec succès à l\'emploi du temps.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'ajout de la séance: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de modification d'une séance de cours.
     */
    public function edit(ESBTPSeanceCours $seancesCour)
    {
        $emploiTemps = $seancesCour->emploiTemps;
        $classe = ESBTPClasse::with('filiere', 'niveau')->findOrFail($emploiTemps->classe_id);

        // Récupérer les matières associées à cette classe ou formation
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

        return view('esbtp.seances-cours.edit', compact(
            'seancesCour',
            'emploiTemps',
            'classe',
            'matieres',
            'enseignants',
            'joursSemaine'
        ));
    }

    /**
     * Mettre à jour une séance de cours existante.
     */
    public function update(Request $request, ESBTPSeanceCours $seancesCour)
    {
        // Validation
        $validated = $request->validate([
            'matiere_id' => 'required|exists:esbtp_matieres,id',
            'enseignant_id' => 'required|exists:users,id',
            'jour_semaine' => 'required|integer|min:1|max:7',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'salle' => 'required|string|max:50',
            'is_active' => 'boolean',
            'type_seance' => 'required|in:cours,td,tp,examen,autre',
            'commentaire' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Mettre à jour la séance
            $seancesCour->matiere_id = $validated['matiere_id'];
            $seancesCour->enseignant_id = $validated['enseignant_id'];
            $seancesCour->jour_semaine = $validated['jour_semaine'];
            $seancesCour->heure_debut = $validated['heure_debut'];
            $seancesCour->heure_fin = $validated['heure_fin'];
            $seancesCour->salle = $validated['salle'];
            $seancesCour->type_seance = $validated['type_seance'];
            $seancesCour->is_active = $request->has('is_active');
            $seancesCour->commentaire = $validated['commentaire'] ?? null;
            $seancesCour->updated_by = auth()->id();

            // Vérifier les conflits d'horaire (en excluant la séance courante)
            $conflits = $this->verifierConflitsHoraire($seancesCour);

            if (count($conflits) > 0) {
                DB::rollBack();
                return redirect()->back()->withInput()
                    ->with('error', 'Conflit d\'horaire détecté: ' . implode(', ', $conflits));
            }

            $seancesCour->save();

            DB::commit();

            return redirect()->route('esbtp.emploi-temps.show', $seancesCour->emploi_temps_id)
                ->with('success', 'La séance a été mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la séance: ' . $e->getMessage());
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
        $query = ESBTPSeanceCours::where('jour_semaine', $seanceCours->jour_semaine)
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
            ->where('enseignant_id', $seanceCours->enseignant_id)
            ->get();

        if ($conflitsEnseignant->count() > 0) {
            $enseignant = User::find($seanceCours->enseignant_id);
            $conflits[] = "L'enseignant {$enseignant->name} a déjà cours à cet horaire";
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

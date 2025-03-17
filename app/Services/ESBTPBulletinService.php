<?php

namespace App\Services;

use App\Models\ESBTPBulletin;
use App\Models\ESBTPResultatMatiere;
use App\Models\ESBTPNote;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPClasse;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPAbsence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ESBTPBulletinService
{
    /**
     * Calculer la moyenne d'une matière pour un étudiant
     */
    public function calculerMoyenneMatiere(ESBTPEtudiant $etudiant, ESBTPMatiere $matiere, $periode, $anneeUniversitaireId)
    {
        $evaluations = ESBTPEvaluation::where('matiere_id', $matiere->id)
            ->where('periode', $periode)
            ->where('annee_universitaire_id', $anneeUniversitaireId)
            ->get();

        if ($evaluations->isEmpty()) {
            return null;
        }

        $sommeNotes = 0;
        $sommeCoefficients = 0;

        foreach ($evaluations as $evaluation) {
            $note = ESBTPNote::where('evaluation_id', $evaluation->id)
                ->where('etudiant_id', $etudiant->id)
                ->first();

            if ($note) {
                $sommeNotes += ($note->valeur / $evaluation->bareme) * 20 * $evaluation->coefficient;
                $sommeCoefficients += $evaluation->coefficient;
            }
        }

        return $sommeCoefficients > 0 ? round($sommeNotes / $sommeCoefficients, 2) : null;
    }

    /**
     * Calculer le rang d'un étudiant dans une matière
     */
    public function calculerRangMatiere(ESBTPMatiere $matiere, ESBTPClasse $classe, $periode, $anneeUniversitaireId)
    {
        $moyennes = collect();

        foreach ($classe->etudiants as $etudiant) {
            $moyenne = $this->calculerMoyenneMatiere($etudiant, $matiere, $periode, $anneeUniversitaireId);
            if ($moyenne !== null) {
                $moyennes->push([
                    'etudiant_id' => $etudiant->id,
                    'moyenne' => $moyenne
                ]);
            }
        }

        $moyennes = $moyennes->sortByDesc('moyenne')->values();
        $rangs = [];

        foreach ($moyennes as $index => $item) {
            $rangs[$item['etudiant_id']] = $index + 1;
        }

        return $rangs;
    }

    /**
     * Calculer la moyenne générale d'un bulletin
     */
    public function calculerMoyenneGenerale(Collection $resultats)
    {
        if ($resultats->isEmpty()) {
            return 0;
        }

        $sommePoints = 0;
        $sommeCoefficients = 0;

        foreach ($resultats as $resultat) {
            if ($resultat->moyenne !== null) {
                $sommePoints += $resultat->moyenne * $resultat->coefficient;
                $sommeCoefficients += $resultat->coefficient;
            }
        }

        return $sommeCoefficients > 0 ? round($sommePoints / $sommeCoefficients, 2) : 0;
    }

    /**
     * Calculer le rang général d'un étudiant dans sa classe
     */
    public function calculerRangGeneral(ESBTPBulletin $bulletin)
    {
        $bulletins = ESBTPBulletin::where('classe_id', $bulletin->classe_id)
            ->where('annee_universitaire_id', $bulletin->annee_universitaire_id)
            ->where('periode', $bulletin->periode)
            ->get();

        $moyennes = collect();

        foreach ($bulletins as $b) {
            $moyennes->push([
                'bulletin_id' => $b->id,
                'moyenne' => $b->moyenne_generale
            ]);
        }

        $moyennes = $moyennes->sortByDesc('moyenne')->values();

        foreach ($moyennes as $index => $item) {
            if ($item['bulletin_id'] === $bulletin->id) {
                return $index + 1;
            }
        }

        return null;
    }

    /**
     * Calculer la mention en fonction de la moyenne
     */
    public function calculerMention($moyenne)
    {
        if ($moyenne >= 16) {
            return 'Très Bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }

    /**
     * Calculer le total des absences pour un étudiant
     */
    public function calculerTotalAbsences(ESBTPEtudiant $etudiant, $periode, $anneeUniversitaireId)
    {
        return [
            'justifiees' => ESBTPAbsence::where('etudiant_id', $etudiant->id)
                ->where('periode', $periode)
                ->where('annee_universitaire_id', $anneeUniversitaireId)
                ->where('est_justifiee', true)
                ->sum('nombre_heures'),
            'non_justifiees' => ESBTPAbsence::where('etudiant_id', $etudiant->id)
                ->where('periode', $periode)
                ->where('annee_universitaire_id', $anneeUniversitaireId)
                ->where('est_justifiee', false)
                ->sum('nombre_heures')
        ];
    }

    /**
     * Générer ou mettre à jour un bulletin
     */
    public function genererBulletin(ESBTPEtudiant $etudiant, ESBTPClasse $classe, $periode, $anneeUniversitaireId)
    {
        DB::beginTransaction();
        try {
            // Créer ou récupérer le bulletin
            $bulletin = ESBTPBulletin::firstOrNew([
                'etudiant_id' => $etudiant->id,
                'classe_id' => $classe->id,
                'annee_universitaire_id' => $anneeUniversitaireId,
                'periode' => $periode
            ]);

            // Récupérer toutes les matières de la classe
            $matieres = $classe->matieres;

            // Pour chaque matière, calculer les résultats
            foreach ($matieres as $matiere) {
                $moyenne = $this->calculerMoyenneMatiere($etudiant, $matiere, $periode, $anneeUniversitaireId);
                $rangs = $this->calculerRangMatiere($matiere, $classe, $periode, $anneeUniversitaireId);

                if ($moyenne !== null) {
                    $resultat = ESBTPResultatMatiere::updateOrCreate(
                        [
                            'bulletin_id' => $bulletin->id,
                            'matiere_id' => $matiere->id
                        ],
                        [
                            'moyenne' => $moyenne,
                            'coefficient' => $matiere->coefficient,
                            'rang' => $rangs[$etudiant->id] ?? null
                        ]
                    );
                }
            }

            // Mettre à jour les statistiques du bulletin
            $resultats = $bulletin->resultats;
            $moyenneGenerale = $this->calculerMoyenneGenerale($resultats);
            $rang = $this->calculerRangGeneral($bulletin);
            $absences = $this->calculerTotalAbsences($etudiant, $periode, $anneeUniversitaireId);

            $bulletin->moyenne_generale = $moyenneGenerale;
            $bulletin->rang = $rang;
            $bulletin->effectif_classe = $classe->etudiants->count();
            $bulletin->mention = $this->calculerMention($moyenneGenerale);
            $bulletin->absences_justifiees = $absences['justifiees'];
            $bulletin->absences_non_justifiees = $absences['non_justifiees'];
            $bulletin->save();

            DB::commit();
            return $bulletin;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

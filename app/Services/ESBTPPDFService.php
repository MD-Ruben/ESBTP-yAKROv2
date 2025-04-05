<?php

namespace App\Services;

use App\Models\ESBTPBulletin;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPResultatMatiere;
use App\Models\ESBTPEmploiTemps;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ESBTPPDFService
{
    protected $numberToWords;

    public function __construct(NumberToWords $numberToWords)
    {
        $this->numberToWords = $numberToWords;
    }

    /**
     * Générer le bulletin en PDF
     */
    public function genererBulletinPDF(ESBTPBulletin $bulletin)
    {
        $etudiant = $bulletin->etudiant;
        $classe = $bulletin->classe;
        $resultats = $bulletin->resultats->sortBy('matiere.name');
        $absences = [
            'justifiees' => $bulletin->absences_justifiees,
            'non_justifiees' => $bulletin->absences_non_justifiees,
            'total' => $bulletin->absences_justifiees + $bulletin->absences_non_justifiees
        ];

        $data = [
            'bulletin' => $bulletin,
            'etudiant' => $etudiant,
            'classe' => $classe,
            'resultats' => $resultats,
            'absences' => $absences,
            'moyenne_en_lettres' => $this->numberToWords->convert($bulletin->moyenne_generale),
            'date_edition' => Carbon::now()->locale('fr')->isoFormat('LL')
        ];

        $pdf = PDF::loadView('pdf.bulletin', $data);
        $pdf->setPaper('A4');

        return $pdf;
    }

    /**
     * Générer le relevé de notes en PDF
     */
    public function genererRelevePDF(ESBTPEtudiant $etudiant, $anneeUniversitaireId)
    {
        $bulletins = $etudiant->bulletins()
            ->where('annee_universitaire_id', $anneeUniversitaireId)
            ->orderBy('periode')
            ->get();

        $data = [
            'etudiant' => $etudiant,
            'bulletins' => $bulletins,
            'date_edition' => Carbon::now()->locale('fr')->isoFormat('LL')
        ];

        $pdf = PDF::loadView('pdf.releve', $data);
        $pdf->setPaper('A4');

        return $pdf;
    }

    /**
     * Générer une évaluation en PDF avec les notes des étudiants
     */
    public function genererEvaluationPDF($evaluation)
    {
        // Chargement des relations nécessaires
        $evaluation->load([
            'matiere',
            'classe',
            'notes.etudiant',
            'createdBy',
            'updatedBy'
        ]);

        // Préparation des statistiques
        $notes = $evaluation->notes;
        $stats = [
            'moyenne' => $notes->count() > 0 ? $notes->avg('note') : 0,
            'max' => $notes->count() > 0 ? $notes->max('note') : 0,
            'min' => $notes->count() > 0 ? $notes->min('note') : 0,
            'total_notes' => $notes->count(),
            'reussite' => $notes->count() > 0
                ? ($notes->filter(function ($note) use ($evaluation) {
                    return $note->note >= ($evaluation->bareme / 2);
                  })->count() / $notes->count()) * 100
                : 0
        ];

        // Préparation des données pour la vue
        $data = [
            'evaluation' => $evaluation,
            'notes' => $notes->sortBy(function($note) {
                return $note->etudiant->nom . ' ' . $note->etudiant->prenom;
            }),
            'stats' => $stats,
            'config' => [
                'school_name' => 'Ecole Speciale du Bâtiment et des Travaux Publics',
                'school_logo' => 'images/esbtp_logo.png',
                'school_address' => config('school.address', 'Yakro, Côte d\'Ivoire'),
                'school_phone' => config('school.phone', ''),
                'school_email' => config('school.email', '')
            ],
            'date_edition' => Carbon::now()->locale('fr')->isoFormat('LL')
        ];

        // Génération du PDF
        $pdf = PDF::loadView('pdf.evaluation', $data);
        $pdf->setPaper('A4');

        return $pdf;
    }

    /**
     * Générer le PV de délibération en PDF
     */
    public function genererPVDeliberationPDF(ESBTPClasse $classe, $periode, $anneeUniversitaireId)
    {
        $bulletins = ESBTPBulletin::where('classe_id', $classe->id)
            ->where('periode', $periode)
            ->where('annee_universitaire_id', $anneeUniversitaireId)
            ->orderBy('rang')
            ->get();

        $matieres = $classe->matieres->sortBy('name');

        $data = [
            'classe' => $classe,
            'periode' => $periode,
            'bulletins' => $bulletins,
            'matieres' => $matieres,
            'date_edition' => Carbon::now()->locale('fr')->isoFormat('LL')
        ];

        $pdf = PDF::loadView('pdf.pv_deliberation', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf;
    }

    /**
     * Générer le rapport d'absences en PDF
     */
    public function genererRapportAbsencesPDF(ESBTPClasse $classe, $periode, $anneeUniversitaireId)
    {
        $absenceService = app(ESBTPAbsenceService::class);
        $rapport = $absenceService->genererRapportClasse($classe, $periode, $anneeUniversitaireId);

        $data = [
            'classe' => $classe,
            'periode' => $periode,
            'rapport' => $rapport,
            'date_edition' => Carbon::now()->locale('fr')->isoFormat('LL')
        ];

        $pdf = PDF::loadView('pdf.rapport_absences', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf;
    }

    /**
     * Générer l'emploi du temps en PDF
     *
     * @param ESBTPEmploiTemps $emploiTemps
     * @return \Barryvdh\DomPDF\PDF
     */
    public function genererEmploiTempsPDF(ESBTPEmploiTemps $emploiTemps)
    {
        // Charger les séances pour cet emploi du temps
        $emploiTemps->load([
            'seances.matiere',
            'classe',
            'classe.filiere',
            'classe.niveau',
            'annee'
        ]);

        // Grouper les séances par jour
        $seancesParJour = $emploiTemps->getSeancesParJour();

        // Récupérer les heures de début et de fin pour l'affichage (créneaux d'une heure)
        $heuresDebut = [];
        $heuresFin = [];
        for ($heure = 8; $heure < 18; $heure++) {
            $heuresDebut[] = sprintf('%02d:00', $heure);
            $heuresFin[] = sprintf('%02d:00', $heure + 1);
        }

        // Noms des jours pour l'affichage
        $joursNoms = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
        ];

        // Créer les variables $timeSlots et $days pour la vue
        $timeSlots = $heuresDebut;
        $days = array_keys($joursNoms);

        // Calcul des statistiques par matière
        $matiereStats = [];
        foreach ($emploiTemps->seances as $seance) {
            $matiereName = $seance->matiere ? $seance->matiere->name : 'Non définie';
            if (!isset($matiereStats[$matiereName])) {
                $matiereStats[$matiereName] = 0;
            }
            $matiereStats[$matiereName]++;
        }

        $data = [
            'emploiTemps' => $emploiTemps,
            'seances' => $emploiTemps->seances,
            'seancesParJour' => $seancesParJour,
            'heuresDebut' => $heuresDebut,
            'heuresFin' => $heuresFin,
            'joursNoms' => $joursNoms,
            'matiereStats' => $matiereStats,
            'timeSlots' => $timeSlots,
            'days' => $days,
            'date_edition' => Carbon::now()->locale('fr')->isoFormat('LL')
        ];

        $pdf = PDF::loadView('pdf.emploi_temps', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf;
    }
}

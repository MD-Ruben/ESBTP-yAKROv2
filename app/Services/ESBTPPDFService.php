<?php

namespace App\Services;

use App\Models\ESBTPBulletin;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPResultatMatiere;
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
}

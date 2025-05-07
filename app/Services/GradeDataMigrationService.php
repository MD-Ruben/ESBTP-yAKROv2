<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\StudentGrade;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPNote;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GradeDataMigrationService
{
    /**
     * Migre les évaluations et les notes de l'ancien système vers le nouveau.
     *
     * @return array Statistiques de migration
     */
    public function migrateGradeData()
    {
        $stats = [
            'evaluations' => [
                'total' => 0,
                'migrated' => 0,
                'skipped' => 0,
                'errors' => 0
            ],
            'grades' => [
                'total' => 0,
                'migrated' => 0,
                'skipped' => 0,
                'errors' => 0
            ]
        ];

        try {
            // Commencer la transaction
            DB::beginTransaction();

            // 1. Migrer les évaluations
            $oldEvaluations = ESBTPEvaluation::with(['matiere', 'classe'])->get();
            $stats['evaluations']['total'] = $oldEvaluations->count();

            foreach ($oldEvaluations as $oldEval) {
                try {
                    // Vérifier si l'évaluation a déjà été migrée
                    $existingEval = Evaluation::where('title', $oldEval->titre)
                        ->where('class_id', $oldEval->classe_id)
                        ->where('subject_id', $oldEval->matiere_id)
                        ->where('date', $oldEval->date)
                        ->first();

                    if ($existingEval) {
                        $stats['evaluations']['skipped']++;
                        continue;
                    }

                    // Trouver l'enseignant
                    $teacher = Teacher::whereHas('user', function($q) use ($oldEval) {
                        $q->where('name', $oldEval->enseignant);
                    })->first();

                    if (!$teacher) {
                        // Créer un enseignant par défaut si nécessaire
                        Log::warning("Enseignant non trouvé pour l'évaluation {$oldEval->id}");
                        $stats['evaluations']['skipped']++;
                        continue;
                    }

                    // Trouver l'année scolaire actuelle
                    $schoolYear = SchoolYear::where('is_current', true)->first();
                    if (!$schoolYear) {
                        Log::warning("Année scolaire non trouvée");
                        $stats['evaluations']['skipped']++;
                        continue;
                    }

                    // Créer une nouvelle évaluation
                    $newEval = new Evaluation();
                    $newEval->title = $oldEval->titre;
                    $newEval->type = $this->mapEvaluationType($oldEval->type);
                    $newEval->description = $oldEval->description;
                    $newEval->date = $oldEval->date;
                    $newEval->semester = $oldEval->periode ?? 1;
                    $newEval->total_points = $oldEval->bareme ?? 20;
                    $newEval->passing_grade = $oldEval->note_passage ?? ($oldEval->bareme ? $oldEval->bareme / 2 : 10);
                    $newEval->coefficient = $oldEval->coefficient ?? 1;
                    $newEval->is_published = $oldEval->est_publie ?? false;
                    $newEval->class_id = $oldEval->classe_id;
                    $newEval->subject_id = $oldEval->matiere_id;
                    $newEval->teacher_id = $teacher->id;
                    $newEval->school_year_id = $schoolYear->id;
                    $newEval->created_by = $oldEval->created_by;
                    $newEval->updated_by = $oldEval->updated_by;
                    $newEval->created_at = $oldEval->created_at;
                    $newEval->updated_at = $oldEval->updated_at;
                    $newEval->save();

                    // Migrer les notes associées
                    $oldNotes = ESBTPNote::where('evaluation_id', $oldEval->id)->get();
                    $stats['grades']['total'] += $oldNotes->count();

                    foreach ($oldNotes as $oldNote) {
                        try {
                            // Vérifier si la note a déjà été migrée
                            $existingGrade = StudentGrade::where('evaluation_id', $newEval->id)
                                ->where('student_id', $oldNote->etudiant_id)
                                ->first();

                            if ($existingGrade) {
                                $stats['grades']['skipped']++;
                                continue;
                            }

                            // Vérifier si l'étudiant existe
                            $student = Student::find($oldNote->etudiant_id);
                            if (!$student) {
                                Log::warning("Étudiant non trouvé pour la note {$oldNote->id}");
                                $stats['grades']['skipped']++;
                                continue;
                            }

                            // Créer une nouvelle note
                            $newGrade = new StudentGrade();
                            $newGrade->evaluation_id = $newEval->id;
                            $newGrade->student_id = $oldNote->etudiant_id;
                            $newGrade->grade = $oldNote->valeur;
                            $newGrade->status = $oldNote->est_absent ? 'absent' : 'present';
                            $newGrade->comment = $oldNote->commentaire;
                            $newGrade->created_by = $oldNote->created_by;
                            $newGrade->updated_by = $oldNote->updated_by;
                            $newGrade->created_at = $oldNote->created_at;
                            $newGrade->updated_at = $oldNote->updated_at;
                            $newGrade->save();

                            $stats['grades']['migrated']++;
                        } catch (\Exception $e) {
                            Log::error("Erreur lors de la migration de la note {$oldNote->id}: " . $e->getMessage());
                            $stats['grades']['errors']++;
                        }
                    }

                    $stats['evaluations']['migrated']++;
                } catch (\Exception $e) {
                    Log::error("Erreur lors de la migration de l'évaluation {$oldEval->id}: " . $e->getMessage());
                    $stats['evaluations']['errors']++;
                }
            }

            // Valider la transaction
            DB::commit();

            return $stats;
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            Log::error("Erreur globale lors de la migration des notes: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Convertit les types d'évaluation de l'ancien système vers le nouveau.
     *
     * @param string|null $oldType
     * @return string
     */
    private function mapEvaluationType($oldType)
    {
        switch (strtolower($oldType ?? '')) {
            case 'devoir':
                return 'devoir';
            case 'exam':
            case 'examen':
                return 'examen';
            case 'controle':
            case 'contrôle':
                return 'controle';
            case 'tp':
            case 'travaux pratiques':
                return 'tp';
            case 'projet':
                return 'projet';
            case 'oral':
                return 'oral';
            default:
                return 'autre';
        }
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPInscription;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\ESBTPBulletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentProgressionController extends Controller
{
    /**
     * Process progression decisions for multiple students
     */
    public function processProgression(Request $request)
    {
        $this->validate($request, [
            'decisions' => 'required|array',
            'decisions.*.student_id' => 'required|exists:esbtp_etudiants,id',
            'decisions.*.action' => 'required|in:promote,repeat',
            'decisions.*.next_class_id' => 'required_if:decisions.*.action,promote|exists:esbtp_classes,id',
            'annee_universitaire_id' => 'required|exists:esbtp_annee_universitaires,id'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->decisions as $decision) {
                $student = ESBTPEtudiant::findOrFail($decision['student_id']);

                // Get current inscription
                $currentInscription = $student->inscriptions()
                    ->where('annee_universitaire_id', $request->annee_universitaire_id)
                    ->where('status', 'active')
                    ->first();

                if (!$currentInscription) {
                    throw new \Exception("No active inscription found for student {$student->id}");
                }

                // Create new inscription for next year
                $newInscription = new ESBTPInscription([
                    'etudiant_id' => $student->id,
                    'annee_universitaire_id' => $request->annee_universitaire_id,
                    'classe_id' => $decision['action'] === 'promote' ? $decision['next_class_id'] : $currentInscription->classe_id,
                    'status' => 'active',
                    'is_redoublant' => $decision['action'] === 'repeat',
                    'date_inscription' => now(),
                    'created_by' => auth()->id()
                ]);

                $newInscription->save();

                // Update current inscription status
                $currentInscription->status = 'completed';
                $currentInscription->save();

                // Log the progression decision
                Log::info('Student Progression', [
                    'student_id' => $student->id,
                    'action' => $decision['action'],
                    'from_class' => $currentInscription->classe_id,
                    'to_class' => $newInscription->classe_id,
                    'academic_year' => $request->annee_universitaire_id,
                    'processed_by' => auth()->id()
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Student progression processed successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get progression recommendations for a class
     */
    public function getRecommendations($classeId, $anneeUniversitaireId)
    {
        $classe = ESBTPClasse::findOrFail($classeId);

        // Get all active students in the class
        $students = ESBTPEtudiant::whereHas('inscriptions', function($query) use ($classeId, $anneeUniversitaireId) {
            $query->where('classe_id', $classeId)
                  ->where('annee_universitaire_id', $anneeUniversitaireId)
                  ->where('status', 'active');
        })->get();

        $recommendations = [];

        foreach ($students as $student) {
            // Get student's bulletin
            $bulletin = ESBTPBulletin::where('etudiant_id', $student->id)
                ->where('classe_id', $classeId)
                ->where('annee_universitaire_id', $anneeUniversitaireId)
                ->where('periode', 'annuel')
                ->first();

            if (!$bulletin) {
                continue;
            }

            // Calculate recommendation based on grades and attendance
            $recommendation = $this->calculateRecommendation($student, $bulletin);

            // Calculate attendance rate
            $attendanceRate = 0;
            if ($student->absences) {
                $totalSessions = $student->absences->count();
                if ($totalSessions > 0) {
                    $presentSessions = $student->absences->where('status', 'present')->count();
                    $attendanceRate = round(($presentSessions / $totalSessions) * 100, 2);
                }
            }

            $recommendations[] = [
                'student_id' => $student->id,
                'student_name' => $student->nom_complet,
                'current_class' => $classe->name,
                'average_grade' => $bulletin->moyenne_generale,
                'attendance_rate' => $attendanceRate,
                'recommendation' => $recommendation,
                'possible_next_classes' => $this->getPossibleNextClasses($classe)
            ];
        }

        return response()->json($recommendations);
    }

    /**
     * Calculate progression recommendation for a student
     */
    private function calculateRecommendation($student, $bulletin)
    {
        // Default passing grade is 10/20
        $passingGrade = 10;

        // Check if student meets promotion criteria
        if ($bulletin->moyenne_generale >= $passingGrade) {
            return 'promote';
        }

        // If average is below passing grade, recommend repeating
        return 'repeat';
    }

    /**
     * Get possible next classes for progression
     */
    private function getPossibleNextClasses($currentClass)
    {
        // Get classes in the same filière but next level
        return ESBTPClasse::where('filiere_id', $currentClass->filiere_id)
            ->where('niveau', '>', $currentClass->niveau)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Display the student progression management page
     */
    public function index()
    {
        $classes = ESBTPClasse::where('is_active', true)->orderBy('name')->get();
        $annees = ESBTPAnneeUniversitaire::orderBy('annee_debut', 'desc')->get();

        return view('esbtp.progression.index', compact('classes', 'annees'));
    }
}

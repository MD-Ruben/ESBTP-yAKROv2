<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ESBTPAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DebugAttendanceStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esbtp:debug-attendance-statuses {--student_id= : ID de l\'étudiant à analyser}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyse les statuts utilisés pour les absences dans la base de données et le code';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Analyse des statuts d\'absence dans la base de données');

        // Compter les différents statuts dans la table attendance
        $statuses = DB::table('e_s_b_t_p_attendances')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $this->table(['Statut', 'Nombre'], $statuses->map(function ($status) {
            return [$status->status, $status->total];
        }));

        // Afficher les statuts utilisés dans le code pour la comptabilisation
        $this->info('');
        $this->info('Statuts utilisés dans le code pour la comptabilisation des absences justifiées:');
        $this->line("- 'Absence justifiée'");
        $this->line("- 'excuse'");

        $this->info('');
        $this->info('Statuts utilisés dans le code pour la comptabilisation des absences non justifiées:');
        $this->line("- 'Absence non justifiée'");
        $this->line("- 'absent'");

        if ($this->option('verbose') || $this->option('v')) {
            $this->info('');
            $this->info('Analyse détaillée des heures d\'absence par statut:');

            $studentId = $this->option('student_id');

            $query = DB::table('e_s_b_t_p_attendances')
                ->join('e_s_b_t_p_sessions', 'e_s_b_t_p_attendances.e_s_b_t_p_session_id', '=', 'e_s_b_t_p_sessions.id')
                ->select(
                    'e_s_b_t_p_attendances.id',
                    'e_s_b_t_p_sessions.date',
                    'e_s_b_t_p_sessions.heure_debut',
                    'e_s_b_t_p_sessions.heure_fin',
                    'e_s_b_t_p_attendances.status'
                );

            if ($studentId) {
                $query->where('e_s_b_t_p_attendances.e_s_b_t_p_etudiant_id', $studentId);

                // Test de génération d'absence
                $this->info('');
                $this->info('Test de calcul des absences pour l\'étudiant #' . $studentId);

                try {
                    $etudiant = \App\Models\ESBTPEtudiant::findOrFail($studentId);
                    $this->line("Étudiant: " . $etudiant->nomComplet);

                    $controller = new \App\Http\Controllers\ESBTPBulletinController();

                    // Tester avec la période et année en cours
                    $anneeCourante = \App\Models\ESBTPAnneeUniversitaire::where('statut', 'actif')->first();

                    if (!$anneeCourante) {
                        $this->error('Aucune année universitaire active trouvée');
                        return 1;
                    }

                    $this->line("Année universitaire: " . $anneeCourante->annee);
                    $this->line("Test pour les périodes disponibles:");

                    $periodes = ['Premier semestre', 'Deuxième semestre', 'Année'];

                    foreach ($periodes as $periode) {
                        $this->info('');
                        $this->info("Calcul pour la période: " . $periode);

                        $dateDebut = null;
                        $dateFin = null;

                        // Définir les dates de début et fin selon la période
                        if ($periode == 'Premier semestre') {
                            $dateDebut = $anneeCourante->date_debut;
                            $dateFin = Carbon::parse($anneeCourante->date_debut)->addMonths(6)->format('Y-m-d');
                        } elseif ($periode == 'Deuxième semestre') {
                            $dateDebut = Carbon::parse($anneeCourante->date_debut)->addMonths(6)->format('Y-m-d');
                            $dateFin = $anneeCourante->date_fin;
                        } else {
                            $dateDebut = $anneeCourante->date_debut;
                            $dateFin = $anneeCourante->date_fin;
                        }

                        $this->line("Période du $dateDebut au $dateFin");

                        try {
                            $absences = $controller->calculerAbsencesAttendance($etudiant->id, $dateDebut, $dateFin);
                            $this->line("Absences justifiées: " . ($absences['absencesJustifiees'] ?? 'Non défini') . " heures");
                            $this->line("Absences non justifiées: " . ($absences['absencesNonJustifiees'] ?? 'Non défini') . " heures");

                            // Afficher aussi les variantes snake_case
                            if (isset($absences['absences_justifiees'])) {
                                $this->line("absences_justifiees: " . $absences['absences_justifiees'] . " heures");
                            }
                            if (isset($absences['absences_non_justifiees'])) {
                                $this->line("absences_non_justifiees: " . $absences['absences_non_justifiees'] . " heures");
                            }
                        } catch (\Exception $e) {
                            $this->error("Erreur lors du calcul des absences: " . $e->getMessage());
                            $this->line("Trace: " . $e->getTraceAsString());
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("Erreur: " . $e->getMessage());
                    $this->line("Trace: " . $e->getTraceAsString());
                }
            }

            $attendances = $query->limit(10)->get();

            $this->table(['ID', 'Date', 'Heure début', 'Heure fin', 'Statut'], $attendances->map(function ($attendance) {
                return [
                    $attendance->id,
                    $attendance->date,
                    $attendance->heure_debut,
                    $attendance->heure_fin,
                    $attendance->status
                ];
            }));

            // Calcul des heures totales par statut
            $statuses = $attendances->pluck('status')->unique();
            foreach ($statuses as $status) {
                $totalHours = 0;
                foreach ($attendances->where('status', $status) as $attendance) {
                    $start = Carbon::parse($attendance->heure_debut);
                    $end = Carbon::parse($attendance->heure_fin);
                    $totalHours += $end->diffInMinutes($start) / 60;
                }
                $this->line("Durée estimée totale pour '$status': " . number_format($totalHours, 2) . " heures");
            }
        }

        return 0;
    }
}

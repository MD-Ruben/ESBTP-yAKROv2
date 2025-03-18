<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPClasse;
use App\Models\ESBTPAnneeUniversitaire;

class CheckEvaluationsAnnees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esbtp:check-evaluations-annees {--fix : Fix evaluations without academic years}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update evaluations without academic years';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking evaluations without academic years...');

        // Get all evaluations without academic years
        $evaluationsSansAnnee = ESBTPEvaluation::whereNull('annee_universitaire_id')->get();
        $count = $evaluationsSansAnnee->count();

        $this->info("Found {$count} evaluations without academic years.");

        if ($count === 0) {
            return Command::SUCCESS;
        }

        $shouldFix = $this->option('fix');
        if (!$shouldFix && !$this->confirm('Do you want to update these evaluations with appropriate academic years?')) {
            return Command::SUCCESS;
        }

        // Get current academic year
        $anneeActuelle = ESBTPAnneeUniversitaire::where('is_current', true)->first();
        if (!$anneeActuelle) {
            $this->error('No current academic year defined. Cannot proceed.');
            return Command::FAILURE;
        }

        $updatedFromClasse = 0;
        $updatedDefault = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($evaluationsSansAnnee as $eval) {
            try {
                if ($eval->classe_id) {
                    $classe = ESBTPClasse::find($eval->classe_id);
                    if ($classe && $classe->annee_universitaire_id) {
                        $eval->annee_universitaire_id = $classe->annee_universitaire_id;
                        $eval->save();
                        $updatedFromClasse++;
                    } else {
                        $eval->annee_universitaire_id = $anneeActuelle->id;
                        $eval->save();
                        $updatedDefault++;
                    }
                } else {
                    $eval->annee_universitaire_id = $anneeActuelle->id;
                    $eval->save();
                    $updatedDefault++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to update evaluation ID {$eval->id}: {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Updated {$updatedFromClasse} evaluations using class academic year");
        $this->info("Updated {$updatedDefault} evaluations using current academic year");

        if ($failed > 0) {
            $this->warn("Failed to update {$failed} evaluations");
        }

        return Command::SUCCESS;
    }
}

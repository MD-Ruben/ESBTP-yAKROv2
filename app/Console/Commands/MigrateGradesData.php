<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GradeDataMigrationService;

class MigrateGradesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esbtp:migrate-grades {--force : Forcer la migration sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migre les données de notes de l\'ancien système vers le nouveau.';

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
     *
     * @param GradeDataMigrationService $service
     * @return int
     */
    public function handle(GradeDataMigrationService $service)
    {
        $this->info('===== Migration des Notes ESBTP =====');
        $this->info('Cette commande va migrer les évaluations et notes de l\'ancien système vers le nouveau.');
        
        if (!$this->option('force') && !$this->confirm('Voulez-vous continuer? Cette opération peut prendre du temps et modifier des données.')) {
            $this->info('Migration annulée.');
            return 0;
        }
        
        $this->info('Démarrage de la migration...');
        
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        
        try {
            $stats = $service->migrateGradeData();
            $bar->finish();
            
            $this->newLine();
            $this->info('Migration terminée avec succès!');
            
            $this->table(
                ['Type', 'Total', 'Migrés', 'Ignorés', 'Erreurs'],
                [
                    ['Évaluations', $stats['evaluations']['total'], $stats['evaluations']['migrated'], $stats['evaluations']['skipped'], $stats['evaluations']['errors']],
                    ['Notes', $stats['grades']['total'], $stats['grades']['migrated'], $stats['grades']['skipped'], $stats['grades']['errors']]
                ]
            );
            
            if ($stats['evaluations']['errors'] > 0 || $stats['grades']['errors'] > 0) {
                $this->warn('Il y a eu des erreurs pendant la migration. Consultez les logs pour plus de détails.');
            }
            
            return 0;
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine();
            $this->error('Une erreur est survenue lors de la migration: ' . $e->getMessage());
            return 1;
        }
    }
} 
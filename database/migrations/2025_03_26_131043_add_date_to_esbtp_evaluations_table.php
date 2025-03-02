<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToEsbtpEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe avant de la modifier
        if (Schema::hasTable('esbtp_evaluations')) {
            Schema::table('esbtp_evaluations', function (Blueprint $table) {
                if (!Schema::hasColumn('esbtp_evaluations', 'date')) {
                    $table->date('date')->nullable()->after('coefficient');
                    \Log::info('Colonne date ajoutée à la table esbtp_evaluations.');
                } else {
                    \Log::info('La colonne date existe déjà dans la table esbtp_evaluations.');
                }
            });
        } else {
            \Log::error('Impossible d\'ajouter la colonne date : la table esbtp_evaluations n\'existe pas.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('esbtp_evaluations')) {
            Schema::table('esbtp_evaluations', function (Blueprint $table) {
                if (Schema::hasColumn('esbtp_evaluations', 'date')) {
                    $table->dropColumn('date');
                    \Log::info('Colonne date supprimée de la table esbtp_evaluations.');
                }
            });
        }
    }
}

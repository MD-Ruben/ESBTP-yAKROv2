<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEsbtpMatiereNiveauTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_matiere_niveau', function (Blueprint $table) {
            // Add coefficient field if it doesn't exist
            if (!Schema::hasColumn('esbtp_matiere_niveau', 'coefficient')) {
                $table->integer('coefficient')->default(1)->after('niveau_etude_id');
            }

            // Add heures_cours field if it doesn't exist
            if (!Schema::hasColumn('esbtp_matiere_niveau', 'heures_cours')) {
                $table->integer('heures_cours')->default(0)->after('coefficient');
            }

            // Add is_active field if it doesn't exist
            if (!Schema::hasColumn('esbtp_matiere_niveau', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('heures_cours');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esbtp_matiere_niveau', function (Blueprint $table) {
            if (Schema::hasColumn('esbtp_matiere_niveau', 'coefficient')) {
                $table->dropColumn('coefficient');
            }

            if (Schema::hasColumn('esbtp_matiere_niveau', 'heures_cours')) {
                $table->dropColumn('heures_cours');
            }

            if (Schema::hasColumn('esbtp_matiere_niveau', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
}

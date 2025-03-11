<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ESBTPEmploiTemps;

class AddAnneeUniversitaireIdToEsbtpEmploiTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_emploi_temps', function (Blueprint $table) {
            if (!Schema::hasColumn('esbtp_emploi_temps', 'annee_universitaire_id')) {
                $table->foreignId('annee_universitaire_id')->nullable()->after('classe_id')
                    ->constrained('esbtp_annee_universitaires')->onDelete('set null');
            }
        });

        // Mettre à jour les emplois du temps existants avec l'année universitaire de leur classe
        $emploisTemps = ESBTPEmploiTemps::with('classe')->get();
        foreach ($emploisTemps as $emploiTemps) {
            if ($emploiTemps->classe) {
                $emploiTemps->annee_universitaire_id = $emploiTemps->classe->annee_universitaire_id;
                $emploiTemps->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esbtp_emploi_temps', function (Blueprint $table) {
            if (Schema::hasColumn('esbtp_emploi_temps', 'annee_universitaire_id')) {
                $table->dropForeign(['annee_universitaire_id']);
                $table->dropColumn('annee_universitaire_id');
            }
        });
    }
}

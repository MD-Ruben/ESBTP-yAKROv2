<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormationIdToEsbtpClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('esbtp_classes', 'formation_id')) {
                $table->foreignId('formation_id')->nullable()->after('annee_universitaire_id')->constrained('esbtp_formations');
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
        Schema::table('esbtp_classes', function (Blueprint $table) {
            if (Schema::hasColumn('esbtp_classes', 'formation_id')) {
                $table->dropForeign(['formation_id']);
                $table->dropColumn('formation_id');
            }
        });
    }
}

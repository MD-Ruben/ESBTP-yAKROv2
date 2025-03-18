<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('esbtp_evaluations', function (Blueprint $table) {
            // Ajouter la colonne periode pour identifier à quelle période appartient l'évaluation
            $table->string('periode')->default('semestre1')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esbtp_evaluations', function (Blueprint $table) {
            $table->dropColumn('periode');
        });
    }
};

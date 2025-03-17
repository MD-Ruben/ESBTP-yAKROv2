<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_evaluations', function (Blueprint $table) {
            // Ajouter les colonnes manquantes
            $table->string('titre')->after('id');
            $table->text('description')->nullable()->after('titre');
            $table->integer('duree_minutes')->nullable()->after('bareme');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esbtp_evaluations', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            $table->dropColumn(['titre', 'description', 'duree_minutes']);
        });
    }
};

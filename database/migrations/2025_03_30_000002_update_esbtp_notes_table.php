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
        Schema::table('esbtp_notes', function (Blueprint $table) {
            // Ajouter les nouveaux champs
            $table->string('semestre')->after('etudiant_id');
            $table->string('annee_universitaire')->after('semestre');
            $table->string('type_evaluation')->after('note');
            $table->decimal('moyenne_matiere', 5, 2)->nullable()->after('type_evaluation');
            $table->integer('rang_matiere')->nullable()->after('moyenne_matiere');
            $table->text('appreciation')->nullable()->after('rang_matiere');

            // Modifier les champs existants
            $table->decimal('note', 5, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esbtp_notes', function (Blueprint $table) {
            $table->dropColumn([
                'semestre',
                'annee_universitaire',
                'type_evaluation',
                'moyenne_matiere',
                'rang_matiere',
                'appreciation'
            ]);
            $table->decimal('note', 8, 2)->change();
        });
    }
};

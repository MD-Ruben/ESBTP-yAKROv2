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
            // Add composite indexes for better performance in querying notes
            $table->index(['etudiant_id', 'semestre', 'classe_id'], 'idx_notes_etudiant_semestre_classe');
            $table->index(['evaluation_id', 'etudiant_id'], 'idx_notes_evaluation_etudiant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esbtp_notes', function (Blueprint $table) {
            $table->dropIndex('idx_notes_etudiant_semestre_classe');
            $table->dropIndex('idx_notes_evaluation_etudiant');
        });
    }
};

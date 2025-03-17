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
        Schema::create('esbtp_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
            $table->decimal('note', 8, 2);
            $table->foreignId('classe_id')->constrained('esbtp_classes');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esbtp_notes');
    }
};

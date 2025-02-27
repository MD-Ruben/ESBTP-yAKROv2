<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter les migrations.
     */
    public function up(): void
    {
        Schema::create('evaluation_supervisor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->default('surveillant'); // surveillant, correcteur, responsable
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['evaluation_id', 'user_id', 'role'], 'evaluation_user_role_unique');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_supervisor');
    }
}; 
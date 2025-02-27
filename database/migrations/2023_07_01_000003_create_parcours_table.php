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
        Schema::create('parcours', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Code unique du parcours');
            $table->string('name')->comment('Nom du parcours');
            $table->text('description')->nullable()->comment('Description du parcours');
            
            $table->foreignId('formation_id')->comment('Formation à laquelle appartient le parcours')
                  ->constrained('formations')->cascadeOnDelete();
                  
            $table->foreignId('responsable_id')->nullable()->comment('Enseignant responsable du parcours')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé le parcours')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour le parcours')
                  ->constrained('users')->nullOnDelete();
                  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcours');
    }
}; 
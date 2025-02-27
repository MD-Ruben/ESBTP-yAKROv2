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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Code unique de la formation');
            $table->string('name')->comment('Nom de la formation');
            $table->text('description')->nullable()->comment('Description de la formation');
            $table->string('level')->comment('Niveau de la formation (Licence, Master, Doctorat, etc.)');
            $table->integer('duration')->default(3)->comment('Durée de la formation en années');
            
            $table->foreignId('ufr_id')->comment('UFR à laquelle appartient la formation')
                  ->constrained('ufrs')->cascadeOnDelete();
                  
            $table->foreignId('department_id')->nullable()->comment('Département responsable de la formation')
                  ->constrained('departments')->nullOnDelete();
                  
            $table->foreignId('coordinator_id')->nullable()->comment('Enseignant coordinateur de la formation')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé la formation')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour la formation')
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
        Schema::dropIfExists('formations');
    }
}; 
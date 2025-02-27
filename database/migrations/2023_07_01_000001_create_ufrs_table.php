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
        Schema::create('ufrs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Code unique de l\'UFR');
            $table->string('name')->comment('Nom de l\'UFR');
            $table->text('description')->nullable()->comment('Description de l\'UFR');
            $table->foreignId('director_id')->nullable()->comment('ID du directeur de l\'UFR')
                  ->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé l\'UFR')
                  ->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour l\'UFR')
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
        Schema::dropIfExists('ufrs');
    }
}; 
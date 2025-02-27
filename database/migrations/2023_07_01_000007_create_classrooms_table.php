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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nom ou numéro de la salle');
            $table->string('building')->nullable()->comment('Bâtiment où se trouve la salle');
            $table->integer('floor')->default(0)->comment('Étage');
            $table->string('room_number')->nullable()->comment('Numéro de la salle');
            $table->integer('capacity')->default(0)->comment('Capacité d\'accueil (nombre de places)');
            $table->string('type')->nullable()->comment('Type de salle (amphithéâtre, salle de TD, laboratoire, etc.)');
            $table->boolean('has_projector')->default(false)->comment('Présence d\'un projecteur');
            $table->boolean('has_computers')->default(false)->comment('Présence d\'ordinateurs');
            $table->boolean('has_whiteboard')->default(false)->comment('Présence d\'un tableau blanc');
            $table->boolean('has_blackboard')->default(false)->comment('Présence d\'un tableau noir');
            $table->boolean('has_internet')->default(false)->comment('Accès à Internet');
            $table->boolean('is_accessible')->default(false)->comment('Accessibilité pour personnes à mobilité réduite');
            $table->text('notes')->nullable()->comment('Notes supplémentaires');
            $table->string('status')->default('available')->comment('Statut (disponible, en maintenance, hors service)');
            
            $table->foreignId('created_by')->nullable()->comment('Utilisateur ayant créé la salle')
                  ->constrained('users')->nullOnDelete();
                  
            $table->foreignId('updated_by')->nullable()->comment('Utilisateur ayant mis à jour la salle')
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
        Schema::dropIfExists('classrooms');
    }
}; 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            
            // Relations avec les autres tables
            $table->foreignId('filiere_id')->constrained('esbtp_filieres');
            $table->foreignId('niveau_etude_id')->constrained('esbtp_niveau_etudes');
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires');
            
            // Capacité et informations supplémentaires
            $table->integer('capacity')->default(50);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Traçabilité
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour améliorer les performances des requêtes fréquentes
            $table->index(['filiere_id', 'niveau_etude_id', 'annee_universitaire_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_classes');
    }
} 
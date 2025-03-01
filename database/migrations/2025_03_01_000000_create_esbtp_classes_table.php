<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateESBTPClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe déjà
        if (!Schema::hasTable('esbtp_classes')) {
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
                
                // Index avec un nom personnalisé plus court pour éviter l'erreur de nom trop long
                $table->index(['filiere_id', 'niveau_etude_id', 'annee_universitaire_id'], 'idx_esbtp_classes_filiere_niveau_annee');
            });
        }
        // Si la table existe déjà, nous pouvons la modifier si nécessaire
        else {
            Schema::table('esbtp_classes', function (Blueprint $table) {
                // Vérifier si l'index existe déjà
                $indexExists = collect(DB::select("SHOW INDEXES FROM esbtp_classes"))->pluck('Key_name')->contains('idx_esbtp_classes_filiere_niveau_annee');
                
                if (!$indexExists) {
                    // Ajouter l'index avec un nom court
                    $table->index(['filiere_id', 'niveau_etude_id', 'annee_universitaire_id'], 'idx_esbtp_classes_filiere_niveau_annee');
                }
            });
        }
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
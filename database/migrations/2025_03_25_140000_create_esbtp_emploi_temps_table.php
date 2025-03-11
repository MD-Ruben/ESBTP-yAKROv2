<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPEmploiTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table des emplois du temps existe déjà
        $tableEmploiTempsExists = Schema::hasTable('esbtp_emploi_temps');

        // Si la table n'existe pas, la créer
        if (!$tableEmploiTempsExists) {
            Schema::create('esbtp_emploi_temps', function (Blueprint $table) {
                $table->id();
                $table->string('titre');
                $table->foreignId('classe_id')->constrained('esbtp_classes')->onDelete('cascade');
                $table->foreignId('annee_universitaire_id')->nullable()->constrained('esbtp_annee_universitaires')->onDelete('set null');
                $table->string('semestre')->nullable(); // 'semestre1', 'semestre2', etc.
                $table->date('date_debut')->nullable();
                $table->date('date_fin')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });

            \Log::info('Table esbtp_emploi_temps créée avec succès.');
        } else {
            \Log::info('La table esbtp_emploi_temps existe déjà.');
        }

        // Vérifier si la table des séances de cours existe déjà
        $tableSeanceCoursExists = Schema::hasTable('esbtp_seance_cours');

        // Si la table n'existe pas, la créer
        if (!$tableSeanceCoursExists) {
            Schema::create('esbtp_seance_cours', function (Blueprint $table) {
                $table->id();
                $table->foreignId('emploi_temps_id')->constrained('esbtp_emploi_temps')->onDelete('cascade');
                $table->foreignId('matiere_id')->nullable()->constrained('esbtp_matieres')->onDelete('set null');
                $table->foreignId('enseignant_id')->nullable()->constrained('users')->onDelete('set null');
                $table->integer('jour_semaine'); // 0 = Lundi, 1 = Mardi, etc.
                $table->time('heure_debut');
                $table->time('heure_fin');
                $table->string('salle')->nullable();
                $table->text('details')->nullable();
                $table->string('type_seance')->default('cours'); // 'cours', 'td', 'tp', etc.
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });

            \Log::info('Table esbtp_seance_cours créée avec succès.');
        } else {
            \Log::info('La table esbtp_seance_cours existe déjà.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_seance_cours');
        Schema::dropIfExists('esbtp_emploi_temps');
    }
}

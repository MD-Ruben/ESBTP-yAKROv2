<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si la table existe déjà
        $tableExists = Schema::hasTable('esbtp_evaluations');
        
        // Si la table n'existe pas, la créer
        if (!$tableExists) {
            Schema::create('esbtp_evaluations', function (Blueprint $table) {
                $table->id();
                $table->string('titre');
                $table->string('type'); // 'examen', 'devoir', 'quiz', etc.
                $table->timestamp('date_evaluation')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('classe_id')->constrained('esbtp_classes')->onDelete('cascade');
                $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
                $table->float('coefficient', 5, 2)->default(1.0);
                $table->float('bareme', 8, 2)->default(20.0);
                $table->integer('duree_minutes')->nullable();
                $table->boolean('is_published')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
            
            \Log::info('Table esbtp_evaluations créée avec succès.');
        } else {
            \Log::info('La table esbtp_evaluations existe déjà.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_evaluations');
    }
} 
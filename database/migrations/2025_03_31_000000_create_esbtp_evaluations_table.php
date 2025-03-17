<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_id')->constrained('esbtp_matieres')->onDelete('cascade');
            $table->foreignId('classe_id')->constrained('esbtp_classes')->onDelete('cascade');
            $table->string('type'); // devoir, examen, rattrapage, etc.
            $table->date('date_evaluation');
            $table->decimal('coefficient', 3, 1)->default(1.0);
            $table->decimal('bareme', 5, 2)->default(20.00);
            $table->string('periode'); // semestre1, semestre2, etc.
            $table->foreignId('annee_universitaire_id')->constrained('esbtp_annee_universitaires')->onDelete('cascade');
            $table->string('status')->default('draft'); // draft, scheduled, in_progress, completed, cancelled
            $table->boolean('is_published')->default(false);
            $table->boolean('notes_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
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
};

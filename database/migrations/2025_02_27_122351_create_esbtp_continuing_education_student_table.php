<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsbtpContinuingEducationStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_continuing_education_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('continuing_education_id');
            $table->foreign('continuing_education_id', 'ce_student_ce_id_foreign')
                ->references('id')
                ->on('esbtp_continuing_education')
                ->onDelete('cascade');
                
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id', 'ce_student_student_id_foreign')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
                
            $table->date('registration_date')->nullable(); // Date d'inscription à la formation
            $table->string('status')->default('registered'); // État: registered, in_progress, completed, abandoned
            $table->string('payment_status')->default('pending'); // État du paiement: pending, partial, completed
            $table->decimal('amount_paid', 10, 2)->default(0); // Montant déjà payé
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->string('certificate_number')->nullable(); // Numéro du certificat délivré
            $table->date('certificate_date')->nullable(); // Date de délivrance du certificat
            $table->timestamps();

            // Assurer l'unicité de la relation
            $table->unique(['continuing_education_id', 'student_id'], 'ce_student_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_continuing_education_student');
    }
}

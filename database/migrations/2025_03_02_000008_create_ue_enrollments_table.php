<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUeEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table already exists to avoid errors
        if (!Schema::hasTable('ue_enrollments')) {
            Schema::create('ue_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('unite_enseignement_id')->constrained('unite_enseignements')->onDelete('cascade');
                $table->string('academic_year');
                $table->string('semester');
                $table->boolean('is_validated')->default(false);
                $table->decimal('final_grade', 5, 2)->nullable();
                $table->text('comments')->nullable();
                $table->timestamps();
                
                // Make sure a student can only enroll once in a UE per academic year and semester
                $table->unique(['student_id', 'unite_enseignement_id', 'academic_year', 'semester'], 'ue_enrollment_unique');
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
        Schema::dropIfExists('ue_enrollments');
    }
} 
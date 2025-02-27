<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPStudyYearsTable extends Migration
{
    /**
     * Exécuter les migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_study_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->comment('1 pour 1ère année, 2 pour 2ème année, etc.');
            $table->foreignId('cycle_id')->constrained('esbtp_cycles')->onDelete('restrict');
            $table->foreignId('specialty_id')->constrained('esbtp_specialties')->onDelete('restrict');
            $table->integer('num_semesters')->default(2);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('esbtp_semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('number')->comment('1 pour 1er semestre, 2 pour 2ème semestre, etc.');
            $table->foreignId('study_year_id')->constrained('esbtp_study_years')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('esbtp_student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('study_year_id')->constrained('esbtp_study_years')->onDelete('cascade');
            $table->string('academic_year');
            $table->string('status')->default('active')->comment('active, completed, withdrawn, failed');
            $table->date('enrollment_date');
            $table->timestamps();
        });
    }

    /**
     * Annuler les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_student_enrollments');
        Schema::dropIfExists('esbtp_semesters');
        Schema::dropIfExists('esbtp_study_years');
    }
} 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTableExtended20250302 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip this migration if the students table already exists
        if (Schema::hasTable('students')) {
            // Add any missing columns to the existing students table
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'student_id_card')) {
                    $table->string('student_id_card')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'parcours_id')) {
                    $table->foreignId('parcours_id')->nullable()->constrained()->nullOnDelete();
                }
                
                if (!Schema::hasColumn('students', 'current_semester')) {
                    $table->string('current_semester')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'academic_status')) {
                    $table->string('academic_status')->default('active');
                }
                
                if (!Schema::hasColumn('students', 'enrollment_date')) {
                    $table->date('enrollment_date')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'expected_graduation_date')) {
                    $table->date('expected_graduation_date')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'previous_education')) {
                    $table->json('previous_education')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'scholarships')) {
                    $table->json('scholarships')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'internships')) {
                    $table->json('internships')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'projects')) {
                    $table->json('projects')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'notes')) {
                    $table->text('notes')->nullable();
                }
                
                if (!Schema::hasColumn('students', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                }
                
                if (!Schema::hasColumn('students', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                }
                
                if (!Schema::hasColumn('students', 'deleted_at')) {
                    $table->softDeletes();
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
        // We don't want to drop the table in the down method
        // as it might have been created by another migration
    }
} 
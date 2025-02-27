<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTableExtended20250302 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip this migration if the teachers table already exists
        if (Schema::hasTable('teachers')) {
            // Add any missing columns to the existing teachers table
            Schema::table('teachers', function (Blueprint $table) {
                if (!Schema::hasColumn('teachers', 'laboratory_id')) {
                    $table->foreignId('laboratory_id')->nullable()->constrained()->nullOnDelete();
                }
                
                if (!Schema::hasColumn('teachers', 'specialties')) {
                    $table->json('specialties')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'grade')) {
                    $table->string('grade')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'status')) {
                    $table->string('status')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'teaching_hours_due')) {
                    $table->integer('teaching_hours_due')->default(0);
                }
                
                if (!Schema::hasColumn('teachers', 'teaching_hours_done')) {
                    $table->integer('teaching_hours_done')->default(0);
                }
                
                if (!Schema::hasColumn('teachers', 'office_location')) {
                    $table->string('office_location')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'office_hours')) {
                    $table->json('office_hours')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'bio')) {
                    $table->text('bio')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'research_interests')) {
                    $table->json('research_interests')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'publications')) {
                    $table->json('publications')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'website')) {
                    $table->string('website')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'availability')) {
                    $table->json('availability')->nullable();
                }
                
                if (!Schema::hasColumn('teachers', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                }
                
                if (!Schema::hasColumn('teachers', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                }
                
                if (!Schema::hasColumn('teachers', 'deleted_at')) {
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
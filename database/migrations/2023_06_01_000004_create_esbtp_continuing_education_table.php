<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPContinuingEducationTable extends Migration
{
    /**
     * Exécuter les migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_continuing_education', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code', 50)->unique();
            $table->string('type', 50);
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->string('coordinator_name')->nullable();
            $table->integer('duration');
            $table->string('duration_unit')->default('hours')->comment('hours, days, weeks, months');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('max_participants')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->text('target_audience')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('certification')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Annuler les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_continuing_education');
    }
} 
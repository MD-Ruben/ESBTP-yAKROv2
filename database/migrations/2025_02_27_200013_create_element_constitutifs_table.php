<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementConstitutifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('element_constitutifs')) {
            Schema::create('element_constitutifs', function (Blueprint $table) {
                $table->id();
                $table->string('code');
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('unite_enseignement_id')->comment('UE à laquelle appartient l\'EC')
                      ->constrained()->cascadeOnDelete();
                $table->integer('credits')->default(0)->comment('Nombre de crédits ECTS');
                $table->float('coefficient', 4, 2)->default(1.0);
                $table->integer('cm_hours')->default(0)->comment('Heures de cours magistraux');
                $table->integer('td_hours')->default(0)->comment('Heures de travaux dirigés');
                $table->integer('tp_hours')->default(0)->comment('Heures de travaux pratiques');
                $table->integer('project_hours')->default(0)->comment('Heures de projet');
                $table->integer('personal_work_hours')->default(0)->comment('Heures de travail personnel');
                $table->foreignId('responsable_id')->nullable()->comment('Responsable de l\'EC')
                      ->constrained('users')->nullOnDelete();
                $table->boolean('is_optional')->default(false);
                $table->foreignId('created_by')->nullable()
                      ->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()
                      ->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('element_constitutifs');
    }
}

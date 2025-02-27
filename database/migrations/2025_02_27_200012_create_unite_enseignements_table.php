<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniteEnseignementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('unite_enseignements')) {
            Schema::create('unite_enseignements', function (Blueprint $table) {
                $table->id();
                $table->string('code');
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('parcours_id')->comment('Parcours auquel appartient l\'UE')
                      ->constrained()->cascadeOnDelete();
                $table->integer('semester')->comment('Numéro du semestre');
                $table->integer('credits')->default(0)->comment('Nombre de crédits ECTS');
                $table->float('coefficient', 4, 2)->default(1.0);
                $table->enum('type', ['obligatoire', 'optionnelle', 'libre'])->default('obligatoire');
                $table->foreignId('responsable_id')->nullable()->comment('Responsable de l\'UE')
                      ->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('unite_enseignements');
    }
}

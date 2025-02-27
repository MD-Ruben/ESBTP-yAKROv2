<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('parcours')) {
            Schema::create('parcours', function (Blueprint $table) {
                $table->id();
                $table->string('code');
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('formation_id')->nullable()->comment('ID de la formation à laquelle appartient le parcours')
                      ->constrained()->nullOnDelete();
                $table->foreignId('responsable_id')->nullable()->comment('ID de l\'utilisateur qui est responsable du parcours')
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
        Schema::dropIfExists('parcours');
    }
}

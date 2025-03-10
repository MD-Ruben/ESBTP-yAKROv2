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
        Schema::create('parcours', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nom du parcours');
            $table->text('description')->nullable()->comment('Description du parcours');
            $table->boolean('is_active')->default(true)->comment('Statut du parcours');
            $table->timestamps();
        });
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

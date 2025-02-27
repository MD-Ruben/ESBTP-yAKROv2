<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsbtpDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du département (BTP, Mines, Géologie, Pétrole, etc.)
            $table->string('code')->unique(); // Code unique du département
            $table->text('description')->nullable(); // Description du département
            $table->string('head_name')->nullable(); // Nom du chef de département
            $table->string('email')->nullable(); // Email de contact du département
            $table->string('phone')->nullable(); // Téléphone de contact du département
            $table->string('logo')->nullable(); // Logo du département
            $table->boolean('is_active')->default(true); // Statut actif/inactif
            $table->softDeletes(); // Pour la suppression logique
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
        Schema::dropIfExists('esbtp_departments');
    }
}

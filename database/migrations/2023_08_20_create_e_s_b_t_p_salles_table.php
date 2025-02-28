<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPSallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_s_b_t_p_salles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de la salle (Salle A, Amphi B, etc.)
            $table->string('code')->unique(); // Code unique de la salle
            $table->string('type'); // Type de salle (Amphi, TD, TP, etc.)
            $table->integer('capacity')->default(0); // Capacité d'accueil
            $table->string('building')->nullable(); // Bâtiment où se trouve la salle
            $table->integer('floor')->default(0); // Étage de la salle
            $table->text('description')->nullable(); // Description détaillée
            $table->boolean('is_active')->default(true); // Statut de la salle
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('e_s_b_t_p_salles');
    }
} 
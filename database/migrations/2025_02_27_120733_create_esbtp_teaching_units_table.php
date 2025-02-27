<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsbtpTeachingUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_teaching_units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de l'UE
            $table->string('code')->unique(); // Code unique de l'UE
            $table->foreignId('semester_id')->constrained('esbtp_semesters'); // Semestre associé
            $table->text('description')->nullable(); // Description de l'UE
            $table->integer('credits'); // Nombre de crédits de l'UE
            $table->integer('coefficient')->default(1); // Coefficient de l'UE
            $table->string('responsible_name')->nullable(); // Nom du responsable de l'UE
            $table->string('responsible_email')->nullable(); // Email du responsable
            $table->boolean('is_optional')->default(false); // UE optionnelle ou obligatoire
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
        Schema::dropIfExists('esbtp_teaching_units');
    }
}

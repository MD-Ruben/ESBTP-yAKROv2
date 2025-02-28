<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESBTPFilieresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_filieres', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de la filière
            $table->string('code')->unique(); // Code unique de la filière
            $table->text('description')->nullable(); // Description de la filière
            $table->boolean('is_active')->default(true); // Statut actif/inactif
            $table->string('parent_id')->nullable(); // Pour les sous-filières (options)
            $table->timestamps();
            $table->softDeletes(); // Pour la suppression logique
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_filieres');
    }
}

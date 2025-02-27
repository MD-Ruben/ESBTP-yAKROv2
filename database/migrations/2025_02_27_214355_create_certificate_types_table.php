<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('requires_approval')->default(true);
            $table->integer('validity_period')->nullable(); // en jours
            $table->timestamps();
            $table->softDeletes();
        });

        // Ajouter la colonne certificate_type_id à la table certificates
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('certificate_type_id')->after('student_id')->constrained('certificate_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['certificate_type_id']);
            $table->dropColumn('certificate_type_id');
        });
        
        Schema::dropIfExists('certificate_types');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Si la table examens n'existe pas, nous la créons
        if (!Schema::hasTable('exams')) {
            Schema::create('exams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('session_id')->constrained()->onDelete('cascade');
                // Nous allons ajouter la clé étrangère vers semesters séparément
                $table->unsignedBigInteger('semester_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Vérifions si la table semesters existe, et ajoutons la clé étrangère si c'est le cas
        if (Schema::hasTable('semesters')) {
            Schema::table('exams', function (Blueprint $table) {
                // Si la contrainte de clé étrangère existe déjà, nous la supprimons d'abord
                try {
                    $table->dropForeign(['semester_id']);
                } catch (\Exception $e) {
                    // La clé étrangère n'existe peut-être pas encore, donc nous continuons
                }

                // Ajout de la clé étrangère
                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
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
        // Nous ne faisons rien ici, car nous ne voulons pas supprimer la table
    }
}

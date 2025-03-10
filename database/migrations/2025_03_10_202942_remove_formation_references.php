<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFormationReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_classes', function (Blueprint $table) {
            // Vérifier si la clé étrangère existe avant de la supprimer
            $foreignKeys = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys('esbtp_classes');

            $formationForeignKeyExists = collect($foreignKeys)
                ->contains(function ($foreignKey) {
                    return $foreignKey->getName() === 'esbtp_classes_formation_id_foreign';
                });

            if ($formationForeignKeyExists) {
                $table->dropForeign(['formation_id']);
            }

            if (Schema::hasColumn('esbtp_classes', 'formation_id')) {
                $table->dropColumn('formation_id');
            }
        });

        Schema::dropIfExists('esbtp_formations');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('esbtp_formations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('esbtp_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('formation_id')->nullable();
            $table->foreign('formation_id')->references('id')->on('esbtp_formations');
        });
    }
}

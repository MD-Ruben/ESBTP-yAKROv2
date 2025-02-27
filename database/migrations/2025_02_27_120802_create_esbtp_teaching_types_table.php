<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEsbtpTeachingTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_teaching_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du type d'enseignement (CM, TD, TP)
            $table->string('code')->unique(); // Code unique du type d'enseignement
            $table->text('description')->nullable(); // Description du type d'enseignement
            $table->decimal('hourly_rate', 10, 2)->nullable(); // Taux horaire pour ce type d'enseignement
            $table->boolean('is_active')->default(true); // Statut actif/inactif
            $table->timestamps();
        });
        
        // Insertion des types d'enseignement par défaut
        DB::table('esbtp_teaching_types')->insert([
            [
                'name' => 'Cours Magistral',
                'code' => 'CM',
                'description' => 'Enseignement théorique dispensé en amphithéâtre',
                'hourly_rate' => 10000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Travaux Dirigés',
                'code' => 'TD',
                'description' => 'Enseignement en groupe pour appliquer les concepts théoriques',
                'hourly_rate' => 7500.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Travaux Pratiques',
                'code' => 'TP',
                'description' => 'Enseignement pratique en laboratoire ou atelier',
                'hourly_rate' => 5000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_teaching_types');
    }
}

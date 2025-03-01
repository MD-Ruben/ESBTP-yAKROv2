<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('esbtp_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nom');
            $table->string('prenoms');
            $table->enum('sexe', ['M', 'F']);
            $table->string('profession')->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone');
            $table->string('telephone_secondaire')->nullable();
            $table->string('email')->nullable();
            $table->string('type_piece_identite')->nullable()->comment('CNI, Passeport, etc.');
            $table->string('numero_piece_identite')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour la relation entre étudiants et parents
        Schema::create('esbtp_etudiant_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('esbtp_etudiants')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('esbtp_parents')->onDelete('cascade');
            $table->string('relation')->comment('père, mère, tuteur, etc.');
            $table->boolean('is_tuteur')->default(false);
            $table->timestamps();

            $table->unique(['etudiant_id', 'parent_id', 'relation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esbtp_etudiant_parent');
        Schema::dropIfExists('esbtp_parents');
    }
}; 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('formations')) {
            Schema::create('formations', function (Blueprint $table) {
                $table->id();
                $table->string('code');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('level')->nullable()->comment('Licence, Master, Doctorat, etc.');
                $table->integer('duration')->nullable()->comment('Durée en années');
                $table->foreignId('ufr_id')->nullable()->comment('UFR à laquelle appartient la formation')
                      ->constrained()->nullOnDelete();
                $table->foreignId('coordinator_id')->nullable()->comment('Coordinateur de la formation')
                      ->constrained('users')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()
                      ->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()
                      ->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('formations');
    }
}

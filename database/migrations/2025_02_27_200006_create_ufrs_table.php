<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUfrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ufrs')) {
            Schema::create('ufrs', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('director_id')->nullable()->comment('Directeur de l\'UFR')
                      ->constrained('users')->nullOnDelete();
                $table->string('location')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('website')->nullable();
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
        Schema::dropIfExists('ufrs');
    }
}

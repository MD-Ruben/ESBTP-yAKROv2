<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsbtpDepartmentPartnershipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esbtp_department_partnership', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id', 'dept_partner_dept_id_foreign')
                ->references('id')
                ->on('esbtp_departments')
                ->onDelete('cascade');
                
            $table->unsignedBigInteger('partnership_id');
            $table->foreign('partnership_id', 'dept_partner_partner_id_foreign')
                ->references('id')
                ->on('esbtp_partnerships')
                ->onDelete('cascade');
                
            $table->text('specific_details')->nullable(); // Détails spécifiques à cette relation
            $table->date('start_date')->nullable(); // Date de début spécifique à cette relation
            $table->date('end_date')->nullable(); // Date de fin spécifique à cette relation
            $table->timestamps();

            // Assurer l'unicité de la relation
            $table->unique(['department_id', 'partnership_id'], 'dept_partner_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('esbtp_department_partnership');
    }
}

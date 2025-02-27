<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            // Add the deleted_at column for soft deletes
            // This column allows us to "hide" records instead of permanently deleting them
            // Think of it like moving a file to the recycle bin instead of permanently deleting it
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            // Remove the deleted_at column
            $table->dropSoftDeletes();
        });
    }
}

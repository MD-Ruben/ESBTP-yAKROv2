<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalColumnsToPermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('category')->nullable()->after('guard_name')->comment('Catégorie de la permission');
            $table->text('description')->nullable()->after('category')->comment('Description de la permission');
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->text('description')->nullable()->after('guard_name')->comment('Description du rôle');
            $table->boolean('is_default')->default(false)->after('description')->comment('Indique si c\'est un rôle par défaut');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropColumn(['category', 'description']);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn(['description', 'is_default']);
        });
    }
}

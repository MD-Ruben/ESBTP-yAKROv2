<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MarkAddRoleToUsersTableAsCompleted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Marquer la migration problématique comme complétée
        DB::table('migrations')->insert([
            'migration' => '2025_02_26_195039_add_role_to_users_table',
            'batch' => 3,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer l'entrée de la migration
        DB::table('migrations')
            ->where('migration', '2025_02_26_195039_add_role_to_users_table')
            ->delete();
    }
}

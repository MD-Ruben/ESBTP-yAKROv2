<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigMatieresToBulletinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esbtp_bulletins', function (Blueprint $table) {
            if (!Schema::hasColumn('esbtp_bulletins', 'config_matieres')) {
                $table->json('config_matieres')->nullable()->after('appreciation_generale');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esbtp_bulletins', function (Blueprint $table) {
            if (Schema::hasColumn('esbtp_bulletins', 'config_matieres')) {
                $table->dropColumn('config_matieres');
            }
        });
    }
}

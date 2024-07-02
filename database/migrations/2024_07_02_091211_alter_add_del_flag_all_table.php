<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterAddDelFlagAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE');
        foreach($tables as $table) {
            Schema::table($table->{'Tables_in_' . $dbName}, function (Blueprint $table) {
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
        $tables = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE');
        foreach($tables as $table) {
            Schema::table($table->{'Tables_in_' . $dbName}, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
}

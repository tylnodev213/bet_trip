<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPeopleBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->mediumInteger('number_adults');
            $table->mediumInteger('number_children')->default(0);
            $table->dropColumn('people');
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('number_adults');
            $table->dropColumn('number_children');
            $table->mediumInteger('people');
            $table->mediumInteger('price');
        });
    }
}

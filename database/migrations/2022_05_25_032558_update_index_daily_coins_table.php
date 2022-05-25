<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIndexDailyCoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_coins', function (Blueprint $table) {
            $table->unique(['date', 'coin'], 'unique_date_coin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_coins', function (Blueprint $table) {
            $table->dropUnique('unique_date_coin');
        });
    }
}

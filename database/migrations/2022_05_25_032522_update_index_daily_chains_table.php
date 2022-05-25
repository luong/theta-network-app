<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIndexDailyChainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_chains', function (Blueprint $table) {
            $table->unique(['date', 'chain'], 'unique_date_chain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_chains', function (Blueprint $table) {
            $table->dropUnique('unique_date_chain');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyCoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_coins', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('coin');
            $table->double('price')->nullable();
            $table->double('market_cap')->nullable();
            $table->double('volume_24h')->nullable();
            $table->bigInteger('supply')->nullable();
            $table->integer('total_stakes')->nullable();
            $table->integer('staked_nodes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_coins');
    }
}

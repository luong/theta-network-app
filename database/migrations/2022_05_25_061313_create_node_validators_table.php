<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodeValidatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_validators', function (Blueprint $table) {
            $table->id();
            $table->string('holder', 255);
            $table->string('name', 100);
            $table->string('chain', 50);
            $table->double('amount');
            $table->string('coin', 50);
            $table->timestamps();
            $table->unique(['holder', 'chain'], 'unique_holder_chain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_validators');
    }
}

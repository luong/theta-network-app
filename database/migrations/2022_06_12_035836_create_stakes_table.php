<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stakes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->unique();
            $table->string('holder', 150);
            $table->string('source', 150);
            $table->string('type', 10);
            $table->double('coins');
            $table->string('currency', 10);
            $table->double('usd');
            $table->string('return_height', 100);
            $table->boolean('withdrawn');
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
        Schema::dropIfExists('stakes');
    }
};

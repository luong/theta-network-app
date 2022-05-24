<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDailyChainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_chains', function (Blueprint $table) {
            $table->integer('validators')->nullable()->after('active_wallets');
            $table->json('metadata')->nullable()->after('validators');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropColumns('daily_chains', ['validators', 'metadata']);
    }
}

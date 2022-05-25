<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Holder;

class CreateHoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255);
            $table->string('name', 100);
            $table->string('chain', 50);
            $table->json('assets')->nullable();
            $table->timestamps();
        });

        Holder::insert([
            ['code' => '0x80eab22e27d4b94511f5906484369b868d6552d2', 'name' => 'Binance', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xa61abd72cdc50d17a3cbdceb57d3d5e4d8839bce', 'name' => 'ThetaLabs', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x4b80a68a8469d33449eb101082e5500b932a23ce', 'name' => 'ThetaLabs', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xe2408dff7a1f9bc247c803e43efa2f0a37b10ba6', 'name' => 'ThetaLabs', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xa144e6a98b967e585b214bfa7f6692af81987e5b', 'name' => 'ThetaLabs', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x15cc4c3f21417c392119054c8fe5895146e1a493', 'name' => 'ThetaLabs', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xafcc901b0e8eac02f0e91bd12791888a0df8a252', 'name' => 'ThetaLabs', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x3905663153b7f2ba8a21f020f87df6fcf13580c5', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xaab4faa8dbd835854e2e724a753b1c4d4020475a', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x53dee6603cd4a1dd549b3d46116a239138945ab0', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xc4e68435b0f12c6664377a961e7c459f414b6ef1', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x7f0f88a29daa41e988aad71668e84d575af8ad28', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xcbcef62ca7a2e367a9c93aba07ea4e63139da99d', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x6872b883464bfba456ab674bbef9824849db91e2', 'name' => 'CAA', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x578f5ddd2221a94f095bc7c81ddf95ee9e0cb58f', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x1786d878cb76a53f5950f41fed7d61617e12dfb5', 'name' => 'DHVC', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x050bb1210802cf5c624a4b3f501f1c12f68dcc05', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x3dd37990b722249f81375c3298eabe491d44944d', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x73668d14e3b69ac9c986d5de2bd96c00377610a1', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x08f927f6212f842ce5af107f2ab5e6efac729de6', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xe4c05fab358c4d253cb519997854a7c2d9384b01', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0xfae4efad7fcc8e3d76dc53ee92c91d88fb7388aa', 'name' => 'BridgeTower', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x66f8aa626b5ccf5d7bee2ad6435a11cf22bed789', 'name' => '*', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['code' => '0x099e156352ab4de87a20801288edb7d753770db8', 'name' => 'Sierra', 'chain' => 'theta', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holders');
    }
}

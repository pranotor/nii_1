<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateBpbd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_bpbd', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('bpb_id');
            $table->foreignId('item_id');
            $table->string('kode_perk')->length(20);
            $table->char('kode_barang')->length(5);
            $table->decimal('qty_pesan', 18, 2)->default('0');
            $table->decimal('qty_terima', 18, 2)->default('0');
            $table->decimal('harga', 18, 2)->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_bpbd');
    }
}

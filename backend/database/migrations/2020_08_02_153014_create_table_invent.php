<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_invent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id');
            $table->string('kode_barang')->length(30);
            $table->date('tgl');
            $table->enum('status',['IN','OUT']);
            $table->string('ref')->length(30);
            $table->decimal('qty', 12, 2);
            $table->decimal('harga', 12, 2);
            $table->decimal('used', 12, 2)->default(0);
            $table->string('ref_harga')->length(30)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_invent');
    }
}

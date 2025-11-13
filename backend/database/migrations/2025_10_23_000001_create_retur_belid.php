<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturBelid extends Migration
{
    public function up(): void
    {
        Schema::create('t_retur_belid', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ret_id');
            $table->unsignedInteger('item_id');
            $table->string('kode_perk', 20);
            $table->char('kode_barang', 5);
            $table->string('kode2', 50)->nullable();
            $table->string('nama_barang2', 100)->nullable();
            $table->string('size', 50)->nullable();
            $table->decimal('qty_retur', 18, 2)->default(0);
            $table->decimal('harga', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->string('satuan', 20)->nullable();
            $table->unsignedInteger('bpbd_id')->nullable();
            $table->index('ret_id');
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_retur_belid');
    }
}

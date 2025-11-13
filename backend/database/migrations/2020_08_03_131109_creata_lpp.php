<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreataLpp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('json_lpp', function (Blueprint $table) {
            $table->id();
            $table->string('JuruBayar')->length(50);
            $table->enum('D_K',['D','K']);
            $table->decimal('Jumlah',12,2);
            $table->string('Tanggal')->length(10);
            $table->string('Tgl_setor')->length(10);
            $table->string('Rekening')->length(30);
            $table->string('ref_jurnal')->length(30)->default('-');
            $table->timestamps();

            $table->index('Tanggal');
            $table->index('JuruBayar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('json_lpp');
    }
}

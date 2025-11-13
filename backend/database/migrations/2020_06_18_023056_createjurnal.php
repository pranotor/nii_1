<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Createjurnal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->increments('id');
            $table->date('tanggal');
            $table->string('referensi')->length(30);
            $table->mediumText('uraian')->nullable();
            $table->string('kode')->length(30);
            $table->decimal('debet', 18, 2)->default('0');
            $table->decimal('kredit', 18, 2)->default('0');
            $table->string('opr')->length(20)->default('-');
            $table->integer('jenis')->length(1);
            $table->string('rekanan')->length(20)->default('-');
            $table->string('unit')->length(20)->default('-');
            $table->string('voucher')->length(30)->default('-');
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
        Schema::dropIfExists('jurnal');
    }
}

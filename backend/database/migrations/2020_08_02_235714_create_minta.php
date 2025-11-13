<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_minta', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bpp_no')->length(30);
            $table->date('tanggal');
            $table->foreignId('guna_id');
            $table->mediumText('uraian')->nullable();
            $table->string('pengelola')->length(20)->default('-');
            $table->string('dikirim')->length(20)->default('-');
            $table->string('pemohon')->length(20)->default('-');
            $table->string('mengeluarkan')->length(20)->default('-');
            $table->tinyInteger('posting')->default(0);
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
        Schema::dropIfExists('t_minta');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTtdDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_ttd_detail', function (Blueprint $table) {
            $table->id();
            $table->string('fungsi')->length('100');
            $table->string('jabatan')->length('100');
            $table->string('nama')->length('100');
            $table->string('nip')->length('30');
            $table->tinyInteger('ordinal')->default('0');
            $table->foreignId('ttd_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_ttd_detail');
    }
}

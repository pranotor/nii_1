<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_voucher', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_vcr')->length(30);
            $table->date('tgl_vcr');
            $table->string('rekanan')->length(20)->default('-');
            $table->mediumText('uraian')->nullable();
            $table->date('tgl_cheq')->nullable();
            $table->string('no_cheq')->length(20)->default('-');
            $table->decimal('k_usaha', 18, 2)->default('0');
            $table->decimal('k_nonUsaha', 18, 2)->default('0');
            $table->decimal('k_pajak', 18, 2)->default('0');
            $table->mediumText('k_nama_rupa')->nullable();
            $table->mediumText('k_kode_rupa')->nullable();
            $table->decimal('k_nom_rupa', 18, 2)->default('0');
            $table->smallInteger('bayar', 1)->default('0');
            $table->string('opr1')->length(20)->default('-');
            $table->string('opr2')->length(20)->default('-');
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
        Schema::dropIfExists('t_voucher');
    }
}

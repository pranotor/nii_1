<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaran extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_voucher_bayar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_vcr')->length(30);
            $table->string('ref_jurnal')->length(30);
            $table->date('tgl_bayar');
            $table->date('tgl_cheq')->nullable();
            $table->string('no_cheq')->length(20)->default('-');
            $table->decimal('d_usaha', 18, 2)->default('0');
            $table->decimal('d_nonUsaha', 18, 2)->default('0');
            $table->decimal('d_pajak', 18, 2)->default('0');
            $table->string('opr')->length(20)->default('-');
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
        Schema::dropIfExists('t_voucher_bayar');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturBeli extends Migration
{
    public function up(): void
    {
        Schema::create('t_retur_beli', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ret_no', 50)->unique();
            $table->date('ret_tgl');
            $table->unsignedInteger('supp_id')->nullable();
            $table->string('bpb_no', 50)->nullable();
            $table->string('po_no', 50)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_retur_beli');
    }
}

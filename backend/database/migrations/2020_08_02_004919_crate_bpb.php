<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateBpb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_bpb', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_no')->length(30);
            $table->string('sj_no')->length(30);
            $table->string('bpb_no')->length(30);
            $table->date('bpb_tgl');
            $table->string('rekanan')->length(20)->default('-');
            $table->date('faktur')->nullable();
            $table->string('opr')->length(20)->default('-');
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
        Schema::dropIfExists('t_bpb');
    }
}

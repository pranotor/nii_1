<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTtd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_ttd', function (Blueprint $table) {
            $table->id();
            $table->string('mod_name')->length('100');
            $table->string('jenis')->length('20');
            $table->string('condition')->nullable();
            $table->timestamps();
            $table->index('mod_name');
            $table->index('condition');
            $table->index('jenis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_ttd');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnBpb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_bpb', function (Blueprint $table) {
            $table->string('voucher')->length(30)->default('-')->after('posting');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_bpb', function (Blueprint $table) {
            $table->dropColumn('voucher');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterJurnal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jurnal', function (Blueprint $table) {
            $table->string('document')->length(30)->default('-')->after('voucher');
            $table->index('referensi');
            $table->index('tanggal');
            $table->index('jenis');
            $table->index('voucher');
            $table->index('kode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jurnal', function (Blueprint $table) {
            $table->dropColumn('document');
            $table->dropIndex(['referensi']);
            $table->dropIndex('tanggal');
            $table->dropIndex('jenis');
            $table->dropIndex('voucher');
            $table->dropIndex('kode');
        });
    }
}

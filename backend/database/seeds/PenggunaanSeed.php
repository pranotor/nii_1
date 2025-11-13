<?php

use Illuminate\Database\Seeder;

class PenggunaanSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('t_penggunaan')->insert([
            'nama_penggunaan' => 'PEMASANGAN BARU'
        ]);
        DB::table('t_penggunaan')->insert([
            'nama_penggunaan' => 'BUKA KEMBALI'
        ]);
        DB::table('t_penggunaan')->insert([
            'nama_penggunaan' => 'PERBAIKAN'
        ]);
        DB::table('t_penggunaan')->insert([
            'nama_penggunaan' => 'PEMASANGAN PIPA TRANDIS'
        ]);
        DB::table('t_penggunaan')->insert([
            'nama_penggunaan' => 'BUKA KEMBALI / PASANG BARU'
        ]);
        DB::table('t_penggunaan')->insert([
            'nama_penggunaan' => 'LAINNYA'
        ]);
        DB::table('t_penggunaan')->insert([
            'nama_penggunaan' => 'KOREKSI'
        ]);
    }
}

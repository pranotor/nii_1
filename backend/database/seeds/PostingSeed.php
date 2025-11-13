<?php

use Illuminate\Database\Seeder;

class PostingSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('t_status_posting')->insert([
            'status' => 0,
        	'status_name' => 'BLM POSTING'
        ]);
        DB::table('t_status_posting')->insert([
            'status' => 1,
        	'status_name' => 'SUDAH POSTING'
        ]);
    }
}

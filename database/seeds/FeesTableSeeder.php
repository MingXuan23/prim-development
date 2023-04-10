<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fees')->delete();

        DB::table('fees')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "Yuran Kelas Bestari",
                "status" => "1",
                "totalamount" => 150,
                "yearfees_id" => 1,
            ),
        ));
    }
}

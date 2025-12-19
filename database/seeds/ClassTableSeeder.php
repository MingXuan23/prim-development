<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classes')->delete();

        DB::table('classes')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => " 1 Bestari",
                "levelid" => 1,
                "status"    => 1,
            ),
            1 =>
            array(
                "id" => 2,
                "nama" => "class 1",
                "levelid" => 2,
                "status"    => 1,

            ),
            array(
                "id" => 3,
                "nama" => "1 Cemerlang",
                "levelid" => 1,
                "status"    => 1,

            ),
        ));
    }
}

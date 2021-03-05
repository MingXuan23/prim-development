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
                "nama" => "Bestari",
                "levelid" => 1,
            ),
        ));
    }
}

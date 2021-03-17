<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('students')->delete();

        DB::table('students')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "Haqeem Solehan",
                "icno" => "100101-14-6137",
            ),
        ));
    }
}

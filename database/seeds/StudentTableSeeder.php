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
                "gender" => "L"
            ),
            1 =>
            array(
                "id" => 2,
                "nama" => "student 1",
                "icno" => "026946-14-6137",
                "gender" => "L"
            ),
            2 =>
            array(
                "id" => 3,
                "nama" => "student A from 1 Bestari",
                "icno" => "026946-15-6137",
                "gender" => "L"
            ),
            3 =>
            array(
                "id" => 4,
                "nama" => "student B from 1 Bestari",
                "icno" => "026946-14-6223",
                "gender" => "P"
            ),
        ));
    }
}

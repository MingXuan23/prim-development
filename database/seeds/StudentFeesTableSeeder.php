<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentFeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student_fees')->delete();

        DB::table('student_fees')->insert(array(
            0 =>
            array(
                "id" => 1,
                "status" => "Belum Dibayar",
                "class_student_id" => 1,
                "fees_details_id" => 1,
            ),
        ));
    }
}

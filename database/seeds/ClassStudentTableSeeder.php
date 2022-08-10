<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassStudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_student')->delete();

        DB::table('class_student')->insert(array(
            0 =>
            array(
                "id" => 1,
                "organclass_id" => 1,
                "student_id" => 1,
                "status" => 1,
            ),
        ));
    }
}

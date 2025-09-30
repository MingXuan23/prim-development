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
            //below is student 1 from class 1 from school 1
            1 =>
            array(
                "id" => 2,
                "organclass_id" => 2,
                "student_id" => 2,
                "status" => 1,
            ),
            //below is students from 1 Bestari
            2 =>
            array(
                "id" => 3,
                "organclass_id" => 1,
                "student_id" => 3,
                "status" => 1,
            ),
            3 =>
            array(
                "id" => 4,
                "organclass_id" => 1,
                "student_id" => 4,
                "status" => 0,
            ),
        ));
    }
}

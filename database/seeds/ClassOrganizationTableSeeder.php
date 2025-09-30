<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_organization')->delete();

        DB::table('class_organization')->insert(array(
            //1 bestari from dunnoe what school in charge by teacher 1
            0 =>
            array(
                "id" => 1,
                "class_id" => 1,
                "organization_id" => 2,
                "organ_user_id" => 7,
            ),
            //below is teacher 1 that in charge of class 1 from school 1
            1 =>
            array(
                "id" => 2,
                "class_id" => 2,
                "organ_user_id" => 7,
                "organization_id" => 4,

            ),
            2 =>
            array(
                "id" => 3,
                "class_id" => 3,
                "organ_user_id" => 7,
                "organization_id" => 2,

            ),
        ));
    }
}

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
            0 =>
            array(
                "id" => 1,
                "class_id" => 1,
                "organization_id" => 2,
            ),
        ));
    }
}

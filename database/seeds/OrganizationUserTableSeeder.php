<?php

use Illuminate\Database\Seeder;

class OrganizationUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('organization_user')->delete();

        \DB::table('organization_user')->insert(array(
            0 =>
            array(
                "id" => 1,
                "user_id" => 1,
                "organization_id" => 1,
                "role_id" => 2
            ),
            1 =>
            array(
                "id" => 2,
                "user_id" => 2,
                "organization_id" => 2,
                "role_id" => 4
            ),
        ));
    }
}

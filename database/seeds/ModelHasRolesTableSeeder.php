<?php

use Illuminate\Database\Seeder;

class ModelHasRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('model_has_roles')->delete();

        \DB::table('model_has_roles')->insert(array(
            0 =>
            array(
                "role_id"    => 1,
                "model_type" => "App\User",
                "model_id"   => 3,
            ),
        ));
    }
}

<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array(
            0 =>
            array(
                "id" => 4,
                "name" => "Pentadbir",
                "guard_name" => "web"
            ),
            1 =>
            array(
                "id" => 5,
                "name" => "Guru",
                "guard_name" => "web"
            ),
            2 =>
            array(
                "id" => 6,
                "name" => "Penjaga",
                "guard_name" => "web"
            ),
            3 =>
            array(
                "id" => 1,
                "name" => "Superadmin",
                "guard_name" => "web"
            ),
            4 =>
            array(
                "id" => 2,
                "name" => "Admin",
                "guard_name" => "web"
            ),
            5 =>
            array(
                "id" => 3,
                "name" => "Jaim",
                "guard_name" => "web"
            ),
            6 =>
            array(
                "id" => 7,
                "name" => "Admin Polimas",
                "guard_name" => "web"
            ),
            7 =>
            array(
                "id" => 8,
                "name" => "Warden",
                "guard_name" => "web"
            ),
            8 =>
            array(
                "id" => 9,
                "name" => "Guard",
                "guard_name" => "web"
            ),
        ));
    }
}

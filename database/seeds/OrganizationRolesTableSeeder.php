<?php

use Illuminate\Database\Seeder;

use function Ramsey\Uuid\v1;

class OrganizationRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('organization_roles')->delete();

        \DB::table('organization_roles')->insert(array(
            0 =>
            array(
                "id" => 4,
                "nama" => "Pentadbir",
            ),
            1 =>
            array(
                "id" => 5,
                "nama" => "Guru",
            ),
            2 =>
            array(
                "id" => 6,
                "nama" => "Ibu",
            ),
            3 =>
            array(
                "id" => 7,
                "nama" => "Bapa",
            ),
            4 =>
            array(
                "id" => 8,
                "nama" => "Penjaga",
            ),
            5 =>
            array(
                "id" => 1,
                "nama" => "Superadmin",
            ),
            6 =>
            array(
                "id" => 2,
                "nama" => "Admin",
            ),
            7 =>
            array(
                "id" => 3,
                "nama" => "Jaim",
            ),
        ));
    }
}

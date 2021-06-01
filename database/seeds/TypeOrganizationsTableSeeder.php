<?php

use Illuminate\Database\Seeder;

class TypeOrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('type_organizations')->delete();
        \DB::table('type_organizations')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "SK/SJK",
            ),
            1 =>
            array(
                "id" => 2,
                "nama" => "SRA/SRAI",
            ),
            2 =>
            array(
                "id" => 3,
                "nama" => "Masjid",
            ),
            3 =>
            array(
                "id" => 4,
                "nama" => "NGO",
            ),
        ));
    }
}

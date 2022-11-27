<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeOrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type_organizations')->delete();
        DB::table('type_organizations')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "SK /SJK",
            ),
            1 =>
            array(
                "id" => 2,
                "nama" => "SRA /SRAI",
            ),
            2 =>
            array(
                "id" => 3,
                "nama" => "SMK /SMJK",
            ),
            3 =>
            array(
                "id" => 4,
                "nama" => "Masjid",
            ),
            4 =>
            array(
                "id" => 5,
                "nama" => "NGO",
            ),
            5 =>
            array(
                "id" => 6,
                "nama" => "Rumah Anak Yatim",
            ),
            6 =>
            array(
                "id" => 7,
                "nama" => "Pusat Tahfiz",
            ),
            7 =>
            array(
                "id" => 8,
                "nama" => "Kedai Makanan",
            ),
        ));
    }
}

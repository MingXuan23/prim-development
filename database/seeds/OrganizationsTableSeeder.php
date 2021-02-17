<?php

use Illuminate\Database\Seeder;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('organizations')->delete();

        \DB::table('organizations')->insert(array(
            0 =>
            array(
                "id" => 1,
                "code" => "MS001",
                "email" => "admin_masjid@gmail.com",
                "nama" => "Masjid Al-Alami",
                "telno" => "01139893143",
                "address" => "UTeM, Ayer Keroh",
                "postcode" => "34400",
                "state" => "Melaka",
                "type_org" => "public",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
            1 =>
            array(
                "id" => 2,
                "code" => "SK001",
                "email" => "admin_sekolah@gmail.com",
                "nama" => "SRA Al-Ridhwaniah",
                "telno" => "01139893143",
                "address" => "UTeM, Ayer Keroh",
                "postcode" => "34400",
                "state" => "Melaka",
                "type_org" => "JAIM",
                "created_at" => "2020-06-07 10:48:33",
                "updated_at" => "2020-06-07 10:52:01"
            ),
        ));
    }
}

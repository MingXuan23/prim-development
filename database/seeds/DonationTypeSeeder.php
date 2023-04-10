<?php

use Illuminate\Database\Seeder;

class DonationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('donation_type')->delete();
        \DB::table('donation_type')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "Kebajikan Pelajar",
            ),
            1 =>
            array(
                "id" => 2,
                "nama" => "FoodBank",
            ),
            2 =>
            array(
                "id" => 3,
                "nama" => "Aktiviti STEM",
            ),
            3 =>
            array(
                "id" => 4,
                "nama" => "Tahfiz",
            ),
            4 =>
            array(
                "id" => 5,
                "nama" => "Masjid",
            ),
            5 =>
            array(
                "id" => 6,
                "nama" => "Rumah Ibadat",
            ),
            6 =>
            array(
                "id" => 7,
                "nama" => "NGOs",
            ),
            7 =>
            array(
                "id" => 8,
                "nama" => "Lain-lain",
            ),
        ));
    }
}

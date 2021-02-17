<?php

use Illuminate\Database\Seeder;

class DonationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $current_date = Carbon\Carbon::now();
        $end_date = $current_date->addDays(30);

        \DB::table('donations')->delete();

        \DB::table('donations')->insert(array(
            0 =>
            array(
                "id"            => 1,
                "nama"          => "Derma Kilat Pembinaan Tandas",
                "description"   => "Ayuh Derma",
                "date_started"  => $current_date,
                "date_end"      => $end_date,
                "date_created"  => $current_date,
                "status"        => 1,
            ),
            1 =>
            array(
                "id"            => 2,
                "nama"          => "Derma Kilat Pemasangan Kipas",
                "description"   => "Ayuh Derma",
                "date_started"  => $current_date,
                "date_end"      => $end_date,
                "date_created"  => $current_date,
                "status"        => 1,
            ),
            2 =>
            array(
                "id"            => 3,
                "nama"          => "Derma Kilat Pemasangan Aircond",
                "description"   => "Ayuh Derma",
                "date_started"  => $current_date,
                "date_end"      => $end_date,
                "date_created"  => $current_date,
                "status"        => 1,
            ),
            3 =>
            array(
                "id"            => 4,
                "nama"          => "Derma Kilat Pemasangan Paip",
                "description"   => "Ayuh Derma",
                "date_started"  => $current_date,
                "date_end"      => $end_date,
                "date_created"  => $current_date,
                "status"        => 1,
            ),
        ));
    }
}

<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Year_FeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('year_fees')->delete();

        DB::table('year_fees')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "2021",
            ),
        ));
    }
}

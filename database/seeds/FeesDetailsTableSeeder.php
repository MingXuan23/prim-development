<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeesDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fees_details')->delete();

        DB::table('fees_details')->insert(array(
            0 =>
            array(
                "id" => 1,
                "status" => "Yuran",
                "details_id" => 1,
                "fees_id" => 1,
            ),
        ));
    }
}

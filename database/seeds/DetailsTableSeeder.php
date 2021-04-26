<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('details')->delete();

        DB::table('details')->insert(array(
            0 =>
            array(
                "id" => 1,
                "nama" => "Yuran",
                "price" => 150,
                "quantity" => 1,
                "totalamount" => 150,
                "category_id" => 1,
            ),
        ));
    }
}

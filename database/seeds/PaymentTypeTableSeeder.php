<?php

use Illuminate\Database\Seeder;

class PaymentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('payment_type')->delete();

        \DB::table('payment_type')->insert(array(
            0 =>
            array(
                "id"    => 1,
                "nama" => "FPX",
            ),
            1 =>
            array(
                "id"    => 2,
                "nama" => "Credit Card",
            ),
            2 =>
            array(
                "id"    => 3,
                "nama" => "Debit Card",
            ),
        ));
    }
}

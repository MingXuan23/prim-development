<?php

use Illuminate\Database\Seeder;

class DonationTransactionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('donation_transaction')->delete();

        DB::table('donation_transaction')->insert(array(
            0 =>
            array(
                "id" => 1,
                "donation_id" => 1,
                "transaction_id" => 1,
                "payment_type_id" => 1
            ),
            1 =>
            array(
                "id" => 2,
                "donation_id" => 1,
                "transaction_id" => 2,
                "payment_type_id" => 1
            ),
            2 =>
            array(
                "id" => 3,
                "donation_id" => 1,
                "transaction_id" => 3,
                "payment_type_id" => 1
            ),
            3 =>
            array(
                "id" => 4,
                "donation_id" => 1,
                "transaction_id" => 4,
                "payment_type_id" => 1
            ),
            4 =>
            array(
                "id" => 5,
                "donation_id" => 1,
                "transaction_id" => 5,
                "payment_type_id" => 1
            ),
            5 =>
            array(
                "id" => 6,
                "donation_id" => 2,
                "transaction_id" => 6,
                "payment_type_id" => 1
            ),
        ));
    }
}

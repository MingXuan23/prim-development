<?php

use Illuminate\Database\Seeder;

class DonationOrganizationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('donation_organization')->delete();

        \DB::table('donation_organization')->insert(array(
            0 =>
            array(
                "id" => 1,
                "donation_id" => 1,
                "organization_id" => 1
            ),
            1 =>
            array(
                "id" => 2,
                "donation_id" => 2,
                "organization_id" => 1,
            ),
            2 =>
            array(
                "id" => 3,
                "donation_id" => 3,
                "organization_id" => 2,
            ),
            3 =>
            array(
                "id" => 4,
                "donation_id" => 4,
                "organization_id" => 2,
            ),
        ));
    }
}

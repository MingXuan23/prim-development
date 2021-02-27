<?php

use Illuminate\Database\Seeder;

class UserReminderDonationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('user_donation_reminder')->delete();

        \DB::table('user_donation_reminder')->insert(array(
            0 =>
            array(
                'user_id' => 1,
                'donation_id' => 1,
                'reminder_id' => 1,
            ),
            1 =>
            array(
                'user_id' => 1,
                'donation_id' => 2,
                'reminder_id' => 2,
            ),
            2 =>
            array(
                'user_id' => 1,
                'donation_id' => 3,
                'reminder_id' => 3,
            ),
        ));
    }
}

<?php

use Illuminate\Database\Seeder;

class RemindersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('reminders')->delete();

        \DB::table('reminders')->insert(array(
            0 =>
            array(
                'id' => 1,
                'date' => 24,
                'time' => '8:00',
                'day' => '',
                'recurrence' => 'monthly',
                'donation_id' => 1,
                'user_id' => 1
            ),
            1 =>
            array(
                'id' => 2,
                'date' => '',
                'time' => '8:00',
                'day' => '',
                'recurrence' => 'daily',
                'donation_id' => 2,
                'user_id' => 1
            ),
            2 =>
            array(
                'id' => 3,
                'date' => '',
                'time' => '8:00',
                'day' => 1,
                'recurrence' => 'weekly',
                'donation_id' => 3,
                'user_id' => 1
            ),
        ));
    }
}
